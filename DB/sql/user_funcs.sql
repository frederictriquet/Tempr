
CREATE TYPE user_creation_return_t AS (created BOOLEAN, pk_user_id INT, login CHARACTER VARYING);

CREATE FUNCTION user_create(
    _login character varying,
    _email character varying,
    _firstname character varying,
    _lastname character varying,
    _password character varying
)
RETURNS user_creation_return_t
AS $$
DECLARE
    code_ INTEGER;
    result_ user_creation_return_t;
    user_id_ INTEGER := 0;
    existing_id_ INTEGER := 0;
    login_to_use_ CHARACTER VARYING;
    login_is_ok_ BOOLEAN := false;
    login_counter_ INTEGER := 2;
BEGIN
	LOCK TABLE users IN EXCLUSIVE MODE;
    -- try _login
    login_to_use_ := _login;
    login_is_ok_ := NOT EXISTS(SELECT 1 FROM users WHERE login = login_to_use_);
    WHILE NOT login_is_ok_ LOOP
        login_to_use_ := _login || '_' || (login_counter_::text);
        login_is_ok_ := NOT EXISTS(SELECT 1 FROM users WHERE login = login_to_use_);
        login_counter_ := login_counter_ + 1;
    END LOOP;
    BEGIN
	    INSERT INTO users(login,email,firstname,lastname,password)
	        VALUES(login_to_use_,_email,_firstname,_lastname,_password)
            RETURNING pk_user_id INTO user_id_;
        result_.created = TRUE;
        result_.pk_user_id = user_id_;
        result_.login = login_to_use_;
        PERFORM trends_user_init(user_id_);
    EXCEPTION WHEN unique_violation THEN
        result_.created = FALSE;
    END;
    RETURN result_;
END
$$ LANGUAGE plpgsql;







CREATE FUNCTION user_fb_create(
    _login character varying,
    _firstname character varying,
    _lastname character varying,
    _facebook_id character varying
)
RETURNS user_creation_return_t
AS $$
DECLARE
    code_ INTEGER;
    result_ user_creation_return_t;
    user_id_ INTEGER := 0;
    existing_id_ INTEGER := 0;
    login_to_use_ CHARACTER VARYING;
    login_is_ok_ BOOLEAN := false;
    login_counter_ INTEGER := 2;
BEGIN
    LOCK TABLE users IN EXCLUSIVE MODE;
    -- try _login
    login_to_use_ := _login;
    login_is_ok_ := NOT EXISTS(SELECT 1 FROM users WHERE login = login_to_use_);
    WHILE NOT login_is_ok_ LOOP
        login_to_use_ := _login || '_' || (login_counter_::text);
        login_is_ok_ := NOT EXISTS(SELECT 1 FROM users WHERE login = login_to_use_);
        login_counter_ := login_counter_ + 1;
    END LOOP;
    BEGIN
        INSERT INTO users(login,firstname,lastname,facebook_id)
            VALUES(login_to_use_,_firstname,_lastname,_facebook_id)
            RETURNING pk_user_id INTO user_id_;
        result_.created = TRUE;
        result_.pk_user_id = user_id_;
        result_.login = login_to_use_;
        PERFORM trends_user_init(user_id_);
    EXCEPTION WHEN unique_violation THEN
        result_.created = FALSE;
    END;
    RETURN result_;
END
$$ LANGUAGE plpgsql;






CREATE FUNCTION user_add_iosdevice(
    _user_id INT,
    _ios_id CHARACTER VARYING
)
RETURNS VOID
AS $$
    INSERT INTO iosdevices(ios_id, fk_user_id)
        SELECT _ios_id, _user_id
        WHERE
            NOT EXISTS (
                SELECT 1 FROM iosdevices WHERE ios_id = _ios_id
            );
$$ LANGUAGE sql;


CREATE FUNCTION user_remove_iosdevice(
    _user_id INTEGER,
    _ios_id character varying
)
RETURNS void
AS $$
    DELETE FROM iosdevices
        WHERE ios_id = _ios_id
          AND fk_user_id = _user_id;
$$ LANGUAGE sql;





--CREATE TYPE user_login_return_t AS (logged_in BOOLEAN, pk_user_id INTEGER, token character varying, refresh_token character varying);
--
--
--CREATE FUNCTION user_login(
--    _user_id INTEGER,
--    _desired_token character varying,
--    _desired_refresh_token character varying
--)
--RETURNS user_login_return_t
--AS $$
--DECLARE
--    result_ user_login_return_t;
--    current_token_ character varying;
--    current_refresh_token_ character varying;
--BEGIN
--    result_.logged_in = TRUE;
--    result_.pk_user_id = _user_id;
--    -- PERFORM user_set_device(_user_id, _device);
--    LOCK TABLE oauth_tokens IN EXCLUSIVE MODE;
--    SELECT token, refresh_token
--        FROM oauth_tokens
--        WHERE fk_user_id = _user_id
--        INTO current_token_, current_refresh_token_;
--    IF NOT FOUND THEN
--        INSERT INTO oauth_tokens(token, refresh_token, fk_user_id, valid_until_ts)
--            VALUES(_desired_token, _desired_refresh_token, _user_id, NOW()+'1day');
--        result_.token = _desired_token;
--        result_.refresh_token = _desired_refresh_token;
--    ELSE
--        UPDATE oauth_tokens
--            SET valid_until_ts = NOW()+'1day',
--                token = current_token_
--            WHERE
--                fk_user_id = _user_id;
--        result_.token = current_token_;
--        result_.refresh_token = current_refresh_token_;
--    END IF;
--    -- RAISE WARNING 'token   %', result.token;
--    -- RAISE WARNING 'refresh %', result.refresh_token;
--    RETURN result_;
--END
--$$ LANGUAGE plpgsql;





--CREATE FUNCTION user_fb_login(
--    _facebook_id character varying,
--    _desired_token character varying,
--    _desired_refresh_token character varying
--)
--RETURNS user_login_return_t
--AS $$
--DECLARE
--    user_id_ INTEGER;
--    result_ user_login_return_t;
--BEGIN
--    SELECT pk_user_id INTO user_id_ FROM users WHERE facebook_id=_facebook_id;
--    IF NOT found THEN
--        result_.logged_in = FALSE;
--        result_.token = 'invalid token';
--        result_.refresh_token = 'invalid token';
--        --RAISE WARNING 'login failed %',_login;
--        RETURN result_;
--    END IF;
--
--    SELECT * FROM user_login(
--        user_id_,
--        _desired_token,
--        _desired_refresh_token
--    ) INTO result_;
--    RETURN result_;
--END
--$$ LANGUAGE plpgsql;



--CREATE FUNCTION user_check_token_and_postpone(
--    _token character varying
--)
--RETURNS INTEGER
--AS $$
--DECLARE
--    user_id_ INTEGER;
--    ts_ TIMESTAMP WITHOUT TIME ZONE;
--BEGIN
--	SELECT fk_user_id FROM oauth_tokens WHERE token = _token
--            AND valid_until_ts >= NOW()
--            INTO user_id_;
--    --RAISE WARNING 'user id %',user_id_;
--    IF NOT found THEN user_id_ = 0; END IF;
--    --RAISE WARNING 'after user id %',user_id_;
--    SELECT valid_until_ts INTO ts_ FROM oauth_tokens WHERE token = _token;
--    UPDATE oauth_tokens
--        SET valid_until_ts = NOW()+'1day'
--        WHERE
--            token = _token
--            AND valid_until_ts >= NOW();
--    --RAISE WARNING 'return user id %',user_id_;
--    RETURN user_id_;
--END
--$$ LANGUAGE plpgsql;



--CREATE FUNCTION user_logout(
--    _token character varying
--)
--RETURNS boolean
--AS $$
--BEGIN
--    DELETE FROM oauth_tokens
--        WHERE
--            token=_token;
--    RETURN found;
--END
--$$ LANGUAGE plpgsql;


--CREATE FUNCTION user_renew_token(
--    _refresh_token character varying,
--    _desired_token character varying
--)
--RETURNS boolean
--AS $$
--BEGIN
--	UPDATE oauth_tokens
--	   SET token = _desired_token,
--	       valid_until_ts = NOW()+'1day'
--	   WHERE refresh_token = _refresh_token;
--	RETURN found;
--END
--$$ LANGUAGE plpgsql;




CREATE FUNCTION user_update_password(
    _reset_token character varying,
    _new_password character varying
)
RETURNS void
AS $$
DECLARE
    user_id_ INTEGER;
BEGIN
	SELECT fk_user_id INTO user_id_ FROM password_resets WHERE token=_reset_token;
	UPDATE users SET password=_new_password WHERE pk_user_id=user_id_;
	DELETE FROM password_resets WHERE token=_reset_token;
END
$$ LANGUAGE plpgsql;



CREATE FUNCTION user_confirm_email(
    _token character varying
)
RETURNS boolean
AS $$
BEGIN
	UPDATE users
	   SET email_confirmed=TRUE
	   FROM email_confirmations ec
	       WHERE ec.fk_user_id = pk_user_id
	           AND ec.token=_token;
    IF found THEN
	   DELETE FROM email_confirmations WHERE token=_token;
    END IF;
    RETURN found;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION user_confirm_phone(
    _user_id INTEGER,
    _code SMALLINT
)
RETURNS boolean
AS $$
BEGIN
    UPDATE users
       SET phone_confirmed=TRUE
       FROM phone_confirmations pc
           WHERE pc.fk_user_id = pk_user_id
               AND pc.code = _code
               AND pc.fk_user_id=_user_id;
    IF found THEN
        DELETE FROM phone_confirmations WHERE fk_user_id=_user_id;
    END IF;
    RETURN found;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION user_confirm_phone_by_phone(
    _user_id INTEGER,
    _phone CHARACTER VARYING
)
RETURNS boolean
AS $$
DECLARE
    f_ BOOLEAN;
BEGIN
    UPDATE users
        SET phone_confirmed=TRUE
        WHERE pk_user_id = _user_id
            AND phone = _phone;
    f_ = found;
    IF f_ THEN
        DELETE FROM phone_confirmations WHERE fk_user_id=_user_id;
    END IF;
    RETURN f_;
END
$$ LANGUAGE plpgsql;


CREATE FUNCTION user_unconfirm_other_phones(
    _user_id INTEGER
)
RETURNS VOID
AS $$
DECLARE
    phone_ CHARACTER VARYING;
BEGIN
	SELECT phone INTO phone_ FROM users WHERE pk_user_id = _user_id AND phone_confirmed;
	IF found THEN
        UPDATE users SET phone_confirmed = FALSE
            WHERE phone = phone_
                AND pk_user_id != _user_id;
    END IF;
END 
$$ LANGUAGE plpgsql;
