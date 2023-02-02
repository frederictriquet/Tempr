
-- function required by the make_schema script (using SchemaSpy)
CREATE FUNCTION information_schema._pg_keypositions() RETURNS SETOF integer
LANGUAGE sql
IMMUTABLE
AS $pg_keypositions$
select g.s
from generate_series(1,current_setting('max_index_keys')::int, 1)
as g(s)
$pg_keypositions$;







INSERT INTO metas(name,string_val) VALUES ('db_type','unknown');

INSERT INTO metas(name,int_val) VALUES ('version_major',1);
INSERT INTO metas(name,int_val) VALUES ('version_minor',1);
INSERT INTO metas(name,date_val) VALUES ('last_upgrade',now());





INSERT INTO parameters(name, descr, value) VALUES ('something', 'dummy value', '12');



