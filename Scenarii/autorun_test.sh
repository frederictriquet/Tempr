#!/bin/bash

./run_all_tests.py $1
while inotifywait -e modify -rq .
do
    ./run_all_tests.py $1
done

