-- les infos dont on a besoin pour relancer un post par SMS
select v.pk_post_id, v.from_firstname, v.tag1, pu.phone
  from view_decorated_pending_posts v
  join pending_users pu on pu.pk_user_id = v.to_fk_pending_user_id
  where v.pending_reason = B'00011000'
  ;

select u.pk_user_id, phone_confirmed, pu.pk_user_id from users u
   join pending_users pu on pu.phone = u.phone
   join pending_posts pp on pp.to_fk_pending_user_id = pu.pk_user_id
   where u.phone_confirmed
   order by u.pk_user_id;

-- les users qui ont été invités par phone
-- qui ont saisi leur phone mais pas confirmé
-- il faudrait les relancer par mail
select u.pk_user_id, phone_confirmed, pu.pk_user_id from users u
   join pending_users pu on pu.phone = u.phone
   join pending_posts pp on pp.to_fk_pending_user_id = pu.pk_user_id
   where not u.phone_confirmed
   order by u.pk_user_id;


select distinct u.pk_user_id from users u
   join pending_users pu on pu.phone = u.phone
   join pending_posts pp on pp.to_fk_pending_user_id = pu.pk_user_id
   where u.phone_confirmed
   order by u.pk_user_id;


select u.pk_user_id, pu.pk_user_id from users u
   join pending_users pu on pu.facebook_id = u.facebook_id
   order by u.pk_user_id;


select distinct to_fk_pending_user_id from pending_posts order by to_fk_pending_user_id;

-- les post en attente parce que le destinataire n'a pas validé son phone
-- alors que dans la table users, le phone est là
select pp.*, pu.*
  from pending_posts pp
  join pending_users pu on pu.pk_user_id = pp.to_fk_pending_user_id
  join users u on u.phone = pu.phone
  ;
  
-- les post en attente parce que le destinataire n'a pas FB
-- alors que dans la table users, il y a FB
select pp.*, pu.*
  from pending_posts pp
  join pending_users pu on pu.pk_user_id = pp.to_fk_pending_user_id
  join users u on u.facebook_id = pu.facebook_id
  ;

-- les posts en attente à cause d'une attente d'amitié
-- alors qu'il n'y a pas de demande d'amitié
select pp.from_fk_user_id as fk_user_id1, pp.to_fk_user_id as fk_user_id2
  from pending_posts pp
  where pp.pending_reason = B'00010000'
except
select fk_user_id1,fk_user_id2 from friendship_requests
;
  

-- user_id et nombre d'amis
select u.pk_user_id,
       coalesce(nb_friends,0)
  from users u
  left join (
    select f.fk_user_id1, count(*) as nb_friends
      from friendships f
      group by f.fk_user_id1
    ) f_ on f_.fk_user_id1 =  u.pk_user_id
  order by u.pk_user_id
  ;


-- les users seuls avec le nombre de demandes d'amitié
select u.pk_user_id, u.login, count(fr.fk_user_id1) as demandeur, count(fr2.fk_user_id2) as demandey, count(pfr.from_fk_user_id) as inviteur
  from users u
  left join friendship_requests fr on fr.fk_user_id1 = u.pk_user_id
  left join friendship_requests fr2 on fr2.fk_user_id2 = u.pk_user_id
  left join pending_friendship_requests pfr on pfr.from_fk_user_id = u.pk_user_id
  where not exists (select 1 from friendships f where f.fk_user_id1 = u.pk_user_id)
  group by u.pk_user_id
  order by u.pk_user_id
  ;


select pfr.*, pu.phone, pu.facebook_id
  from pending_friendship_requests pfr
  join pending_users pu on pu.pk_user_id = pfr.to_fk_pending_user_id
  order by pfr.from_fk_user_id
  ;



-- stats sur les htags
select h.*, count(hl.*) as nb
  from htags h
  left join posts_htags ph on ph.fk_htag_id = h.pk_htag_id
  left join htags_likes hl on hl.fk_post_id = ph.fk_post_id and hl.ck_seq_id = ph.ck_seq_id
  group by pk_htag_id
  order by nb desc
  limit 10;

-- les pending users qu'on peut supprimer
select pu.pk_user_id as PU_id, pu.phone, u.pk_user_id as USER_id
  from pending_users pu
  join users u on u.phone = pu.phone and u.phone_confirmed
  ;

with confirmed_users as (
    select pu.pk_user_id
      from pending_users pu
      join users u on u.phone = pu.phone and u.phone_confirmed
  )
  delete from pending_users pu
    using confirmed_users cu
    where pu.pk_user_id = cu.pk_user_id
  ;


-- vérification qu'il n'existe pas de pending_posts pour les users qu'on
-- pense pouvoir supprimer
select * from pending_posts p
  where p.to_fk_pending_user_id in (
    select pu.pk_user_id
      from pending_users pu
      join users u on u.phone = pu.phone and u.phone_confirmed
  )
  ;




select pu.pk_user_id as PU_id, pu.facebook_id, u.pk_user_id as USER_id
  from pending_users pu
  join users u on u.facebook_id = pu.facebook_id
  ;


-- les demandes d'amitié qui commencent à dater
select * from events where type='friendship_request' and creation_ts < now() - interval '1week'
order by to_fk_user_id, creation_ts ;

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


-- nombre d'amis de chaque user, chaque jour
select u.pk_user_id
  from users u
  left join friendships f on f.fk_user_id1 = u.pk_user_id


-- les villes qu'il faudrait resoudre
select * from posts p
  join devcities dc on dc.pk_devcity_id=p.fk_devcity_id
  where p.fk_devcity_id is not null and geo is null
  order by locality
  ;
