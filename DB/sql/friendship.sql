
CREATE FUNCTION friendship_requests_get(
    _pk_user_id INT
)
RETURNS SETOF friendship_requests
AS $$
    SELECT * FROM friendship_requests
        WHERE fk_user_id2 = _pk_user_id;
$$ LANGUAGE sql STABLE;


CREATE FUNCTION friendship_request_create(
    _pk_user_id1 INT,
    _pk_user_id2 INT
)
RETURNS event_and_data_type_t -- see events.sql for type definition
AS $$
DECLARE
    res_ event_and_data_type_t;
BEGIN
    --RAISE WARNING 'friendship_request_create % -> %', _pk_user_id1, _pk_user_id2;
    -- can not be friend with self
	IF _pk_user_id1 = _pk_user_id2 THEN
        res_.event_type = 'friendship_denied';
        --RAISE WARNING 'friendship_request_create RETURNS %',res_.event_type; 
        RETURN res_;
    END IF;

	LOCK TABLE friendship_requests IN EXCLUSIVE MODE;
	-- search for corresponding friendship
    IF EXISTS(
        SELECT 1 FROM friendships
            WHERE fk_user_id1 = _pk_user_id1
                AND fk_user_id2 = _pk_user_id2
    ) THEN
        res_.event_type = 'already_friends';
	    res_.event_data = _pk_user_id1;
	    res_.event_data2 = _pk_user_id2;
        --RAISE WARNING 'friendship_request_create RETURNS %',res_.event_type; 
        RETURN res_;
    END IF;

	-- search for reciprocal request
	DELETE FROM friendship_requests
	    WHERE fk_user_id2 = _pk_user_id1
            AND fk_user_id1 = _pk_user_id2;
    IF found THEN
        -- create friendship
        --RAISE WARNING 'friendship_request_create RETURNS call to friendship_create(%,%)',_pk_user_id1,_pk_user_id2; 
        RETURN friendship_create(_pk_user_id1,_pk_user_id2);
    ELSE
        INSERT INTO friendship_requests(fk_user_id1,fk_user_id2,request_ts)
            SELECT _pk_user_id1,_pk_user_id2,NOW()
            WHERE NOT EXISTS(
                SELECT 1 FROM friendship_requests
                    WHERE fk_user_id1 = _pk_user_id1
                        AND fk_user_id2 = _pk_user_id2
            );
        -- PERFORM event_create_friendship_request(_pk_user_id1, _pk_user_id2);
        res_.event_type = 'friendship_request';
        res_.event_data = _pk_user_id1;
        res_.event_data2 = _pk_user_id2;
        --RAISE WARNING 'friendship_request_create RETURNS %',res_.event_type; 
        RETURN res_;
    END IF;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION friendship_request_refuse(
    _pk_user_id1 INT,
    _pk_user_id2 INT
)
RETURNS event_and_data_type_t -- see events.sql for type definition
AS $$
DECLARE
    res_ event_and_data_type_t;
BEGIN
    -- search for reciprocal request
    DELETE FROM friendship_requests
        WHERE fk_user_id2 = _pk_user_id1
            AND fk_user_id1 = _pk_user_id2;
    IF found THEN
        res_.event_type = 'friendship_refusal';
        res_.event_data = _pk_user_id1;
        res_.event_data2 = _pk_user_id2;
    ELSE
        res_.event_type = 'N/A';
    END IF;
    RETURN res_;
END
$$ LANGUAGE plpgsql;



CREATE FUNCTION friendship_create(
    _pk_user_id1 INT,
    _pk_user_id2 INT
)
RETURNS event_and_data_type_t -- see events.sql for type definition
AS $$
DECLARE
    res_ event_and_data_type_t;
BEGIN
	-- can not be friend with self
    IF _pk_user_id1 = _pk_user_id2 THEN
        res_.event_type = 'N/A';
        res_.event_data = 0;
        res_.event_data2 = 0;
        --RAISE WARNING 'friendship_create RETURNS % % %',res_.event_type, res_.event_data, res_.event_data2; 
        RETURN res_;
    END IF;
    -- do not create a friendship that already exists
    IF EXISTS(
        SELECT 1 FROM friendships
            WHERE fk_user_id1 = _pk_user_id1
                AND fk_user_id2 = _pk_user_id2
    ) THEN
        res_.event_type = 'already_friends';
        res_.event_data = _pk_user_id1;
        res_.event_data2 = _pk_user_id2;
        --RAISE WARNING 'friendship_create RETURNS % % %',res_.event_type, res_.event_data, res_.event_data2; 
        RETURN res_;
    END IF;

	INSERT INTO friendships(fk_user_id1, fk_user_id2, start_ts)
        VALUES
	        (_pk_user_id1, _pk_user_id2, NOW()),
	        (_pk_user_id2, _pk_user_id1, NOW());
    -- PERFORM flow_update_for_new_friendship(_pk_user_id1, _pk_user_id2);
    DELETE FROM friendship_requests
        WHERE (fk_user_id2 = _pk_user_id1 AND fk_user_id1 = _pk_user_id2)
           OR (fk_user_id2 = _pk_user_id2 AND fk_user_id1 = _pk_user_id1);
    -- PERFORM event_create_friendship_acceptance(_pk_user_id1, _pk_user_id2);
    res_.event_type = 'friendship_acceptance';
    res_.event_data = _pk_user_id1;
    res_.event_data2 = _pk_user_id2;
    --RAISE WARNING 'friendship_create RETURNS % % %',res_.event_type, res_.event_data, res_.event_data2; 
    RETURN res_;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION friendship_delete(
    _pk_user_id1 INT,
    _pk_user_id2 INT
)
RETURNS event_and_data_type_t -- see events.sql for type definition
AS $$
DECLARE
    res_ event_and_data_type_t;
BEGIN
	DELETE FROM friendships
	   WHERE
	       (fk_user_id1 = _pk_user_id1 AND fk_user_id2 = _pk_user_id2)
	    OR (fk_user_id1 = _pk_user_id2 AND fk_user_id2 = _pk_user_id1);
	IF found THEN
--	    PERFORM flow_update_for_friendship_removal(_pk_user_id1, _pk_user_id2);
	    res_.event_type = 'friendship_removal';
	    res_.event_data = _pk_user_id1;
	    res_.event_data2 = _pk_user_id2;
    ELSE
        res_.event_type = 'N/A';
        res_.event_data = 0;
        res_.event_data2 = 0;
    END IF;
    RETURN res_;
END
$$ LANGUAGE plpgsql;
