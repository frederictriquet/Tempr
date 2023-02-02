drop view view_pending_posts_by_sms;
drop view view_decorated_pending_posts;
drop view view_pending_post_with_tags;

alter table pending_posts
  add column nb_reminds integer default(0),
  add column last_remind_date date;

CREATE VIEW view_pending_post_with_tags AS
    SELECT p.*,
        string_agg(CASE WHEN ph.ck_seq_id=1 THEN m.filename END,'') AS filename,
        string_agg(CASE WHEN ph.ck_seq_id=1 THEN mv.filename END,'') AS filename_vid,
        string_agg(CASE WHEN ph.ck_seq_id=1 THEN h.tag END,'') AS tag1,
        string_agg(CASE WHEN ph.ck_seq_id=2 THEN h.tag END,'') AS tag2,
        string_agg(CASE WHEN ph.ck_seq_id=3 THEN h.tag END,'') AS tag3,
        SUM(CASE WHEN ph.ck_seq_id=1 THEN ph.pop END) AS pop1,
        SUM(CASE WHEN ph.ck_seq_id=2 THEN ph.pop END) AS pop2,
        SUM(CASE WHEN ph.ck_seq_id=3 THEN ph.pop END) AS pop3,
        MAX(CASE WHEN ph.ck_seq_id=1 THEN ph.fk_htag_id END) AS id1,
        MAX(CASE WHEN ph.ck_seq_id=2 THEN ph.fk_htag_id END) AS id2,
        MAX(CASE WHEN ph.ck_seq_id=3 THEN ph.fk_htag_id END) AS id3
        FROM pending_posts p
            LEFT JOIN posts_htags_pending ph ON ph.fk_post_id = p.pk_post_id
            LEFT JOIN htags h ON h.pk_htag_id = ph.fk_htag_id
            LEFT JOIN medias m ON m.pk_media_id = fk_media_id
            LEFT JOIN medias mv ON mv.pk_media_id = fk_media_vid_id
        GROUP BY p.pk_post_id;

CREATE VIEW view_decorated_pending_posts AS
    SELECT p.*,
        u1.firstname AS from_firstname,
        u1.lastname AS from_lastname,
        m1.filename AS from_filename_profile,
        u2.firstname AS to_firstname,
        u2.lastname AS to_lastname,
        m2.filename AS to_filename_profile
        ,c.locality AS city
    FROM view_pending_post_with_tags p
        JOIN users u1 ON u1.pk_user_id = p.from_fk_user_id
            LEFT JOIN medias m1 ON m1.pk_media_id = u1.fk_media_id_profile
        LEFT JOIN users u2 ON u2.pk_user_id = p.to_fk_user_id
            LEFT JOIN medias m2 ON m2.pk_media_id = u2.fk_media_id_profile
        LEFT JOIN devcities c ON c.pk_devcity_id = p.fk_devcity_id
    ;

create view view_pending_posts_by_sms as
  select v.pk_post_id, v.creation_ts, v.nb_reminds, v.last_remind_date, v.from_firstname, v.tag1, pu.phone, v.pending_reason
    from view_decorated_pending_posts v
    join pending_users pu on pu.pk_user_id = v.to_fk_pending_user_id
  ;
