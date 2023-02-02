
CREATE FUNCTION media_create(
    _filename CHARACTER VARYING
)
RETURNS BIGINT
AS $$
    INSERT INTO medias (filename)
        VALUES (_filename)
        RETURNING pk_media_id;
$$ LANGUAGE sql;


CREATE FUNCTION media_confirm(
    _media_id BIGINT
)
RETURNS boolean
AS $$
BEGIN
    UPDATE medias
        SET creation_ts = NOW()
        WHERE pk_media_id = _media_id;
    RETURN found;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION media_delete(
    _media_id BIGINT
)
RETURNS boolean
AS $$
BEGIN
    DELETE FROM medias WHERE pk_media_id = _media_id;
    RETURN found;
END
$$ LANGUAGE plpgsql;
