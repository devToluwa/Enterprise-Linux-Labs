#!/bin/bash
ldir="./reports" 
current_date=$(date +%Y-%m-%d)
current_time=$(date +%H:%M:%S)

if [ ! -d $ldir ]; then
  mkdir $ldir
fi
touch report-$current_date 
echo -e "DAILY REPORT\nGenerated on $current_date\nReport created at $current_time" > report-$current_date 
