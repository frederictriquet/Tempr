-- -------------------------------------------------------------------- EVENTS
-- DROP TYPE IF EXISTS event_type_t CASCADE;
CREATE TYPE event_type_t AS ENUM(
    'N/A',
    'friendship_denied','already_friends','friendship_request',
    'friendship_acceptance','friendship_removal',
    'friendship_refusal',
    'post','pending_post','post_delete','user_delete',
    'post_by_phone',
    'comment','like','i_am_liked','they_like_it',
    'profile_updated','background_updated',
    'stats_updated',
    'sysmsg',
    'phone_new','phone_confirmed','fb_connected',
    'user_deleted'
);
CREATE TYPE event_and_data_type_t AS (
    event_type event_type_t,
    event_data BIGINT,
    event_data2 BIGINT
);

-- DROP TABLE IF EXISTS events;
CREATE TABLE events (
    pk_event_id BIGSERIAL NOT NULL PRIMARY KEY,
    creation_ts TIMESTAMP WITHOUT TIME ZONE NOT NULL DEFAULT(NOW()),
    type event_type_t NOT NULL,

    from_fk_user_id INTEGER,
    to_fk_user_id INTEGER,
    fk_post_id BIGINT,
    body CHARACTER VARYING(255),

    FOREIGN KEY (from_fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (to_fk_user_id) REFERENCES users(pk_user_id) ON DELETE CASCADE,
    FOREIGN KEY (fk_post_id) REFERENCES posts(pk_post_id) ON DELETE CASCADE
);

CREATE VIEW view_events_with_users AS
    SELECT e.*,
        u1.firstname AS from_firstname, u1.lastname AS from_lastname, m1.filename AS from_profile,
        u2.firstname AS to_firstname, u2.lastname AS to_lastname, m2.filename AS to_profile
        FROM events e
        LEFT JOIN users u1 ON u1.pk_user_id = e.from_fk_user_id
            LEFT JOIN medias m1 on m1.pk_media_id = u1.fk_media_id_profile
        LEFT JOIN users u2 ON u2.pk_user_id = e.to_fk_user_id
            LEFT JOIN medias m2 on m2.pk_media_id = u2.fk_media_id_profile
    ;

CREATE VIEW view_events_old_friendship_requests AS
    WITH summary AS (
    SELECT e.*,
           ROW_NUMBER() OVER(PARTITION BY e.to_fk_user_id
                                 ORDER BY e.creation_ts ASC) AS rk
      FROM events e
      WHERE type='friendship_request'
          AND creation_ts < NOW() - INTERVAL '1week'
      )
    SELECT s.*
    FROM summary s
    WHERE s.rk = 1;



CREATE OR REPLACE FUNCTION event_create(
    _type event_type_t,
    _from_id INTEGER,
    _to_id INTEGER,
    _post_id BIGINT
)
RETURNS void
AS $$
DECLARE
    id_ BIGINT;
BEGIN
	-- TODO utiliser le ON CONFLICT
	-- ATTENTION :   where x=NULL  ne marche pas
	-- IS NOT DISTINCT FROM marche comme voulu mais est "moins performant" qu'un =
	-- voir pour construire la requete en fonction des params null
    LOCK TABLE events IN EXCLUSIVE MODE;
    SELECT pk_event_id INTO id_ FROM events WHERE
        type = _type
        AND from_fk_user_id IS NOT DISTINCT FROM _from_id
        AND to_fk_user_id IS NOT DISTINCT FROM _to_id
        AND fk_post_id IS NOT DISTINCT FROM _post_id;
    IF found THEN
        --RAISE WARNING 'FOUND (%) % % % %', id_, _type, _from_id, _to_id, _post_id;
        UPDATE events SET creation_ts = NOW() WHERE pk_event_id = id_;
    ELSE
        --RAISE WARNING 'NOT FOUND % % % %', _type, _from_id, _to_id, _post_id;
        INSERT INTO events(type, from_fk_user_id, to_fk_user_id, fk_post_id)
            VALUES (_type, _from_id, _to_id, _post_id);
    END IF;
END
$$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION event_remove(
    _type event_type_t,
    _from_id INTEGER,
    _to_id INTEGER
)
RETURNS void
AS $$
    DELETE FROM events
      WHERE type = _type
      AND from_fk_user_id = _from_id
      AND to_fk_user_id = _to_id;
$$ LANGUAGE sql;


CREATE FUNCTION events_get(
    _user_id INTEGER,
    _until_ts TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
)
RETURNS SETOF view_events_with_users
AS $$
    SELECT *
        FROM view_events_with_users
        WHERE to_fk_user_id = _user_id
            AND creation_ts < _until_ts
        ORDER BY creation_ts DESC
        LIMIT 5;
$$ LANGUAGE sql STABLE;

CREATE FUNCTION events_get_up(
    _user_id INTEGER,
    _from_ts TIMESTAMP WITHOUT TIME ZONE DEFAULT NOW()
)
RETURNS SETOF view_events_with_users
AS $$
    WITH t AS (
        SELECT *
            FROM view_events_with_users
            WHERE to_fk_user_id = _user_id
                AND creation_ts > _from_ts
            ORDER BY creation_ts ASC
            LIMIT 5
    )
    SELECT * FROM t ORDER BY creation_ts DESC;
$$ LANGUAGE sql STABLE;


CREATE FUNCTION events_exist(
    _user_id INTEGER,
    _ts TIMESTAMP WITHOUT TIME ZONE
)
RETURNS BOOLEAN
AS $$
    SELECT EXISTS (
        SELECT 1 FROM events
            WHERE to_fk_user_id = _user_id
            AND creation_ts > _ts
    );
$$ LANGUAGE sql STABLE;

