
CREATE FUNCTION comment_create(
    _post_id BIGINT,
    _user_id INT,
    _body TEXT
)
RETURNS boolean
AS $$
DECLARE
BEGIN
	-- authorized to post a comment ?
	-- post must be in user's flow
    IF EXISTS (
    	SELECT 1 FROM flows
	      WHERE fk_post_id = _post_id
    	     AND fk_user_id = _user_id
    ) THEN
        INSERT INTO comments(creation_ts, body, fk_post_id, from_fk_user_id)
            VALUES (NOW(), _body, _post_id, _user_id);
        UPDATE posts SET nb_comments = nb_comments+1 WHERE pk_post_id = _post_id;
        RETURN true;
    ELSE
        RETURN false;
    END IF;
END 
$$ LANGUAGE plpgsql;


CREATE FUNCTION comment_delete(
    _comment_id BIGINT,
    _user_id INT
)
RETURNS boolean
AS $$
DECLARE
    post_id_ BIGINT;
BEGIN
    -- authorized to delete the comment ?
    -- must be creator of the comment or recipient of the post
    SELECT fk_post_id INTO post_id_ FROM comments
        WHERE pk_comment_id = _comment_id
          AND from_fk_user_id = _user_id;
    IF post_id_ IS NULL THEN
        SELECT fk_post_id INTO post_id_ FROM comments c
            JOIN posts p ON p.pk_post_id = c.fk_post_id AND p.to_fk_user_id = _user_id
            WHERE c.pk_comment_id = _comment_id;
        IF post_id_ IS NULL THEN
            RETURN false;
        END IF;
    END IF;
    DELETE FROM comments WHERE pk_comment_id = _comment_id;
    UPDATE posts SET nb_comments = nb_comments-1 WHERE pk_post_id = post_id_;
    RETURN found;
END 
$$ LANGUAGE plpgsql;


CREATE FUNCTION comments_get(
    _post_id BIGINT,
    _from_ts TIMESTAMP WITHOUT TIME ZONE DEFAULT to_timestamp(0)
)
RETURNS TABLE(
    pk_comment_id BIGINT,
    creation_ts TIMESTAMP WITHOUT TIME ZONE,
    body VARCHAR(512),
    from_fk_user_id INTEGER,
    firstname CHARACTER VARYING(120),
    lastname CHARACTER VARYING(120),
    filename_profile CHARACTER VARYING(255)
)
AS $$
    SELECT c.pk_comment_id, c.creation_ts, c.body, c.from_fk_user_id, u.firstname, u.lastname, m.filename AS filename_profile
        FROM comments c
            JOIN users u ON u.pk_user_id = c.from_fk_user_id
            LEFT JOIN medias m ON m.pk_media_id = u.fk_media_id_profile
        WHERE c.fk_post_id = _post_id
            AND c.creation_ts > _from_ts
        ORDER BY c.creation_ts ASC
        LIMIT 10;
$$ LANGUAGE sql STABLE;
