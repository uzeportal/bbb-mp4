#!/bin/bash
n=0

#Give date range of recordings for which you want to create MP4 files. 
#To create MP4 files of 29 Sep, from_date should be 29 Sep and to_date should be 30 Sep i.e. 1 day additional
from_date='29 Sep 2020'
to_date='30 Sep 2020'

#File where unprocessed recordings will be added. We will convert these recordings into MP4.
filename='bbb-unprocessed-recordings.txt'
#Ensure that the file exists and is empty
:> "$filename"

#Path of recordings is /mnt/scalelite-recordings/var/bigbluebutton/published/presentation/ if you execute this script on  Scalelite
#Path of recordings is /var/bigbluebutton/published/presentation if you execute this script on BBB
for i in $(find /mnt/scalelite-recordings/var/bigbluebutton/published/presentation/ -maxdepth 1 -newerct "$from_date" ! -newerct "$to_date" -printf "%f\n"); do
    recording="mp4/$i.mp4"
    [ ! -f "$recording" ] && n=$((n+1)) && echo "#$n $recording not found" && echo "$i" >> "$filename"
done

parallel -j 2 --joblog log/parallel_mp4.log --resume-failed -a "$filename" node bbb-mp4 &
