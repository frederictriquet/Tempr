-- -------------------------------------------------------------------- PENDING USERS
CREATE TABLE pending_users (
    pk_user_id SERIAL,

    phone CHARACTER VARYING(50),
    facebook_id CHARACTER VARYING(64),

    PRIMARY KEY (pk_user_id)
);

-- -------------------------------------------------------------------- PENDING FRIENDSHIP REQUESTS
CREATE TABLE pending_friendship_requests(
    from_fk_user_id INTEGER NOT NULL,
    to_fk_pending_user_id INTEGER NOT NULL,
    request_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,

    FOREIGN KEY (from_fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (to_fk_pending_user_id) REFERENCES pending_users(pk_user_id) ON DELETE CASCADE
);
-- -------------------------------------------------------------------- PENDING POSTS
CREATE TABLE pending_posts (
    pk_post_id BIGSERIAL,
    creation_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    body VARCHAR(512),

    fk_media_id BIGINT,
    fk_media_vid_id BIGINT,

    from_fk_user_id INTEGER NOT NULL,
    to_fk_user_id INTEGER,
    to_fk_pending_user_id INTEGER,

    fk_devcity_id BIGINT,

    pending_reason bit(8),
    nb_reminds integer default(0),
    last_remind_date date,

    PRIMARY KEY (pk_post_id),
    FOREIGN KEY (fk_devcity_id) REFERENCES devcities(pk_devcity_id) ON DELETE SET NULL,
    FOREIGN KEY (fk_media_id) REFERENCES medias(pk_media_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_media_vid_id) REFERENCES medias(pk_media_id) ON DELETE CASCADE,
    FOREIGN KEY (from_fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (to_fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (to_fk_pending_user_id) REFERENCES pending_users(pk_user_id) ON DELETE SET NULL
);
-- -------------------------------------------------------------------- PENDING HTAGS
CREATE TABLE posts_htags_pending (
    fk_post_id BIGINT NOT NULL,
    ck_seq_id INTEGER NOT NULL,

    fk_htag_id BIGINT NOT NULL,
    pop INTEGER NOT NULL DEFAULT(0),

    FOREIGN KEY (fk_post_id) REFERENCES pending_posts(pk_post_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_htag_id) REFERENCES htags(pk_htag_id),
    PRIMARY KEY (fk_post_id, ck_seq_id)
);

CREATE VIEW view_pending_post_with_tags AS
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
        FROM pending_posts p
            LEFT JOIN posts_htags_pending ph ON ph.fk_post_id = p.pk_post_id
            LEFT JOIN htags h ON h.pk_htag_id = ph.fk_htag_id
            LEFT JOIN medias m ON m.pk_media_id = fk_media_id
            LEFT JOIN medias mv ON mv.pk_media_id = fk_media_vid_id
        GROUP BY p.pk_post_id;

CREATE VIEW view_decorated_pending_posts AS
    SELECT p.*,
        u1.firstname AS from_firstname,
        u1.lastname AS from_lastname,
        m1.filename AS from_filename_profile,
        u2.firstname AS to_firstname,
        u2.lastname AS to_lastname,
        m2.filename AS to_filename_profile
        ,c.locality AS city
    FROM view_pending_post_with_tags p
        JOIN users u1 ON u1.pk_user_id = p.from_fk_user_id
            LEFT JOIN medias m1 ON m1.pk_media_id = u1.fk_media_id_profile
        LEFT JOIN users u2 ON u2.pk_user_id = p.to_fk_user_id
            LEFT JOIN medias m2 ON m2.pk_media_id = u2.fk_media_id_profile
        LEFT JOIN devcities c ON c.pk_devcity_id = p.fk_devcity_id
    ;





-- -------------------------------------------------------------------- FUNCTIONS

-- TODO quand on passe à postgres 9.5 utiliser le "on conflict"
CREATE FUNCTION pending_user_create_by_phone(
    _phone CHARACTER VARYING(50)
)
RETURNS INTEGER
AS $$
DECLARE
    id_ INTEGER;
BEGIN
    --RAISE WARNING 'pending_user_create_by_phone';
    LOCK TABLE pending_users IN EXCLUSIVE MODE;
    SELECT pk_user_id INTO id_ FROM pending_users WHERE phone = _phone LIMIT 1;
    IF NOT found THEN
        INSERT INTO pending_users(phone) VALUES (_phone) RETURNING pk_user_id INTO id_;
    END IF;
    RETURN id_;
END
$$ LANGUAGE plpgsql;

-- TODO quand on passe à postgres 9.5 utiliser le "on conflict"
CREATE FUNCTION pending_user_create_by_fb(
    _facebook_id CHARACTER VARYING(64)
)
RETURNS INTEGER
AS $$
DECLARE
    id_ INTEGER;
BEGIN
    LOCK TABLE pending_users IN EXCLUSIVE MODE;
    SELECT pk_user_id INTO id_ FROM pending_users WHERE facebook_id = _facebook_id LIMIT 1;
    IF NOT found THEN
        INSERT INTO pending_users(facebook_id) VALUES (_facebook_id) RETURNING pk_user_id INTO id_;
    END IF;
    RETURN id_;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION pending_user_request_friendship(
    _from_user_id INT,
    _to_user_id INT
)
RETURNS void
AS $$
BEGIN
    --RAISE WARNING 'pending_user_request_friendship % -> %"',_from_user_id, _to_user_id;
    INSERT INTO pending_friendship_requests(from_fk_user_id,to_fk_pending_user_id,request_ts)
        SELECT _from_user_id,_to_user_id,NOW()
        WHERE NOT EXISTS(
            SELECT 1 FROM pending_friendship_requests
                WHERE from_fk_user_id = _from_user_id
                    AND to_fk_pending_user_id = _to_user_id
        );
END
$$ LANGUAGE plpgsql;
