-- -------------------------------------------------------------------- META DATA
CREATE TABLE metas (
    pk_meta_id SERIAL,
    name VARCHAR(32) NOT NULL,
    string_val VARCHAR(64),
    int_val BIGINT,
    date_val TIMESTAMP,
    last_update TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT now(),
    PRIMARY KEY (pk_meta_id),
    UNIQUE (name)
);

-- -------------------------------------------------------------------- PARAMETERS
CREATE TABLE parameters (
    pk_parameter_id SERIAL,
    name VARCHAR(32) NOT NULL,
    descr VARCHAR(64) NOT NULL,
    value VARCHAR(256),
    PRIMARY KEY (pk_parameter_id),
    UNIQUE (name)
);

-- -------------------------------------------------------------------- JOBS
CREATE TYPE activity_t AS ENUM('none','launched once','once','active');

CREATE TABLE jobs (
    pk_job_id SERIAL,
    name VARCHAR(128) NOT NULL,
    descr VARCHAR(256) NOT NULL,
    activity activity_t NOT NULL,
    crontab VARCHAR(128) NOT NULL,
    last_begin_ts TIMESTAMP WITHOUT TIME ZONE DEFAULT(to_timestamp(0)),
    last_duration INTEGER,
    is_running BOOLEAN NOT NULL DEFAULT(False),
    status TEXT,
    PRIMARY KEY (pk_job_id),
    UNIQUE (name)
);

-- TODO REMOVE ME
INSERT INTO jobs(name,descr,activity,crontab) VALUES
--('cities','','once','* * * * *'),
('update_user_trends','','active','* * * * *')
,('purge_push_tokens','','active','0 * * * *')
;


-- -------------------------------------------------------------------- MEDIAS
CREATE TABLE medias (
    pk_media_id BIGSERIAL,
    creation_ts TIMESTAMP WITHOUT TIME ZONE,
    filename CHARACTER VARYING(255) NOT NULL,

    PRIMARY KEY (pk_media_id)
);

-- -------------------------------------------------------------------- USERS
CREATE TABLE users (
    pk_user_id SERIAL,
    login CHARACTER VARYING(255) NOT NULL,
    firstname CHARACTER VARYING(120) NOT NULL,
    lastname CHARACTER VARYING(120) NOT NULL,
    birthdate DATE,
    city CHARACTER VARYING(50),
    phone CHARACTER VARYING(50),
    phone_confirmed BOOLEAN NOT NULL DEFAULT(False),
    private BOOLEAN NOT NULL DEFAULT(False),
    lang CHARACTER VARYING(2) NOT NULL DEFAULT('fr'),
    likes INTEGER NOT NULL DEFAULT(0),
    last_hello TIMESTAMP WITHOUT TIME ZONE DEFAULT ('-INFINITY'::TIMESTAMP WITHOUT TIME ZONE),

    -- profile and background pictures
    fk_media_id_profile BIGINT,
    fk_media_id_background BIGINT,

    -- email + password
    email CHARACTER VARYING(128),
    email_confirmed BOOLEAN NOT NULL DEFAULT(False),
    password CHARACTER VARYING(256),

    -- facebook connect
    facebook_id CHARACTER VARYING(64),

    -- push notification parameters
    pn_postaboutyou BOOLEAN NOT NULL DEFAULT(True),
    pn_friendshiprequest BOOLEAN NOT NULL DEFAULT(True),
    pn_frienshipacceptance BOOLEAN NOT NULL DEFAULT(True),
    pn_profileupdated BOOLEAN NOT NULL DEFAULT(True),
    pn_comment BOOLEAN NOT NULL DEFAULT(True),
    pn_like BOOLEAN NOT NULL DEFAULT(True),

    lasttrends_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT ('-INFINITY'::TIMESTAMP WITHOUT TIME ZONE),
    signup_date DATE DEFAULT(NOW()),
    PRIMARY KEY (pk_user_id),
    FOREIGN KEY (fk_media_id_profile) REFERENCES medias(pk_media_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_media_id_background) REFERENCES medias(pk_media_id) ON DELETE CASCADE,
    UNIQUE (login),
    UNIQUE (email),
    UNIQUE (facebook_id)
);

insert into users(pk_user_id, login,firstname,lastname,birthdate,city,private)
    values(1,'Tempr-App', 'Tempr','App', '2016-06-24','Lille',true);

-- -------------------------------------------------------------------- OAUTH2
--CREATE TABLE oauth_tokens (
--    token CHARACTER VARYING(64) NOT NULL,
--    refresh_token CHARACTER VARYING(64) NOT NULL,
--    fk_user_id INTEGER NOT NULL,
--    valid_until_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
--
--    -- CONSTRAINT uniq_token UNIQUE (token),
--    -- CONSTRAINT uniq_refresh_token UNIQUE (refresh_token),
--    PRIMARY KEY (token),
--    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE
--);

-- -------------------------------------------------------------------- DEVICES
CREATE TABLE iosdevices (
    ios_id CHARACTER VARYING(255) NOT NULL,

    fk_user_id INTEGER NOT NULL,

    UNIQUE (ios_id),
    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE
);



-- -------------------------------------------------------------------- RELATIONSHIPS
CREATE TABLE follows (
    fk_user_id1 INTEGER NOT NULL,
    fk_user_id2 INTEGER NOT NULL,
    start_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    CONSTRAINT fk_follow_user_id1 FOREIGN KEY (fk_user_id1) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    CONSTRAINT fk_follow_user_id2 FOREIGN KEY (fk_user_id2) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    CONSTRAINT pk_follow PRIMARY KEY (fk_user_id1, fk_user_id2)
);


CREATE TABLE friendships (
    fk_user_id1 INTEGER NOT NULL,
    fk_user_id2 INTEGER NOT NULL,
    start_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    FOREIGN KEY (fk_user_id1) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_user_id2) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    PRIMARY KEY (fk_user_id1, fk_user_id2)
);

CREATE TABLE friendship_requests (
    fk_user_id1 INTEGER NOT NULL,
    fk_user_id2 INTEGER NOT NULL,
    request_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    FOREIGN KEY (fk_user_id1) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_user_id2) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    PRIMARY KEY (fk_user_id1, fk_user_id2)
);


-- -------------------------------------------------------------------- WORKS AT
CREATE TABLE workplace (
    pk_workplace_id SERIAL,
    name CHARACTER VARYING(255) NOT NULL,
    PRIMARY KEY (pk_workplace_id),
    UNIQUE (name)
);

CREATE TABLE works_at (
    fk_user_id INTEGER NOT NULL,
    fk_workplace_id INTEGER NOT NULL,
    date_start TIMESTAMP WITHOUT TIME ZONE NOT NULL,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_workplace_id) REFERENCES workplace(pk_workplace_id),
    PRIMARY KEY (fk_user_id, fk_workplace_id)
);

-- Enable PostGIS (includes raster)
CREATE EXTENSION postgis;
-- Enable Topology
CREATE EXTENSION postgis_topology;
-- -------------------------------------------------------------------- CITIES
CREATE TABLE countries (
    iso2 CHARACTER VARYING(2) UNIQUE NOT NULL,
    iso3 CHARACTER VARYING(3) UNIQUE NOT NULL,
    name CHARACTER VARYING(40) UNIQUE NOT NULL,
    geoname_id INTEGER UNIQUE NOT NULL,
    phone_code CHARACTER VARYING(30) NOT NULL
);

CREATE TABLE cities (
    pk_city_id BIGINT, -- geocode ID, NOT a SERIAL !
    name CHARACTER VARYING(255) NOT NULL,
    latitude REAL NOT NULL,
    longitude REAL NOT NULL,
    country CHARACTER VARYING(2) NOT NULL,
    PRIMARY KEY (pk_city_id)
    --UNIQUE (name)
);

-- city names coming from the devices
CREATE TABLE devcities (
    pk_devcity_id SERIAL,
    locality CHARACTER VARYING(255) NOT NULL,
    country_code CHARACTER VARYING(2) NOT NULL,
    fk_city_id INTEGER,
    PRIMARY KEY (pk_devcity_id),
    FOREIGN KEY (fk_city_id) REFERENCES cities(pk_city_id)
    --UNIQUE (name)
);

--CREATE TABLE lives_in (
--    fk_user_id INTEGER NOT NULL,
--    fk_city_id INTEGER NOT NULL,
--    date_start TIMESTAMP WITHOUT TIME ZONE NOT NULL,
--
--    PRIMARY KEY (fk_user_id, fk_city_id),
--    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
--    FOREIGN KEY (fk_city_id) REFERENCES cities(pk_city_id)
--);

-- -------------------------------------------------------------------- POSTS

CREATE TABLE posts (
    pk_post_id BIGSERIAL,
    creation_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
    body VARCHAR(512),

    fk_media_id BIGINT,
    fk_media_vid_id BIGINT,

    from_fk_user_id INTEGER NOT NULL,
    to_fk_user_id INTEGER,

    fk_devcity_id BIGINT,
    nb_comments INTEGER DEFAULT(0),

    PRIMARY KEY (pk_post_id),
    FOREIGN KEY (fk_devcity_id) REFERENCES devcities(pk_devcity_id) ON DELETE SET NULL,
    FOREIGN KEY (fk_media_id) REFERENCES medias(pk_media_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_media_vid_id) REFERENCES medias(pk_media_id) ON DELETE CASCADE,
    FOREIGN KEY (from_fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (to_fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE
);



CREATE TABLE flows (
    fk_user_id INT NOT NULL,
    fk_post_id BIGINT NOT NULL,

    PRIMARY KEY (fk_user_id, fk_post_id)--,
    --FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    --FOREIGN KEY (fk_post_id) REFERENCES posts(pk_post_id) ON DELETE CASCADE
);

CREATE TABLE comments (
	pk_comment_id BIGSERIAL,
	creation_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
	body VARCHAR(512) NOT NULL,

	fk_post_id BIGINT NOT NULL,
	from_fk_user_id INTEGER,

	PRIMARY KEY (pk_comment_id),
	FOREIGN KEY (fk_post_id) REFERENCES posts(pk_post_id) ON DELETE CASCADE,
	FOREIGN KEY (from_fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE
);
-- -------------------------------------------------------------------- HASHTAGS

CREATE TABLE htags (
    pk_htag_id BIGSERIAL,
    tag CHARACTER VARYING(255) NOT NULL,

    PRIMARY KEY (pk_htag_id),
    UNIQUE (tag)
);

-- a post has 0-n htags
-- a htag belongs to 0-n posts
-- same htag may be used several times in a given post
CREATE TABLE posts_htags (
    fk_post_id BIGINT NOT NULL,
    ck_seq_id INTEGER NOT NULL,

    fk_htag_id BIGINT NOT NULL,
    pop INTEGER NOT NULL DEFAULT(0),

    FOREIGN KEY (fk_post_id) REFERENCES posts(pk_post_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_htag_id) REFERENCES htags(pk_htag_id),
    PRIMARY KEY (fk_post_id, ck_seq_id)
);




-- -------------------------------------------------------------------- HASHTAG LIKES
-- when someone "likes" or "does not like any more" a htag in a post

CREATE TABLE htags_likes (
    fk_user_id INTEGER NOT NULL,
    fk_post_id BIGINT NOT NULL,
    ck_seq_id INTEGER NOT NULL,

    --fk_htag_id BIGINT NOT NULL,
    begin_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
--    end_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_post_id, ck_seq_id) REFERENCES posts_htags(fk_post_id, ck_seq_id) ON DELETE CASCADE,
--    PRIMARY KEY (fk_user_id, fk_post_id, ck_seq_id, begin_ts)
    PRIMARY KEY (fk_user_id, fk_post_id, ck_seq_id)
);




-- -------------------------------------------------------------------- TRENDS
CREATE TABLE user_long_term_trends_detail (
    fk_user_id INTEGER NOT NULL,
    ck_seq_id INTEGER NOT NULL, -- 1..52

    fk_htag_id BIGINT,
    pop INTEGER,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_htag_id) REFERENCES htags(pk_htag_id),
    PRIMARY KEY (fk_user_id,ck_seq_id)
);

CREATE TABLE user_long_term_trends_summary (
    fk_user_id INTEGER NOT NULL,
    ck_seq_id INTEGER NOT NULL, -- 1..5

    fk_htag_id BIGINT,
    pop INTEGER,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_htag_id) REFERENCES htags(pk_htag_id),
    PRIMARY KEY (fk_user_id,ck_seq_id)
);

CREATE TABLE user_long_term_trends (
    fk_user_id INTEGER NOT NULL,
    fk_htag_id BIGINT,
    pop INTEGER NOT NULL,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_htag_id) REFERENCES htags(pk_htag_id),
    PRIMARY KEY (fk_user_id,fk_htag_id)
);


CREATE TABLE user_recent_trends (
    fk_user_id INTEGER NOT NULL,
    fk_htag_id BIGINT,
    pop INTEGER NOT NULL,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_htag_id) REFERENCES htags(pk_htag_id),
    PRIMARY KEY (fk_user_id,fk_htag_id)
);


-- -------------------------------------------------------------------- HASHTAG PROFILE LIKES
-- when someone "likes" or "does not like any more" a htag in a user's trends

--CREATE TABLE htags_long_term_trends_likes (
--    fk_user_id INTEGER NOT NULL,
--    fk_user_id_profile INTEGER NOT NULL,
--    fk_htag_id BIGINT NOT NULL,
--
--    begin_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
--    end_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
--
--    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
--    FOREIGN KEY (fk_user_id_profile,fk_htag_id) REFERENCES user_long_term_trends(fk_user_id,fk_htag_id) ON DELETE CASCADE
--);
--
--CREATE TABLE htags_recent_trends_likes (
--    fk_user_id INTEGER NOT NULL,
--    fk_user_id_profile INTEGER NOT NULL,
--    fk_htag_id BIGINT NOT NULL,
--
--    begin_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
--    end_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,
--
--    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
--    FOREIGN KEY (fk_user_id_profile,fk_htag_id) REFERENCES user_recent_trends(fk_user_id,fk_htag_id) ON DELETE CASCADE
--);



-- -------------------------------------------------------------------- PASSWORD RESETS
CREATE TABLE password_resets(
    fk_user_id INTEGER NOT NULL,
    token CHARACTER VARYING(64) NOT NULL,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE
);

-- -------------------------------------------------------------------- EMAIL CONFIRMATIONS
CREATE TABLE email_confirmations(
    fk_user_id INTEGER NOT NULL,
    token CHARACTER VARYING(64) NOT NULL,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE
);

-- -------------------------------------------------------------------- PHONE CONFIRMATIONS
CREATE TABLE phone_confirmations(
    fk_user_id INTEGER NOT NULL,
    code SMALLINT NOT NULL,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE
);


-- -------------------------------------------------------------------- REPORTS

CREATE TABLE reports_posts(
    fk_post_id BIGINT NOT NULL,
    fk_user_id INTEGER NOT NULL,
    ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_post_id) REFERENCES posts(pk_post_id) ON DELETE CASCADE,
    PRIMARY KEY(fk_user_id, fk_post_id)
);

CREATE TABLE reports_comments(
    fk_comment_id BIGINT NOT NULL,
    fk_user_id INTEGER NOT NULL,
    ts TIMESTAMP WITHOUT TIME ZONE NOT NULL,

    FOREIGN KEY (fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_comment_id) REFERENCES comments(pk_comment_id) ON DELETE CASCADE,
    PRIMARY KEY(fk_user_id, fk_comment_id)
);

