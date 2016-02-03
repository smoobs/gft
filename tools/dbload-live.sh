#!/bin/bash

export HERE="$( dirname "$0" )"
source "$HERE/../config"
eval $( "$HERE/find-mysql.sh" )
FIX_WP_SQL="$HERE/fix_wp_sql.pl"

echo "Creating database $DB_NAME"
echo "CREATE DATABASE IF NOT EXISTS $DB_NAME" | mysql -u$DB_USER || exit

for tbl in "$SQL_DIR"/*.sql; do
  echo "Loading $tbl"
  cat "$tbl" | perl "$FIX_WP_SQL" "$FIX_INDOM" "$FIX_OUTDOM" | $MYSQL -u$DB_USER $DB_NAME
done

# vim:ts=2:sw=2:sts=2:et:ft=sh

