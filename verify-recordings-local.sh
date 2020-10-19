#!/bin/bash

echo "Total recordings over 15 days: "
find /var/bigbluebutton/published/presentation/ -maxdepth 1 -mtime -15 -printf "%f\n" | egrep '[a-z0-9]*-[0-9]*' | wc -l

echo "Already processed MP4: "
ls -l mp4 | grep mp4 | wc -l
