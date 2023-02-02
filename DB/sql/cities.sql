
COPY countries FROM '/srv/Tempr/DB/sql/countries.dump' (DELIMITER '|');

COPY cities FROM '/srv/Tempr/DB/sql/cities.dump' (DELIMITER '|');

ALTER TABLE cities ADD COLUMN
    geo GEOGRAPHY(POINT, 4326) DEFAULT(ST_GeomFromText('POINT(0 0)', 4326));

UPDATE cities SET geo = ST_MakePoint(longitude, latitude);

CREATE INDEX cities_geo_idx ON cities USING GIST ( geo );



CREATE FUNCTION cities_get(
    _lat REAL,
    _lon REAL
)
RETURNS TABLE(pk_city_id BIGINT,name CHARACTER VARYING(255), country CHARACTER VARYING(2), km FLOAT)
AS $$
    SELECT cities.pk_city_id, cities.name, cities.country, ST_Distance(geo, poi)/1000 AS km
    FROM cities,
        (select ST_MakePoint(_lon,_lat)::geography as poi) as poi -- Warning ST_MakePoint takes (longitude,latitude) as params
    WHERE ST_DWithin(geo, poi, 10000)
    ORDER BY ST_Distance(geo, poi)
    LIMIT 10;
$$ LANGUAGE sql STABLE;


CREATE FUNCTION city_get(
    _lat REAL,
    _lon REAL
)
RETURNS TABLE(pk_city_id BIGINT,name CHARACTER VARYING(255), country CHARACTER VARYING(2), km FLOAT)
AS $$
    SELECT cities.pk_city_id, cities.name, cities.country, ST_Distance(geo, poi)/1000 AS km
    FROM cities,
        (select ST_MakePoint(_lon,_lat)::geography as poi) as poi -- Warning ST_MakePoint takes (longitude,latitude) as params
    WHERE ST_DWithin(geo, poi, 10000)
    ORDER BY ST_Distance(geo, poi)
    LIMIT 1;
$$ LANGUAGE sql STABLE;



CREATE FUNCTION devcity_create(
    _locality character varying,
    _country_code character varying
)
RETURNS INTEGER
AS $$
DECLARE
    id INTEGER;
BEGIN
    LOCK TABLE devcities IN EXCLUSIVE MODE;
    SELECT pk_devcity_id INTO id FROM devcities WHERE locality = _locality AND country_code = _country_code;
    IF NOT found THEN
        INSERT INTO devcities(locality,country_code) VALUES(_locality,_country_code) RETURNING pk_devcity_id INTO id;
    END IF;
    RETURN id;
END
$$ LANGUAGE plpgsql;

--
--CREATE FUNCTION devcity_add(
--    _post_id BIGINT,
--    _tag character varying,
--    _seq_id INT
--)
--RETURNS void
--AS $$
--BEGIN
--    IF _tag != '' THEN
--       INSERT INTO posts_htags(fk_post_id, fk_htag_id, ck_seq_id)
--           SELECT _post_id,
--                  htags_create(_tag),
--                  _seq_id;
--    END IF;
--END
--$$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION link_cities(
    _devcityid integer,
    _cityid integer
)
RETURNS void
AS $$
    UPDATE devcities SET fk_city_id = _cityid WHERE pk_devcity_id = _devcityid;
$$ LANGUAGE sql;


