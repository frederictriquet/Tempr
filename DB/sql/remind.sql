
-- tout compte n'ayant pas validé son mail
-- relancer par mail ???

-- les pending users par phone
-- -> on les relance par sms : réémission du SMS
--  

-- les users qui n'ont pas confirmé leur numéro de phone
-- -> on les relance par mail
-- il faut valider le mail au préalable

-- les users qui n'ont pas d'amis
-- -> on les relance par mail
--    et par event
-- il faut valider le mail au préalable

-- garder trace du refus d'être sollicité par mail



-- les infos nécessaires pour (ré)émettre un SMS d'invitation
create view view_pending_posts_by_sms as
  select v.pk_post_id, v.creation_ts, v.nb_reminds, v.last_remind_date, v.from_firstname, v.tag1, pu.phone, v.pending_reason
    from view_decorated_pending_posts v
    join pending_users pu on pu.pk_user_id = v.to_fk_pending_user_id
  ;




