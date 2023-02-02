-- This is only demo data
-- DO NOT DEPLOY ON PROD

-- COPY users FROM '/srv/Tempr/DB/sql/users.dump' (DELIMITER '|');
-- SELECT setval('users_pk_user_id_seq', max(pk_user_id)) FROM users;

COPY htags FROM '/srv/Tempr/DB/sql/htags.dump' (DELIMITER '|');
SELECT setval('htags_pk_htag_id_seq', max(pk_htag_id)) FROM htags;
