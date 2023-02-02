
CREATE OR REPLACE FUNCTION trend_user_update(_user_id INT)
RETURNS void
AS
$$
DECLARE
    r RECORD;
BEGIN
    UPDATE users SET lasttrends_ts = NOW()
        WHERE pk_user_id = _user_id
        ;
--            AND lasttrends_ts < NOW() - INTERVAL '1day';
    IF NOT found THEN
        --RAISE WARNING 'not upd %', _user_id;
        RETURN;
    END IF;
    --RAISE WARNING 'upd %', _user_id;
    DELETE FROM user_recent_trends WHERE fk_user_id = _user_id;
    INSERT INTO user_recent_trends(fk_user_id, fk_htag_id, pop)
        SELECT _user_id, ph.fk_htag_id, count(*) as c
            FROM htags_likes hl
            JOIN posts_htags ph ON hl.fk_post_id = ph.fk_post_id AND hl.ck_seq_id = ph.ck_seq_id
            JOIN posts p ON ph.fk_post_id = p.pk_post_id AND p.to_fk_user_id = _user_id
            WHERE hl.begin_ts > NOW() - INTERVAL '14days'
                --AND hl.end_ts > NOW()
            GROUP BY ph.fk_htag_id
            ORDER BY c DESC
            LIMIT 10;
    -- update the total amount of likes for the user
    update users u set likes = (
      select COALESCE(SUM(ph.pop), 0)
      from posts p
      join posts_htags ph ON ph.fk_post_id = p.pk_post_id
      where p.to_fk_user_id = u.pk_user_id
      and u.pk_user_id = _user_id
    )
    where u.pk_user_id = _user_id;

END
$$ LANGUAGE plpgsql;

CREATE FUNCTION trends_user_init(
    _user_id INTEGER
) RETURNS void
AS $$
    INSERT INTO user_long_term_trends_detail(fk_user_id, ck_seq_id)
        SELECT _user_id, generate_series(1,52);
    INSERT INTO user_long_term_trends_summary(fk_user_id, ck_seq_id)
        SELECT _user_id, generate_series(1,40);
$$ LANGUAGE sql;

CREATE OR REPLACE FUNCTION trend_user_long_term_update(
    _user_id INT
) RETURNS void
AS $$
DECLARE
    ts_end_ TIMESTAMP WITHOUT TIME ZONE := NOW();
    ts_begin_ TIMESTAMP WITHOUT TIME ZONE;
    htag_id_ BIGINT;
    total_pop_ INTEGER;
BEGIN
    FOR i IN 1..52 LOOP -- one year
        ts_begin_ := ts_end_ - INTERVAL '1week';
        -- RAISE WARNING '% : % - %', i, ts_begin_, ts_end_;

        SELECT ph.fk_htag_id, COUNT(hl.*) AS total_pop
            INTO htag_id_, total_pop_
            FROM posts p
            JOIN posts_htags ph ON ph.fk_post_id = p.pk_post_id
            JOIN htags_likes hl ON
                    hl.fk_post_id = ph.fk_post_id
                AND hl.ck_seq_id = ph.ck_seq_id
                AND hl.begin_ts BETWEEN ts_begin_ AND ts_end_
                --AND hl.end_ts > ts_end_
            WHERE p.to_fk_user_id = _user_id
            GROUP BY ph.fk_htag_id
            ORDER BY total_pop DESC
            LIMIT 1;
        -- IF _user_id = 1 THEN RAISE WARNING '% % %', i, htag_id_, total_pop_; END IF;
        UPDATE user_long_term_trends_detail
            SET fk_htag_id = htag_id_, pop = total_pop_
            WHERE
                    fk_user_id = _user_id
                AND ck_seq_id = i
                -- do not update if unchanged
                -- 'IS DISTINCT FROM' is equivalent to '!=' except it deals
                -- with NULL values as expected
                AND (fk_htag_id IS DISTINCT FROM htag_id_ OR pop IS DISTINCT FROM total_pop_)
                ;
        -- RAISE WARNING '% % %', found, htag_id_, total_pop_;
        ts_end_ := ts_begin_;
    END LOOP;

    UPDATE user_long_term_trends_summary s
        SET fk_htag_id = NULL, pop = NULL
        WHERE fk_user_id = _user_id;

    UPDATE user_long_term_trends_summary s
        SET fk_htag_id = det.fk_htag_id
        FROM (
            SELECT row_number() OVER(ORDER BY SUM(d.pop) DESC) r, d.fk_htag_id
                FROM user_long_term_trends_detail d
                WHERE d.fk_user_id = _user_id and d.fk_htag_id is not null
                GROUP BY d.fk_htag_id
                ORDER BY SUM(d.pop) DESC
                LIMIT 40
        ) det
        WHERE s.ck_seq_id = det.r
        AND s.fk_user_id = _user_id;

    UPDATE user_long_term_trends_summary s
        SET pop = total.pop
        FROM (
            SELECT ph.fk_htag_id, SUM(ph.pop) AS pop
            FROM posts_htags ph
                JOIN posts p ON p.pk_post_id = ph.fk_post_id
                            AND p.to_fk_user_id = _user_id
                GROUP BY ph.fk_htag_id
        ) total
        WHERE s.fk_user_id = _user_id
        AND s.fk_htag_id = total.fk_htag_id;
END
$$ LANGUAGE plpgsql;
