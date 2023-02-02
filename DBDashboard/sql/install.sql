-- -------------------------------------------------------------------- SIGNUP
CREATE TABLE signups (
	pk_signup_date DATE,
	nb_signups_total INTEGER,
	PRIMARY KEY (pk_signup_date)
);

-- -------------------------------------------------------------------- NB_FRIENDS OF USERS
CREATE TABLE friends (
	ck_user_id INT,
	ck_date DATE,
	nb_friends_today INTEGER,
	PRIMARY KEY (ck_user_id, ck_date)
);
