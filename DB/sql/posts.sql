
CREATE VIEW view_post_with_tags AS
    SELECT p.*,
        string_agg(CASE WHEN ph.ck_seq_id=1 THEN m.filename END,'') AS filename,
        string_agg(CASE WHEN ph.ck_seq_id=1 THEN mv.filename END,'') AS filename_vid,
        string_agg(CASE WHEN ph.ck_seq_id=1 THEN h.tag END,'') AS tag1,
        string_agg(CASE WHEN ph.ck_seq_id=2 THEN h.tag END,'') AS tag2,
        string_agg(CASE WHEN ph.ck_seq_id=3 THEN h.tag END,'') AS tag3,
        SUM(CASE WHEN ph.ck_seq_id=1 THEN ph.pop END) AS pop1,
        SUM(CASE WHEN ph.ck_seq_id=2 THEN ph.pop END) AS pop2,
        SUM(CASE WHEN ph.ck_seq_id=3 THEN ph.pop END) AS pop3,
        MAX(CASE WHEN ph.ck_seq_id=1 THEN ph.fk_htag_id END) AS id1,
        MAX(CASE WHEN ph.ck_seq_id=2 THEN ph.fk_htag_id END) AS id2,
        MAX(CASE WHEN ph.ck_seq_id=3 THEN ph.fk_htag_id END) AS id3
        FROM posts p
            LEFT JOIN posts_htags ph ON ph.fk_post_id = p.pk_post_id
            LEFT JOIN htags h ON h.pk_htag_id = ph.fk_htag_id
            LEFT JOIN medias m ON m.pk_media_id = fk_media_id
            LEFT JOIN medias mv ON mv.pk_media_id = fk_media_vid_id
        GROUP BY p.pk_post_id;

-- c'est ça qu'il faut utiliser quand on affiche un post
-- c'est la vue la plus complète
CREATE VIEW view_decorated_posts AS
    SELECT p.*,
        u1.firstname AS from_firstname,
        u1.lastname AS from_lastname,
        m1.filename AS from_filename_profile,
        u2.firstname AS to_firstname,
        u2.lastname AS to_lastname,
        m2.filename AS to_filename_profile
        ,c.locality AS city
    FROM view_post_with_tags p
        JOIN users u1 ON u1.pk_user_id = p.from_fk_user_id
            LEFT JOIN medias m1 ON m1.pk_media_id = u1.fk_media_id_profile
        JOIN users u2 ON u2.pk_user_id = p.to_fk_user_id
            LEFT JOIN medias m2 ON m2.pk_media_id = u2.fk_media_id_profile
        LEFT JOIN devcities c ON c.pk_devcity_id = p.fk_devcity_id
    ;

CREATE FUNCTION post_get(
    _pk_post_id BIGINT,
    _pk_user_id INT
)
RETURNS SETOF view_decorated_posts
AS $$
	SELECT *
	    FROM view_decorated_posts v
	    WHERE
	        pk_post_id = _pk_post_id
	        AND
	        EXISTS (
                -- soit le post appartient à un user dont le profil est public
                SELECT 1 FROM users
                    WHERE NOT private AND pk_user_id = v.to_fk_user_id
                UNION ALL
                -- soit on est ami avec le from ou le to du post
                SELECT 1 FROM friendships
                    WHERE fk_user_id1 = v.to_fk_user_id
                    AND fk_user_id2 = v.to_fk_user_id
	       );
$$ LANGUAGE sql STABLE;

CREATE FUNCTION posts_can_i_see_user_posts(
    _pk_viewer_user_id INT,
    _pk_user_id INT
)
RETURNS boolean
AS $$
	SELECT EXISTS (
        -- soit le profil est public
        SELECT 1 FROM users u
            WHERE NOT u.private AND u.pk_user_id = _pk_user_id
        UNION ALL
        -- soit on est ami
        SELECT 1 FROM friendships f
            WHERE f.fk_user_id1 = _pk_user_id
            AND f.fk_user_id2 = _pk_viewer_user_id
    );
$$ LANGUAGE sql STABLE;

CREATE FUNCTION post_can_i_share(
    _pk_post_id BIGINT,
    _pk_user_id INT
)
RETURNS BOOLEAN
AS $$
	SELECT EXISTS (
	   -- mon compte est public
	   -- je suis le destinataire du post
	   SELECT 1 FROM posts p
	       JOIN users u ON u.pk_user_id = _pk_user_id AND NOT u.private
	       WHERE p.pk_post_id = _pk_post_id
	         AND p.to_fk_user_id = _pk_user_id
	);
$$ LANGUAGE sql STABLE;


CREATE FUNCTION posts_by_tag_down_get(
    _pk_viewer_user_id INT,
    _pk_user_id INT,
    _pk_tag_id BIGINT,
    _before_ts TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
)
RETURNS SETOF view_decorated_posts
AS $$
DECLARE
    can_view_ BOOLEAN;
BEGIN
	SELECT posts_can_i_see_user_posts(_pk_viewer_user_id, _pk_user_id) INTO can_view_;
	IF can_view_ THEN
	   RETURN QUERY SELECT DISTINCT v.* FROM view_decorated_posts v
	       JOIN posts_htags ph ON ph.fk_post_id = v.pk_post_id AND ph.fk_htag_id = _pk_tag_id
	       WHERE v.to_fk_user_id = _pk_user_id
	           AND v.creation_ts < _before_ts
	       ORDER BY v.creation_ts DESC
	       LIMIT 5;
	END IF;
	RETURN;
END
$$ LANGUAGE plpgsql STABLE;


CREATE FUNCTION posts_by_tag_up_get(
    _pk_viewer_user_id INT,
    _pk_user_id INT,
    _pk_tag_id BIGINT,
    _after_ts TIMESTAMP WITHOUT TIME ZONE
)
RETURNS SETOF view_decorated_posts
AS $$
DECLARE
    can_view_ BOOLEAN;
BEGIN
    SELECT posts_can_i_see_user_posts(_pk_viewer_user_id, _pk_user_id) INTO can_view_;
    IF can_view_ THEN
        RETURN QUERY WITH p AS (
            SELECT DISTINCT v.* FROM view_decorated_posts v
                JOIN posts_htags ph ON ph.fk_post_id = v.pk_post_id AND ph.fk_htag_id = _pk_tag_id
                WHERE v.to_fk_user_id = _pk_user_id
                    AND v.creation_ts > _after_ts
                ORDER BY v.creation_ts ASC
                LIMIT 5
            )
            SELECT * FROM p ORDER BY creation_ts DESC;
    END IF;
    RETURN;
END
$$ LANGUAGE plpgsql STABLE;


--  POSTS WITH MEDIA
CREATE FUNCTION posts_with_media_down_get(
    _pk_viewer_user_id INT,
    _pk_user_id INT,
    _before_ts TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
)
RETURNS TABLE(
    fk_post_id BIGINT,
    creation_ts TIMESTAMP WITHOUT TIME ZONE,
    filename CHARACTER VARYING
)
AS $$
DECLARE
    can_view_ BOOLEAN;
BEGIN
	-- RAISE WARNING 'posts_with_media_down_get % % %', _pk_viewer_user_id, _pk_user_id, _before_ts;
    SELECT posts_can_i_see_user_posts(_pk_viewer_user_id, _pk_user_id) INTO can_view_;
    IF can_view_ THEN
        RETURN QUERY SELECT p.pk_post_id AS fk_post_id, p.creation_ts, m.filename
        FROM posts p
            JOIN users u ON p.to_fk_user_id = u.pk_user_id
            JOIN medias m ON p.fk_media_id = m.pk_media_id
            WHERE
                p.to_fk_user_id = _pk_user_id
	            AND p.creation_ts < _before_ts
            ORDER BY p.creation_ts DESC
            LIMIT 15;
    END IF;
    RETURN;
END
$$ LANGUAGE plpgsql STABLE;


CREATE FUNCTION posts_with_media_up_get(
    _pk_viewer_user_id INT,
    _pk_user_id INT,
    _after_ts TIMESTAMP WITHOUT TIME ZONE
)
RETURNS TABLE(
    fk_post_id BIGINT,
    creation_ts TIMESTAMP WITHOUT TIME ZONE,
    filename CHARACTER VARYING
)
AS $$
DECLARE
    can_view_ BOOLEAN;
BEGIN
    SELECT posts_can_i_see_user_posts(_pk_viewer_user_id, _pk_user_id) INTO can_view_;
    IF can_view_ THEN
        RETURN QUERY WITH p AS (
                SELECT p.pk_post_id AS fk_post_id, p.creation_ts, m.filename
                FROM posts p
                    JOIN users u ON p.to_fk_user_id = u.pk_user_id
                    JOIN medias m ON p.fk_media_id = m.pk_media_id
                    WHERE
                        p.to_fk_user_id = _pk_user_id
                        AND p.creation_ts > _after_ts
                    ORDER BY p.creation_ts ASC
                    LIMIT 15
            )
            SELECT * FROM p ORDER BY creation_ts DESC;
    END IF;
    RETURN;
END
$$ LANGUAGE plpgsql STABLE;



-- TODO REMOVE ME ? NOT USED: there is no "wall" page
CREATE FUNCTION posts_get(
    _pk_user_id INT,
    _pk_other_user_id INT,
    _start INT
)
RETURNS SETOF view_decorated_posts
AS $$
DECLARE
    friendship INT := 0;
BEGIN
    IF _pk_user_id = _pk_other_user_id THEN
        friendship = 1;
    ELSE
        SELECT 1 FROM friendships WHERE fk_user_id1 = _pk_user_id AND fk_user_id2 = _pk_other_user_id INTO friendship;
    END IF;
    IF friendship = 1 THEN
        -- RAISE NOTICE '% and % are friends', _pk_user_id, _pk_other_user_id;
        RETURN QUERY SELECT * FROM view_decorated_posts
            WHERE
                from_fk_user_id = _pk_other_user_id
                OR to_fk_user_id = _pk_other_user_id
            ORDER BY creation_ts DESC
            LIMIT 5 OFFSET _start;
    ELSE
        -- RAISE NOTICE '% and % are NOT friends', _pk_user_id, _pk_other_user_id;
        RETURN QUERY SELECT * FROM view_decorated_posts
            WHERE
                (from_fk_user_id = _pk_other_user_id
                OR to_fk_user_id = _pk_other_user_id)
                AND privacy = 'public'
            ORDER BY creation_ts DESC
            LIMIT 5 OFFSET _start;
    END IF;
END;
$$ LANGUAGE plpgsql STABLE;









CREATE FUNCTION posts_create(
    _body character varying,
    _from_user_id INT,
    _to_user_id INT,
    _tag1 character varying,
    _tag2 character varying,
    _tag3 character varying,
    _city_id INT,
    _lat REAL,
    _lon REAL
)
RETURNS event_and_data_type_t
AS $$
DECLARE
    post_id_ bigint;
    friendship_ INT := 0;
    res_ event_and_data_type_t;
BEGIN
    IF _from_user_id = _to_user_id
      OR _from_user_id = 1
    THEN
        friendship_ = 1;
    ELSE
        SELECT 1 FROM friendships WHERE fk_user_id1 = _from_user_id AND fk_user_id2 = _to_user_id INTO friendship_;
    END IF;
    -- RAISE NOTICE 'friendship %', friendship;
    IF friendship_ = 1 THEN
        INSERT INTO posts(creation_ts, body, from_fk_user_id, to_fk_user_id, fk_devcity_id, geo)
            VALUES(NOW(), _body, _from_user_id, _to_user_id, _city_id, ST_MakePoint(_lon, _lat))
            RETURNING pk_post_id INTO post_id_;
        PERFORM htags_post(post_id_, _tag1, _tag2, _tag3);
        --PERFORM posts_publish(_from_user_id, _to_user_id, post_id_);
        res_.event_type = 'post';
        res_.event_data = post_id_;
        res_.event_data2 = 0;
    ELSE
        res_.event_type = 'N/A';
        res_.event_data = 0;
        res_.event_data2 = 0;
    END IF;
    RETURN res_;
END
$$ LANGUAGE plpgsql;


-- create a pending post, the post is pending due to missing media and/or friendship
CREATE FUNCTION post_create_pending(
    _body character varying,
    _from_user_id INT,
    _to_user_id INT,
    _tag1 character varying,
    _tag2 character varying,
    _tag3 character varying,
    _city_id INT,
    _lat REAL,
    _lon REAL,
    _pending_reason INT
)
RETURNS event_and_data_type_t
AS $$
DECLARE
    post_id_ bigint;
    friendship_ INT := 0;
    res_ event_and_data_type_t;
BEGIN
	-- RAISE NOTICE 'post create pending';
    IF _from_user_id = _to_user_id OR (_pending_reason::BIT(8) & B'00010000' = B'00010000')
      OR _from_user_id = 1
    THEN
        friendship_ = 1;
    ELSE
        SELECT 1 FROM friendships WHERE fk_user_id1 = _from_user_id AND fk_user_id2 = _to_user_id INTO friendship_;
    END IF;
    -- RAISE NOTICE 'friendship %', friendship;
    IF friendship_ = 1 THEN
        INSERT INTO pending_posts(creation_ts, body, from_fk_user_id, to_fk_user_id, fk_devcity_id, geo, pending_reason)
            VALUES(NOW(), _body, _from_user_id, _to_user_id, _city_id, ST_MakePoint(_lon, _lat), _pending_reason::bit(8))
            RETURNING pk_post_id INTO post_id_;
        PERFORM htags_post_pending(post_id_, _tag1, _tag2, _tag3);
        res_.event_type = 'pending_post';
        res_.event_data = post_id_;
        res_.event_data2 = 0;
    ELSE
        res_.event_type = 'N/A';
        res_.event_data = 0;
        res_.event_data2 = 0;
    END IF;
    RETURN res_;
END
$$ LANGUAGE plpgsql;



-- create a pending post, the post is pending due to pending user
CREATE FUNCTION post_create_pending_user(
    _body character varying,
    _from_user_id INT,
    _to_pending_user_id INT,
    _tag1 character varying,
    _tag2 character varying,
    _tag3 character varying,
    _city_id INT,
    _lat REAL,
    _lon REAL,
    _pending_reason INT
)
RETURNS event_and_data_type_t
AS $$
DECLARE
    post_id_ bigint;
    friendship_ INT := 0;
    res_ event_and_data_type_t;
BEGIN
    INSERT INTO pending_posts(creation_ts, body, from_fk_user_id, to_fk_pending_user_id, fk_devcity_id, geo, pending_reason)
        VALUES(NOW(), _body, _from_user_id, _to_pending_user_id, _city_id, ST_MakePoint(_lon, _lat), _pending_reason::bit(8))
        RETURNING pk_post_id INTO post_id_;
    PERFORM htags_post_pending(post_id_, _tag1, _tag2, _tag3);
    res_.event_type = 'pending_post';
    res_.event_data = post_id_;
    res_.event_data2 = 0;
    RETURN res_;
END
$$ LANGUAGE plpgsql;



--CREATE FUNCTION posts_publish(
--    _from_user_id INT,
--    _to_user_id INT,
--    _post_id BIGINT
--)
--RETURNS event_and_data_type_t
--AS $$
--DECLARE
--    res_ event_and_data_type_t;
--BEGIN
--	-- TODO EVENTS: decoupler ici
--	RAISE WARNING 'SHOULD NOT HAPPEN ANY MORE ******************************';
--    PERFORM flow_update_for_new_post(_from_user_id, _post_id);
--    IF _from_user_id != _to_user_id THEN
--        PERFORM flow_update_for_new_post(_to_user_id, _post_id);
--    END IF;
--    --PERFORM event_create_new_post(_post_id);
--    res_.event_type = 'post';
--    res_.event_data = _post_id;
--    res_.event_data2 = 0;
--    RETURN res_;
--END
--$$ LANGUAGE plpgsql;
--
--CREATE FUNCTION posts_publish(
--    _post_id BIGINT
--)
--RETURNS event_and_data_type_t
--AS $$
--DECLARE
--    from_user_id_ INT;
--    to_user_id_ INT;
--    res_ event_and_data_type_t;
--BEGIN
--    SELECT from_fk_user_id, to_fk_user_id
--        INTO from_user_id_, to_user_id_
--        FROM posts
--        WHERE pk_post_id = _post_id;
--    RETURN posts_publish(from_user_id_, to_user_id_, _post_id);
--END
--$$ LANGUAGE plpgsql;


CREATE FUNCTION post_unpend_priv(
    _post_id BIGINT
)
RETURNS event_and_data_type_t
AS $$
DECLARE
    real_post_id_ BIGINT;
    res_ event_and_data_type_t;
BEGIN
    --RAISE WARNING 'post_unpend_priv %', _post_id;
	IF EXISTS(
	    SELECT 1 FROM pending_posts
	        WHERE pk_post_id = _post_id
	        AND pending_reason = B'00000000'
--			AND NOT pending_pic
--			AND NOT pending_video
--			AND NOT pending_thumbnail
--			AND NOT pending_rcpt
    )
    THEN
        INSERT INTO posts(creation_ts, body, fk_media_id, fk_media_vid_id, from_fk_user_id, to_fk_user_id, fk_devcity_id, geo)
            SELECT        NOW(),       body, fk_media_id, fk_media_vid_id, from_fk_user_id, to_fk_user_id, fk_devcity_id, geo
                FROM pending_posts pp
                WHERE pp.pk_post_id = _post_id
            RETURNING pk_post_id INTO real_post_id_;
        INSERT INTO posts_htags(fk_post_id, ck_seq_id, fk_htag_id)
            SELECT real_post_id_, php.ck_seq_id, php.fk_htag_id
                FROM posts_htags_pending php
                WHERE php.fk_post_id = _post_id;
        DELETE FROM pending_posts WHERE pk_post_id = _post_id;
        -- RETURN posts_publish(real_post_id_);
        res_.event_type = 'post';
        res_.event_data = real_post_id_;
        res_.event_data2 = 0;
    ELSE
        res_.event_type = 'N/A';
        res_.event_data = 0;
        res_.event_data2 = 0;
    END IF;
    RETURN res_;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION post_unpend_pic(
    _post_id BIGINT,
    _media_id BIGINT
)
RETURNS event_and_data_type_t
AS $$
DECLARE
    res_ event_and_data_type_t;
BEGIN
    -- RAISE WARNING 'post_unpend_pic %', _post_id;
    UPDATE pending_posts
--        SET pending_pic = FALSE, fk_media_id = _media_id
        SET pending_reason = pending_reason & (B'11111110'), fk_media_id = _media_id
        WHERE pk_post_id = _post_id; -- AND pending_pic ??
    IF found THEN
        SELECT * FROM post_unpend_priv(_post_id) INTO res_;
    ELSE
        res_.event_type = 'N/A';
	    res_.event_data = 0;
	    res_.event_data2 = 0;
    END IF;
    RETURN res_;
    --RAISE WARNING 'post_unpend_pic DONE';
END
$$ LANGUAGE plpgsql;

CREATE FUNCTION post_unpend_video(
    _post_id BIGINT,
    _media_id BIGINT
)
RETURNS event_and_data_type_t
AS $$
DECLARE
    res_ event_and_data_type_t;
BEGIN
    -- RAISE WARNING 'post_unpend_video %', _post_id;
    UPDATE pending_posts
--        SET pending_video = FALSE, fk_media_vid_id = _media_id
        SET pending_reason = pending_reason & (B'11111101'), fk_media_vid_id = _media_id
        WHERE pk_post_id = _post_id; -- AND pending_video ??
    IF found THEN
        SELECT * FROM post_unpend_priv(_post_id) INTO res_;
    ELSE
	    res_.event_type = 'N/A';
	    res_.event_data = 0;
	    res_.event_data2 = 0;
    END IF;
    RETURN res_;
END
$$ LANGUAGE plpgsql;

CREATE FUNCTION post_unpend_thumbnail(
    _post_id BIGINT,
    _media_id BIGINT
)
RETURNS event_and_data_type_t
AS $$
DECLARE
    res_ event_and_data_type_t;
BEGIN
    -- RAISE WARNING 'post_unpend_thumbnail %', _post_id;
    UPDATE pending_posts
--        SET pending_thumbnail = FALSE, fk_media_id = _media_id
        SET pending_reason = pending_reason & (B'11111011'), fk_media_id = _media_id
        WHERE pk_post_id = _post_id; -- AND pending_thumbnail ??
    IF found THEN
        -- RAISE WARNING 'HERE';
        SELECT * FROM post_unpend_priv(_post_id) INTO res_;
        -- RAISE WARNING 'THERE';
    ELSE
	    res_.event_type = 'N/A';
	    res_.event_data = 0;
	    res_.event_data2 = 0;
    END IF;
    RETURN res_;
END
$$ LANGUAGE plpgsql;




CREATE FUNCTION post_unpend_friendship(
    _post_id BIGINT
)
RETURNS event_and_data_type_t
AS $$
DECLARE
    res_ event_and_data_type_t;
BEGIN
    --RAISE WARNING 'post_unpend_friendship %', _post_id;
    UPDATE pending_posts
        SET pending_reason = pending_reason & (B'11101111')
        WHERE pk_post_id = _post_id; -- AND pending_reason... ??
    IF found THEN
        -- RAISE WARNING 'HERE';
        SELECT * FROM post_unpend_priv(_post_id) INTO res_;
        -- RAISE WARNING 'THERE';
    ELSE
        res_.event_type = 'N/A';
        res_.event_data = 0;
        res_.event_data2 = 0;
    END IF;
    RETURN res_;
END
$$ LANGUAGE plpgsql;



CREATE FUNCTION posts_unpend_for_friendship(
    _from_user_id INTEGER,
    _to_user_id INTEGER
)
RETURNS SETOF event_and_data_type_t
AS $$
DECLARE
    r RECORD;
BEGIN
    --RAISE WARNING 'posts_unpend_for_friendship % %', _from_user_id,  _to_user_id;
	FOR r IN SELECT p.pk_post_id FROM pending_posts p 
            WHERE p.from_fk_user_id = _from_user_id
              AND p.to_fk_user_id = _to_user_id
    LOOP
        --RAISE WARNING 'posts_unpend_for_friendship %', r.pk_post_id;
        RETURN QUERY SELECT e.* FROM post_unpend_friendship(r.pk_post_id) e WHERE e.event_type='post';
    END LOOP;
    RETURN;
END
$$ LANGUAGE plpgsql;






CREATE FUNCTION posts_unpend_rcpt_priv(
    _to_user_id INTEGER,
    _pending_user_id INTEGER
)
RETURNS SETOF event_and_data_type_t
AS $$
DECLARE
    r RECORD;
BEGIN
    --RAISE WARNING 'posts_unpend_rcpt %', _to_user_id;    
    UPDATE pending_posts
    SET pending_reason = (pending_reason & (B'11110111')) | B'00010000', to_fk_user_id = _to_user_id
    WHERE to_fk_pending_user_id = _pending_user_id; -- AND pending_reason... ??

       -- convertir les pending_friendship_requests vers le pending_user
       -- en friendship_requests vers le user
    FOR r IN
        SELECT from_fk_user_id
            FROM pending_friendship_requests
            WHERE to_fk_pending_user_id = _pending_user_id
    LOOP
        --RAISE WARNING 'posts_unpend_rcpt FR % -> %',r.from_fk_user_id, _to_user_id;
        RETURN QUERY SELECT e.* FROM friendship_request_create(r.from_fk_user_id, _to_user_id) e;-- WHERE e.event_type='friendship_request' OR e.event_type='already_friends';
    END LOOP;
    --DELETE FROM pending_users WHERE pk_user_id = _pending_user_id;
    RETURN;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION posts_unpend_rcpt_by_phone(
    _to_user_id INTEGER
)
RETURNS SETOF event_and_data_type_t
AS $$
DECLARE
    r RECORD;
    pending_user_id_ INTEGER;
BEGIN
    --RAISE WARNING 'posts_unpend_rcpt %', _to_user_id;
    SELECT pu.pk_user_id FROM pending_users pu JOIN users u ON u.phone = pu.phone WHERE u.pk_user_id = _to_user_id INTO pending_user_id_;

    RETURN QUERY SELECT * FROM posts_unpend_rcpt_priv(_to_user_id, pending_user_id_);
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION posts_unpend_rcpt_by_fb(
    _to_user_id INTEGER
)
RETURNS SETOF event_and_data_type_t
AS $$
DECLARE
    r RECORD;
    pending_user_id_ INTEGER;
BEGIN
    --RAISE WARNING 'posts_unpend_rcpt %', _to_user_id;
    SELECT pu.pk_user_id FROM pending_users pu JOIN users u ON u.facebook_id = pu.facebook_id WHERE u.pk_user_id = _to_user_id INTO pending_user_id_;

    RETURN QUERY SELECT * FROM posts_unpend_rcpt_priv(_to_user_id, pending_user_id_);
END
$$ LANGUAGE plpgsql;



CREATE FUNCTION posts_delete(
    _post_id BIGINT,
    _user_id INT
)
RETURNS boolean
AS $$
DECLARE
BEGIN
    -- authorized to delete the post ?
    -- must be creator or recipient of the post
    IF EXISTS (
        SELECT 1 FROM posts
            WHERE pk_post_id = _post_id
                AND (
                    from_fk_user_id = _user_id
                    OR
                    to_fk_user_id = _user_id
                )
    ) THEN
        DELETE FROM posts WHERE pk_post_id = _post_id;
        --PERFORM flow_update_for_post_removal(_post_id);
        RETURN true;
    END IF;
    RETURN false;
END
$$ LANGUAGE plpgsql;

