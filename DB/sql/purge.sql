CREATE FUNCTION purge_confirmed_users_phone()
RETURNS INTEGER
AS $$
DECLARE
    nb_ INTEGER;
BEGIN
    -- vérification qu'il n'existe pas de pending_posts pour les users qu'on
    -- pense pouvoir supprimer
    IF NOT EXISTS (
        select 1 from pending_posts p
            where p.to_fk_pending_user_id in (
                select pu.pk_user_id
                    from pending_users pu
                    join users u on u.phone = pu.phone and u.phone_confirmed
            )
        )
    THEN
        with confirmed_users as (
            select pu.pk_user_id
                from pending_users pu
                join users u on u.phone = pu.phone and u.phone_confirmed
        )
        delete from pending_users pu
            using confirmed_users cu
            where pu.pk_user_id = cu.pk_user_id
        ;
        GET DIAGNOSTICS nb_ = ROW_COUNT;
    ELSE
        nb_ := -1;
    END IF;
    RETURN nb_;
END 
$$ LANGUAGE plpgsql;


CREATE FUNCTION purge_confirmed_users_facebook()
RETURNS INTEGER
AS $$
DECLARE
    nb_ INTEGER;
BEGIN
    -- vérification qu'il n'existe pas de pending_posts pour les users qu'on
    -- pense pouvoir supprimer
    IF NOT EXISTS (
        select 1 from pending_posts p
            where p.to_fk_pending_user_id in (
                select pu.pk_user_id
                    from pending_users pu
                    join users u on u.facebook_id = pu.facebook_id
            )
        )
    THEN
        with confirmed_users as (
            select pu.pk_user_id
                from pending_users pu
                join users u on u.facebook_id = pu.facebook_id
        )
        delete from pending_users pu
            using confirmed_users cu
            where pu.pk_user_id = cu.pk_user_id
        ;
        GET DIAGNOSTICS nb_ = ROW_COUNT;
    ELSE
        nb_ := -1;
    END IF;
    RETURN nb_;
END 
$$ LANGUAGE plpgsql;
