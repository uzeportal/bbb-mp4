#!/bin/bash

S3_BUCKET_NAME='s3://slate-recording'
NUM_DAYS=7

#File where unprocessed recordings will be added. We will convert these recordings into MP4.
filename='bbb-unprocessed-recordings.txt'
#Ensure that the file exists and is empty
:> "$filename"

find /mnt/scalelite-recordings/var/bigbluebutton/published/presentation/ -maxdepth 1 -mtime -"$NUM_DAYS" -printf "%f\n" | egrep '[a-z0-9]*-[0-9]*' > "$filename"

TOTAL_RECRODINGS=$(cat "$filename" | wc -l)
echo "Total recordings the last $NUM_DAYS day: $TOTAL_RECRODINGS"

echo "Ensuring already converted MP4 files are sync with S3"
aws s3 sync mp4/ "$S3_BUCKET_NAME"  --acl public-read

echo "Removing files which are already synced with S3"
./s3-verify-recordings.sh

TOTAL_UNPROCESSED_RECRODINGS=$(cat "$filename" | wc -l)
echo "Unprocessed recordings the last $NUM_DAYS day: $TOTAL_UNPROCESSED_RECRODINGS"

echo "Starting MP4 conversion using GNU Parallel"
parallel -j 3 --timeout 300% --joblog log/parallel_mp4.log -a "$filename" node bbb-mp4 &
