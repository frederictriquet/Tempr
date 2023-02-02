CREATE TYPE do_i_like_t AS (
    like1 BOOLEAN,
    like2 BOOLEAN,
    like3 BOOLEAN
);

--CREATE VIEW view_flow_posts AS
--    SELECT p.*, f.fk_user_id FROM flows f
--        JOIN view_post_with_tags p ON p.pk_post_id = f.fk_post_id;

CREATE FUNCTION flow_posts_get(
    _user_id INT,
    _start INT
)
RETURNS SETOF view_decorated_posts
AS $$
    SELECT v.*
        FROM view_decorated_posts v
        JOIN flows f ON f.fk_post_id = v.pk_post_id
        WHERE fk_user_id = _user_id
        ORDER BY v.creation_ts DESC
        LIMIT 5 OFFSET _start;
$$ LANGUAGE sql STABLE;


CREATE FUNCTION flow_posts_down_get(
    _user_id INT,
    _before_ts TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
)
RETURNS SETOF view_decorated_posts
AS $$
    SELECT v.*
        FROM view_decorated_posts v
        JOIN flows f ON f.fk_post_id = v.pk_post_id
        WHERE fk_user_id = _user_id
            AND v.creation_ts < _before_ts
        ORDER BY v.creation_ts DESC
        LIMIT 5;
$$ LANGUAGE sql STABLE;

CREATE FUNCTION flow_posts_up_get(
    _user_id INT,
    _after_ts TIMESTAMP WITHOUT TIME ZONE
)
RETURNS SETOF view_decorated_posts
AS $$
    WITH p AS (
        SELECT v.*
            FROM view_decorated_posts v
            JOIN flows f ON f.fk_post_id = v.pk_post_id
            WHERE fk_user_id = _user_id
                AND v.creation_ts > _after_ts
            ORDER BY v.creation_ts ASC
            LIMIT 5
    )
    SELECT * FROM p ORDER BY creation_ts DESC;
$$ LANGUAGE sql STABLE;



CREATE FUNCTION flow_get_do_i_like(
    _user_id INT,
    _post_ids BIGINT[]
)
RETURNS TABLE(pk_post_id BIGINT, ck_seq_id INT)
AS $$
    SELECT f.fk_post_id AS pk_post_id, hl.ck_seq_id
       FROM flows f
           JOIN posts p ON p.pk_post_id = f.fk_post_id
           JOIN htags_likes hl ON hl.fk_post_id = p.pk_post_id
           	                  AND hl.fk_user_id = _user_id
           	                  --AND NOW() < hl.end_ts
       WHERE f.fk_user_id = _user_id AND f.fk_post_id = ANY(_post_ids)
       ORDER BY p.creation_ts DESC
    ;
$$ LANGUAGE sql STABLE;

-- add a post to the flows of:
-- * the user of the post (may be the creator or the recipient)
-- * his friends

CREATE FUNCTION flow_update_for_new_post(
    _user_id INT,
    _post_id BIGINT
)
RETURNS void
AS $$
BEGIN
	-- in my flow
    INSERT INTO flows(fk_user_id, fk_post_id)
       SELECT _user_id, _post_id
       WHERE NOT EXISTS (SELECT 1 FROM flows WHERE fk_user_id = _user_id AND fk_post_id = _post_id);

    -- in my friends' flows
    INSERT INTO flows(fk_user_id, fk_post_id)
       SELECT fk_user_id2, _post_id
           FROM friendships
           WHERE fk_user_id1 = _user_id
           AND NOT EXISTS (SELECT 1 FROM flows WHERE fk_user_id = fk_user_id2 AND fk_post_id = _post_id);
END
$$ LANGUAGE plpgsql;



-- for both users, add posts (to their respective flows) from/to the other user
CREATE FUNCTION flow_update_for_new_friendship(
    _user_id1 INT,
    _user_id2 INT
)
RETURNS void
AS $$
BEGIN
    PERFORM flow_update_for_new_friendship_priv(_user_id1,_user_id2);
    PERFORM flow_update_for_new_friendship_priv(_user_id2,_user_id1);
END
$$ LANGUAGE plpgsql;


-- PRIVATE function
-- updates user2's flow with posts from user1 (user1's posts as creator and recipient)
-- this function is called twice by flow_update_for_new_friendship
CREATE FUNCTION flow_update_for_new_friendship_priv(
    _user_id1 INT,
    _user_id2 INT
)
RETURNS void
AS $$
BEGIN
    INSERT INTO flows(fk_user_id, fk_post_id)
        SELECT _user_id2, p.pk_post_id
            FROM posts p
            JOIN friendships fr ON fr.fk_user_id2 = _user_id2 AND fr.fk_user_id1 = _user_id1
            WHERE (p.from_fk_user_id = _user_id1 OR p.to_fk_user_id = _user_id1)
            AND NOT EXISTS (SELECT 1 FROM flows fl WHERE fl.fk_user_id = _user_id2 AND fl.fk_post_id = p.pk_post_id)
            and p.from_fk_user_id != 1
            ;
END
$$ LANGUAGE plpgsql;



-- for both users, remove the posts (from their respective flows) from/to the other user
CREATE FUNCTION flow_update_for_friendship_removal(
    _user_id1 INT,
    _user_id2 INT
)
RETURNS void
AS $$
BEGIN
	PERFORM flow_update_for_friendship_removal_priv(_user_id1, _user_id2);
    PERFORM flow_update_for_friendship_removal_priv(_user_id2, _user_id1);
END
$$ LANGUAGE plpgsql;


-- PRIVATE function
-- update user2's flow by removing posts about user1 (posts from/to user1)
CREATE FUNCTION flow_update_for_friendship_removal_priv(
    _user_id1 INT,
    _user_id2 INT
)
RETURNS void
AS $$
BEGIN
	DELETE FROM flows
	    WHERE (fk_user_id, fk_post_id)
	    IN (SELECT _user_id2, pk_post_id
	        FROM posts p
	        WHERE p.from_fk_user_id = _user_id1 OR p.to_fk_user_id = _user_id1
	    );
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION flow_update_for_post_removal(
    _post_id BIGINT
)
RETURNS void
AS $$
    DELETE FROM flows WHERE fk_post_id = _post_id;
$$ LANGUAGE sql;
