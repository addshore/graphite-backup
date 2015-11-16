# graphite-backup

A crude script for backing up data from graphite.

## How to Backup

    php run.php metric.name

Will create a file in /data called metric.name.txt that looks like this:

    1447595400 117
    1447595460 128
    1447595520 99
    1447595580 132

##### Advanced Backups

Backup wildcard targets

    php run.php metric.*.foo

Backup multiple targets

    php run.php metric.1 metric.2

## How to Restore

You could restore this data by iterating over the files using something like this:

    echo "${METRIC} ${VALUE} ${TIMESTAMP}" | nc -q0 ${SERVER} ${PORT}

See http://graphite.readthedocs.org/en/latest/feeding-carbon.html for other similar code for the graphite api.