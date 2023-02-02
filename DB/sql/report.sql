
CREATE FUNCTION report_post(
    _post_id BIGINT,
    _user_id INT
)
RETURNS void
AS $$
    INSERT INTO reports_posts(fk_post_id, fk_user_id, ts)
    VALUES (_post_id, _user_id, NOW());
$$ LANGUAGE sql;

CREATE FUNCTION report_comment(
    _comment_id BIGINT,
    _user_id INT
)
RETURNS void
AS $$
    INSERT INTO reports_comments(fk_comment_id, fk_user_id, ts)
    VALUES (_comment_id, _user_id, NOW());
$$ LANGUAGE sql;


