#!/bin/bash

export HERE="$( dirname "$0" )"
source "$HERE/../config"
eval $( "$HERE/find-mysql.sh" )

mkdir -p "$SQL_DIR"

echo 'SHOW TABLES' | $MYSQL -u$DB_USER $DB_NAME | tail -n +2 | \
  while read tbl; do 
    echo "Dumping $tbl to $SQL_DIR/$tbl.sql"
    $MYSQLDUMP --skip-extended-insert --skip-dump-date -u$DB_USER $DB_NAME "$tbl" > "$SQL_DIR/$tbl.sql" || exit
  done

# vim:ts=2:sw=2:sts=2:et:ft=sh

