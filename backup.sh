#!/bin/bash

echo Backing up schema ...
# run mysqldump
mysqldump --opt --add-drop-database --no-data -u root --password="root" useradmin > schema.sql

echo Created MySQL backup: schema.sql




