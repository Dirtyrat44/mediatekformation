#!/bin/bash
DATE=$(date +%F)
DB_NAME="mediatekformation_mediatek"
DB_USER=""
DB_PASS=""
DB_HOST="mysql-mediatekformation.alwaysdata.net"
BACKUP_DIR="/home/mediatekformation/backups"
FILENAME="${DB_NAME}_$DATE.sql"

mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/$FILENAME
