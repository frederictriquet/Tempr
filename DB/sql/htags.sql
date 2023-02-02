

-- TODO quand on passe à postgres 9.5 utiliser le "on conflict"
CREATE FUNCTION htags_create(
    _tag character varying
)
RETURNS BIGINT
AS $$
DECLARE
    id_ BIGINT;
BEGIN
    LOCK TABLE htags IN EXCLUSIVE MODE;
    SELECT pk_htag_id INTO id_ FROM htags WHERE tag = _tag;
    IF NOT found THEN
	    INSERT INTO htags(tag) VALUES(_tag) RETURNING pk_htag_id INTO id_;
    END IF;
    RETURN id_;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION htags_add(
    _post_id BIGINT,
    _tag character varying,
    _seq_id INT
)
RETURNS void
AS $$
BEGIN
	IF _tag != '' THEN
	   INSERT INTO posts_htags(fk_post_id, fk_htag_id, ck_seq_id)
	       SELECT _post_id,
	              htags_create(_tag),
	              _seq_id;
	END IF;
END
$$ LANGUAGE plpgsql;



CREATE FUNCTION htags_post(
    _post_id BIGINT,
    _tag1 character varying,
    _tag2 character varying,
    _tag3 character varying
)
RETURNS void
AS $$
BEGIN
	PERFORM htags_add(_post_id, _tag1, 1);
    PERFORM htags_add(_post_id, _tag2, 2);
    PERFORM htags_add(_post_id, _tag3, 3);
END
$$ LANGUAGE plpgsql;


-- TODO factoriser ?
CREATE FUNCTION htags_add_pending(
    _post_id BIGINT,
    _tag character varying,
    _seq_id INT
)
RETURNS void
AS $$
BEGIN
    IF _tag != '' THEN
       INSERT INTO posts_htags_pending(fk_post_id, fk_htag_id, ck_seq_id)
           SELECT _post_id,
                  htags_create(_tag),
                  _seq_id;
    END IF;
END
$$ LANGUAGE plpgsql;


-- TODO factoriser ?
CREATE FUNCTION htags_post_pending(
    _post_id BIGINT,
    _tag1 character varying,
    _tag2 character varying,
    _tag3 character varying
)
RETURNS void
AS $$
BEGIN
    PERFORM htags_add_pending(_post_id, _tag1, 1);
    PERFORM htags_add_pending(_post_id, _tag2, 2);
    PERFORM htags_add_pending(_post_id, _tag3, 3);
END
$$ LANGUAGE plpgsql;


--CREATE FUNCTION htag_like(
--    _user_id INT,
--    _post_id BIGINT,
--    _tag_num INT
--)
--RETURNS BOOLEAN
--AS $$
--BEGIN
--	--RAISE WARNING 'LIKE % %', _post_id, _tag_num;
--    -- check if user can see the post
--    IF NOT EXISTS (
--        SELECT 1 FROM flows WHERE
--            fk_user_id = _user_id AND
--            fk_post_id = _post_id
--        )
--    THEN
--        --RAISE WARNING 'user cannot see post';
--        RETURN FALSE;
--    END IF;
--
--    -- check if user has an active like (wants to like twice ? then do nothing)
--	IF EXISTS (
--	    SELECT 1 FROM htags_likes WHERE
--	        fk_user_id = _user_id AND
--	        ck_seq_id = _tag_num AND
--	        fk_post_id = _post_id AND
--	        NOW() BETWEEN begin_ts AND end_ts
--	    )
--	THEN
--        --RAISE WARNING 'user already likes tag';
--        RETURN FALSE;
--    END IF;
--
--    INSERT INTO htags_likes(fk_user_id, fk_post_id, ck_seq_id, begin_ts, end_ts)
--            VALUES (_user_id, _post_id, _tag_num, NOW(), 'INFINITY'::timestamp without time zone);
--    UPDATE posts_htags
--        SET pop=pop+1
--        WHERE
--            fk_post_id = _post_id AND
--            ck_seq_id = _tag_num;
--    --RAISE WARNING 'done %', found;
--    RETURN TRUE;
--END
--$$ LANGUAGE plpgsql;
--
--
--
--CREATE FUNCTION htag_unlike(
--    _user_id INT,
--    _post_id BIGINT,
--    _tag_num INT
--)
--RETURNS void
--AS $$
--BEGIN
--    --RAISE WARNING 'UNLIKE % %', _post_id, _tag_num;
--    -- check if user can see the post
--    IF NOT EXISTS (
--        SELECT 1 FROM flows WHERE
--            fk_user_id = _user_id AND
--            fk_post_id = _post_id
--        )
--    THEN
--        --RAISE WARNING 'user cannot see post';
--        RETURN;
--    END IF;
--
--    -- check if user has zero active like (wants to unlike twice, or unlike something he hasn't already liked ? then do nothing)
--    IF NOT EXISTS (
--        SELECT 1 FROM htags_likes WHERE
--            fk_user_id = _user_id AND
--            ck_seq_id = _tag_num AND
--            fk_post_id = _post_id AND
--            NOW() BETWEEN begin_ts AND end_ts
--        )
--    THEN
--        --RAISE WARNING 'user does not already likes tag';
--        RETURN;
--    END IF;
--
--    UPDATE htags_likes
--        SET end_ts = NOW()
--        WHERE
--            fk_user_id = _user_id AND
--            ck_seq_id = _tag_num AND
--            fk_post_id = _post_id AND
--            end_ts > NOW();
--    --RAISE WARNING 'done %', found;
--    UPDATE posts_htags
--        SET pop=pop-1
--        WHERE
--            fk_post_id = _post_id AND
--            ck_seq_id = _tag_num;
--    --RAISE WARNING 'done2 %', found;
--END
--$$ LANGUAGE plpgsql;


CREATE FUNCTION htag_like_priv_(
    _user_id INT,
    _post_id BIGINT,
    _tag_num INT
)
RETURNS VOID
AS $$
BEGIN
    INSERT INTO htags_likes(fk_user_id, fk_post_id, ck_seq_id, begin_ts)
            VALUES (_user_id, _post_id, _tag_num, NOW());
    -- TODO voir si un trigger serait mieux adapté ici
    UPDATE posts_htags
        SET pop=pop+1
        WHERE
            fk_post_id = _post_id AND
            ck_seq_id = _tag_num;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION htag_like(
    _user_id INT,
    _post_id BIGINT,
    _tag_num INT
)
RETURNS BOOLEAN
AS $$
BEGIN
    --RAISE WARNING 'LIKE % %', _post_id, _tag_num;
    -- check if user can see the post
    IF NOT EXISTS (
        SELECT 1 FROM flows WHERE
            fk_user_id = _user_id AND
            fk_post_id = _post_id
        )
    THEN
        --RAISE WARNING 'user cannot see post';
        RETURN FALSE;
    END IF;

    PERFORM htag_like_priv_(_user_id, _post_id, _tag_num)
        WHERE NOT EXISTS (
	        SELECT 1 FROM htags_likes WHERE
	            fk_user_id = _user_id AND
	            ck_seq_id = _tag_num AND
	            fk_post_id = _post_id
        );
    --RAISE WARNING 'done %', found;
    RETURN TRUE;
END
$$ LANGUAGE plpgsql;



CREATE FUNCTION htag_unlike(
    _user_id INT,
    _post_id BIGINT,
    _tag_num INT
)
RETURNS void
AS $$
BEGIN
    --RAISE WARNING 'UNLIKE % %', _post_id, _tag_num;
    -- check if user can see the post
    IF NOT EXISTS (
        SELECT 1 FROM flows WHERE
            fk_user_id = _user_id AND
            fk_post_id = _post_id
        )
    THEN
        --RAISE WARNING 'user cannot see post';
        RETURN;
    END IF;

    DELETE FROM htags_likes
        WHERE
            fk_user_id = _user_id AND
            ck_seq_id = _tag_num AND
            fk_post_id = _post_id;
    IF found THEN
	    UPDATE posts_htags
	        SET pop=pop-1
	        WHERE
	            fk_post_id = _post_id AND
	            ck_seq_id = _tag_num AND
	            pop > 0;
	END IF;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION htags_suggest(
    _user_id INT
)
RETURNS TABLE(tag CHARACTER VARYING(255))
AS $$
    SELECT * FROM (
        select tag from view_user_recent_trends WHERE fk_user_id = _user_id order by pop desc limit 15
    ) dummy1
    UNION
    SELECT * FROM (
        select tag from view_user_long_term_trends WHERE fk_user_id = _user_id order by pop desc limit 25
    ) dummy2;
$$ LANGUAGE sql STABLE;



