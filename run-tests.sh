#!/bin/sh

# check wich config file to use .xml or .xml.dist
if [ -f "./phpunit.xml" ]
then
    TESTFILE="./phpunit.xml"
else
    TESTFILE="./phpunit.xml.dist"
fi

# number of parallel jobs
NUMJOBS="$(($(nproc)+1))"

# run paratest
./vendor/bin/paratest $COVERAGE --processes $NUMJOBS --functional --phpunit ./vendor/bin/phpunit --bootstrap ./tests/bootstrap.php --configuration $TESTFILE --colors --path ./tests/
