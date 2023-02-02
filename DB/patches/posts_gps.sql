ALTER TABLE posts ADD COLUMN geo GEOGRAPHY(POINT, 4326);
ALTER TABLE pending_posts ADD COLUMN geo GEOGRAPHY(POINT, 4326);

UPDATE posts p
  SET geo = cc.geo
  FROM (
    SELECT dc.pk_devcity_id, c.geo FROM cities c
      JOIN devcities dc ON dc.fk_city_id = c.pk_city_id
  ) cc
  WHERE
      p.fk_devcity_id IS NOT NULL
  AND cc.geo IS NOT NULL
  AND cc.pk_devcity_id = p.fk_devcity_id
  ;



CREATE OR REPLACE FUNCTION posts_create(
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
    RAISE NOTICE 'lat %  lon %', _lat, _lon;
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
CREATE OR REPLACE FUNCTION post_create_pending(
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
    RAISE NOTICE 'lat %  lon %', _lat, _lon;
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
CREATE OR REPLACE FUNCTION post_create_pending_user(
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
