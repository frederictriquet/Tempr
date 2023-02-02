#!/bin/bash

ROOT='/opt/Tempr/'
PYTESTS=$(find ${ROOT}Scenarii -name "*.py" | xargs wc -l | tail -1 | awk '{print $1}')
PYJOBS=$(find ${ROOT}Jobs -name "*.py" -print0 | xargs -0 wc -l | tail -1 | awk '{print $1}')
MYPHP="${ROOT}Dashboard/application/views
   ${ROOT}Dashboard/application/controllers
   ${ROOT}Dashboard/application/libraries
   ${ROOT}ME/application/views
   ${ROOT}ME/application/controllers
   ${ROOT}ME/application/libraries"
YMLDOC="${ROOT}Doc/WS/DOC/tempr.yml"
REST="${ROOT}WS"
DEPLOY="${ROOT}Deploy/Spawn"
PHP=$(find ${MYPHP} -name "*.php" | grep -v '/vendor/' | grep -v '/WS/Slim/' | grep -v 'application/views/errors' | xargs wc -l | tail -1 | awk '{print $1}')
REST=$(find ${REST} -name "*.php" | grep -v '/vendor/' | grep -v '/WS/Slim/' | grep -v 'application/views/errors' | xargs wc -l | tail -1 | awk '{print $1}')

SQL=$(find ${ROOT} -name "*.sql" | grep -v '/vendor/' | xargs wc -l | tail -1 | awk '{print $1}')
SH=$(find ${ROOT} -name "*.sh" | grep -v '/vendor/' | xargs wc -l | tail -1 | awk '{print $1}')
YML=$(find ${DEPLOY} -name "*.yml" | xargs wc -l | tail -1 | awk '{print $1}')

DOC=$(wc -l ${YMLDOC} | awk '{print $1}')
printf "PHP REST     % 10d   (l'API REST)\n" ${REST}
printf "PHP WEB      % 10d   (Dashboard, www.tempr.me)\n" ${PHP}
printf "Python TESTS % 10d   (les tests automatiques)\n" ${PYTESTS}
printf "Python JOBS  % 10d   (les jobs)\n" ${PYJOBS}
printf "SQL          % 10d   (le code de la base de données Postgresql)\n" ${SQL}
printf "Bash         % 10d   (des scripts pour simplifier/automatiser des tâches)\n" ${SH}
printf "YML          % 10d   (la description de tâches pour installer l'environnement de travail et la préprod)\n" ${YML}
printf "DOC          % 10d   (la documentation des API pour Tymate)\n" ${DOC}
echo "-----------------------"
printf "TOTAL        % 10d\n" $(($REST + $PHP + $PYTESTS + $PYJOBS + $SQL + $SH + $YML))
