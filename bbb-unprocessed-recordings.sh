#!/bin/bash
#From the given target-recording file, identify recordings which doesn't exist in mp4 directory and append to unprocessed-recording file to be processed again
unprocessedfilename='bbb-unprocessed-recordings.txt'
truncate -s 0 unprocessedfilename
filename='bbb-target-recordings.txt'
n=1

while read line; do
    echo "$n: $line"
    FILE="mp4/$line.mp4"
    [ ! -f "$FILE" ] && n=$((n+1)) && echo "$FILE" >> unprocessedfilename
done < $filename    
echo "$n Unprocessed recording"
