#!/bin/bash

export HERE="$( dirname "$0" )"
source "$HERE/../config"
eval $( "$HERE/find-mysql.sh" )

echo "Creating database $DB_NAME"
echo "CREATE DATABASE IF NOT EXISTS $DB_NAME" | $MYSQL -u$DB_USER || exit

for tbl in "$SQL_DIR"/*.sql; do
  echo "Loading $tbl"
  cat "$tbl" | $MYSQL -u$DB_USER $DB_NAME
done

# vim:ts=2:sw=2:sts=2:et:ft=sh

