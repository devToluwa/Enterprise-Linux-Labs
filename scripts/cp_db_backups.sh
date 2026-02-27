#!/bin/bash

source="root@192.168.153.12:/home/toluwa/here/mysql_dumps_backups/"
dest="/home/toluwa/Homelabs002/Enterprise-Linux-Labs/db_backups/"

/usr/bin/rsync -a "$source" "$dest" 
