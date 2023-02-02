
CREATE VIEW view_user_recent_trends AS
    SELECT urt.fk_user_id, urt.fk_htag_id, urt.pop, h.tag
        FROM user_recent_trends urt
        JOIN htags h ON h.pk_htag_id = urt.fk_htag_id;

CREATE VIEW view_user_long_term_trends AS
    SELECT ultts.fk_user_id, ultts.fk_htag_id, ultts.pop, h.tag
        FROM user_long_term_trends_summary ultts
        JOIN htags h ON h.pk_htag_id = ultts.fk_htag_id;

CREATE VIEW view_profiles AS
    SELECT u.*,
           m_profile.filename AS filename_profile,
           m_background.filename AS filename_background
        FROM users u
            LEFT JOIN medias m_profile ON m_profile.pk_media_id = u.fk_media_id_profile
            LEFT JOIN medias m_background ON m_background.pk_media_id = u.fk_media_id_background;

CREATE VIEW view_profiles_small AS
    SELECT pk_user_id,
           FALSE AS is_full,
           firstname,
           lastname,
           filename_profile
        FROM view_profiles;

CREATE TYPE profile_data_big_t AS (
    pk_user_id INT,
    is_full BOOLEAN,
    firstname CHARACTER VARYING,
    lastname CHARACTER VARYING,
    filename_profile CHARACTER VARYING,
    filename_background CHARACTER VARYING,
    city CHARACTER VARYING,
    likes INTEGER
);



CREATE FUNCTION profile_get_data_big_(
    _user_id INTEGER
)
RETURNS profile_data_big_t
AS $$
DECLARE
    result_ profile_data_big_t;
BEGIN
    SELECT pk_user_id, TRUE, firstname, lastname, filename_profile, filename_background, city, likes
        INTO result_.pk_user_id, result_.is_full, result_.firstname, result_.lastname, result_.filename_profile, result_.filename_background, result_.city, result_.likes
        FROM view_profiles
        WHERE pk_user_id = _user_id;
    return result_;
END
$$ LANGUAGE plpgsql STABLE;





-- get the profile of a user
-- for instance when you click on his name after a search
CREATE FUNCTION profile_get(
    _user_id INTEGER,
    _other_user_id INTEGER
)
RETURNS profile_data_big_t
AS $$
DECLARE
    dummy_ INTEGER;
    result_ profile_data_big_t;
BEGIN
	IF _user_id = _other_user_id THEN
        SELECT * INTO result_
            FROM profile_get_data_big_(_other_user_id);
        RETURN result_;
	ELSE
	    SELECT 1 FROM friendships WHERE fk_user_id1=_user_id AND fk_user_id2=_other_user_id
	    UNION ALL
	    SELECT 1 FROM users WHERE pk_user_id=_other_user_id AND NOT private
	    INTO dummy_;
	    IF found THEN
            SELECT * INTO result_
                FROM profile_get_data_big_(_other_user_id);
	        RETURN result_;
        END IF;
	END IF;
    SELECT * INTO result_
--	    FROM profile_get_data_small_(_other_user_id);
        FROM view_profiles_small
        WHERE pk_user_id = _other_user_id;
    result_.is_full = FALSE;
	RETURN result_;
END
$$ LANGUAGE plpgsql STABLE;


-- get my own profile
-- used by the app to retrieve private information
-- used by the dashboard too
CREATE FUNCTION profile_get(
    _user_id INTEGER
)
RETURNS SETOF view_profiles
AS $$
BEGIN
    RETURN QUERY SELECT *
        FROM view_profiles
        WHERE pk_user_id = _user_id;
END
$$ LANGUAGE plpgsql STABLE;



--
CREATE OR REPLACE FUNCTION profile_friends_of(
    _user_id INTEGER,
    _start INTEGER
)
RETURNS SETOF view_profiles_small
AS $$
    SELECT vp.*
        FROM view_profiles_small vp
        JOIN friendships f ON f.fk_user_id2 = vp.pk_user_id
        WHERE f.fk_user_id1 = _user_id
        ORDER BY firstname, lastname
        -- LIMIT 10   -- TODO FRED retablir cette limite
        OFFSET _start;
$$ LANGUAGE sql STABLE;


-- les medias d'un user (ceux qu'on a postés sur lui)
-- récents = les 6 derniers
CREATE OR REPLACE FUNCTION profile_get_recent_medias(
    _user_id INT
)
RETURNS TABLE(
    fk_post_id BIGINT,
    creation_ts TIMESTAMP WITHOUT TIME ZONE,
    filename CHARACTER VARYING
)
AS
$$
    SELECT p.pk_post_id AS fk_post_id, p.creation_ts, m.filename
        FROM posts p
        JOIN users u ON p.to_fk_user_id = u.pk_user_id
        JOIN medias m ON p.fk_media_id = m.pk_media_id
        WHERE
            p.to_fk_user_id = _user_id
        ORDER BY p.creation_ts DESC
        LIMIT 6;
$$ LANGUAGE sql STABLE;
