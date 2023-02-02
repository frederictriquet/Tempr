#!/bin/bash


GEOFILE='~/Geonames/'

#F=$(grep -e '^fr,' $GEOFILE)

#grep -e '^fr,' $GEOFILE | awk -F, '{print $3}'

grep 'Nord-Pas-de' $GEOFILE2 | less



# dans GeoLite2-City-Locations-fr.csv
#geoname_id,locale_code,continent_code,continent_name,country_iso_code,country_name,subdivision_1_iso_code,subdivision_1_name,         subdivision_2_iso_code,subdivision_2_name,city_name,metro_code,time_zone
#2998324,   fr,         EU,            Europe,        FR,              France,      O,                     "Région Nord-Pas-de-Calais",59,                    Nord,              Lille,    ,          Europe/Paris
#2967110,fr,EU,Europe,FR,France,O,"Région Nord-Pas-de-Calais",59,Nord,,,Europe/Paris
#2967112,fr,EU,Europe,FR,France,O,"Région Nord-Pas-de-Calais",62,Pas-de-Calais,,,Europe/Paris
#2967157,fr,EU,Europe,FR,France,O,"Région Nord-Pas-de-Calais",59,Nord,,,Europe/Paris
#2967200,fr,EU,Europe,FR,France,O,"Région Nord-Pas-de-Calais",62,Pas-de-Calais,,,Europe/Paris
#2967284,fr,EU,Europe,FR,France,O,"Région Nord-Pas-de-Calais",59,Nord,,,Europe/Paris
#2967288,fr,EU,Europe,FR,France,O,"Région Nord-Pas-de-Calais",59,Nord,,,Europe/Paris