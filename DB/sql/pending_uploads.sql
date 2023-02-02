
CREATE TABLE pending_uploads (
    token CHARACTER VARYING(64) NOT NULL,
    fk_user_id INTEGER NOT NULL,
    fk_media_id BIGINT NOT NULL,
    fk_post_id BIGINT,
    destination SMALLINT NOT NULL, -- 0='profile', 1='background', 2='post image', 3='post video', 4='post video thumbnail'
    creation_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,

    PRIMARY KEY (token),
    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_post_id) REFERENCES pending_posts(pk_post_id) ON DELETE CASCADE
);


CREATE FUNCTION pending_upload_create(
    _token CHARACTER VARYING,
    _user_id INT,
    _filename CHARACTER VARYING,
    _destination INT, -- 0='profile', 1='background', 2='post image', 3='post video', 4='post video thumbnail'
    _post_id BIGINT DEFAULT NULL
)
RETURNS void
AS $$
DECLARE
    media_id_ BIGINT;
BEGIN
    -- create the media
    SELECT media_create(_filename) INTO media_id_;

    INSERT INTO pending_uploads
              (token, fk_user_id, fk_media_id, destination, fk_post_id, creation_ts)
        VALUES(_token, _user_id, media_id_, _destination, _post_id, NOW());
END;
$$ LANGUAGE plpgsql;





CREATE FUNCTION pending_upload_confirm(
    _token CHARACTER VARYING,
    _user_id INT
)
RETURNS event_and_data_type_t -- see events.sql for type definition
AS $$
DECLARE
    post_id_ BIGINT;
    media_id_ BIGINT;
    destination_ INTEGER;
    res_ event_and_data_type_t;
BEGIN
	-- RAISE WARNING 'pending upload confirm';
    SELECT fk_post_id, fk_media_id, destination FROM pending_uploads
        WHERE token = _token AND fk_user_id = _user_id
        INTO post_id_, media_id_, destination_;
    IF NOT found THEN
        -- RAISE WARNING 'not found';
        res_.event_type = 'N/A';
        res_.event_data = 0;
        res_.event_data2 = 0;
    ELSE
	    PERFORM media_confirm(media_id_);
	    -- link media and profile/post
	    CASE destination_
	        WHEN 0 THEN
	            -- RAISE WARNING 'profile';
	            UPDATE users SET fk_media_id_profile = media_id_ WHERE pk_user_id = _user_id;
	            res_.event_type = 'profile_updated';
	            res_.event_data = _user_id;
                res_.event_data2 = 0;
	        WHEN 1 THEN
	            -- RAISE WARNING 'background';
	            UPDATE users SET fk_media_id_background = media_id_ WHERE pk_user_id = _user_id;
	            res_.event_type = 'background_updated';
	            res_.event_data = _user_id;
                res_.event_data2 = 0;
	        WHEN 2 THEN
	            -- RAISE WARNING 'post image';
	            SELECT * FROM post_unpend_pic(post_id_,media_id_) INTO res_;
	        WHEN 3 THEN
	            -- RAISE WARNING 'post video';
	            SELECT * FROM post_unpend_video(post_id_,media_id_) INTO res_;
	        WHEN 4 THEN
	            -- RAISE WARNING 'post video thumbnail *******************';
	            SELECT * FROM post_unpend_thumbnail(post_id_,media_id_) INTO res_;
                -- RAISE WARNING 'post video thumbnail DONE **************';
	    END CASE;
	
	    -- RAISE WARNING 'delete';
	    -- remove the pending upload
	    DELETE FROM pending_uploads WHERE token = _token;
    END IF;
    -- RAISE WARNING 'PENDING UPLOAD CONFIRMED';
    RETURN res_;
END;
$$ LANGUAGE plpgsql;





CREATE FUNCTION pending_upload_abort(
    _token CHARACTER VARYING,
    _user_id INT
)
RETURNS CHARACTER VARYING
AS $$
DECLARE
    post_id_ BIGINT;
    media_id_ BIGINT;
    destination_ INTEGER;
BEGIN
    -- RAISE WARNING 'pending upload abort';
    SELECT fk_post_id, fk_media_id, destination FROM pending_uploads
        WHERE token = _token AND fk_user_id = _user_id
        INTO post_id_, media_id_, destination_;
    IF NOT found THEN
        -- RAISE WARNING 'not found';
        RETURN 'No such pending upload';
    END IF;
    PERFORM media_delete(media_id_);
    -- link media and profile
    CASE destination_
        WHEN 0 THEN
            -- RAISE WARNING 'profile';
        WHEN 1 THEN
            -- RAISE WARNING 'background';
        WHEN 2 THEN
            -- RAISE WARNING 'post image';
            DELETE FROM pending_posts WHERE pk_post_id = post_id_;
        WHEN 3 THEN
            -- RAISE WARNING 'post video';
            DELETE FROM pending_posts WHERE pk_post_id = post_id_;
        WHEN 4 THEN
            -- RAISE WARNING 'post video thumbnail';
            DELETE FROM pending_posts WHERE pk_post_id = post_id_;
    END CASE;

    -- RAISE WARNING 'delete';
    -- remove the pending upload
    DELETE FROM pending_uploads WHERE token = _token;

    RETURN 'aborted';
END;
$$ LANGUAGE plpgsql;
