
CREATE OR REPLACE FUNCTION dev_randomizelikes(
    _duration INTERVAL
)
RETURNS void
AS $$
BEGIN
	PERFORM setseed(1.0);
    UPDATE htags_likes SET begin_ts = (NOW() - random()*_duration)
    --WHERE end_ts > NOW()
    ;
END 
$$ LANGUAGE plpgsql;
