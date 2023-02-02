drop table fullcities;
create table fullcities(
geonameid bigint primary key,
name character varying(200),
asciiname character varying(200),
alternatenames character varying(10000),
latitude float,
longitude float,
featureclass char,
featurecode character varying(10),
countrycode character varying(2),
cc2 character varying(200),
admin1code character varying(20),
admin2code character varying(80),
admin3code character varying(20),
admin4code character varying(20),
population bigint,
elevation integer default(0),
dem integer default(0),
timezone character varying(40),
modificationdate date
);
copy fullcities from '/tmp/a.txt' (DELIMITER '|', null '' );
