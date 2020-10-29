#!/bin/bash

. ./.env

MEETINGS="meeting_file.txt"
RECORDED_MEETINGS="recorded_meetings.txt"
:> "$RECORDED_MEETINGS"

echo "List of meetings recorded in the last 7 days: recorded_meetings.txt"

find /var/bigbluebutton/recording/raw/ -maxdepth 1 -mtime -15 -printf "%f\n" | egrep "^[0-9a-f]{40}-[[:digit:]]{13}$" > "$MEETINGS"

while read meeting; do
  events_xml="/var/bigbluebutton/recording/raw/$meeting/events.xml"

  if [[ -f "$events_xml" ]]; 
  then
    #Read events.xml and look for RecordStatusEvent as true, which means the meeting was recorded
    result=`xmlstarlet sel -t -v '//recording/event[@eventname="RecordStatusEvent"]/status' "$events_xml"`
    
    if [ -n "$result" ]; 
    then
      echo "$meeting" >> "$RECORDED_MEETINGS"
    fi
  fi


done < $MEETINGS
rm "$MEETINGS"

#Create a tmp file to record unprocessed recordings
RECORDED_MEETINGS_TEMP='recorded_meetings.txt.t'
:> "$RECORDED_MEETINGS_TEMP"


#Create a file to keep MP4 already processed and uploaded on AWS S3
processedfilename='s3-processed-recordings.txt'
:> "$processedfilename"

aws s3 ls "s3://$S3BucketName" | awk '{ print $4 }' | cut -f 1 -d '.' | egrep '[a-z0-9\-]{54}' > $processedfilename

while read unprocessed_recording; do
  if grep -q "$unprocessed_recording" "$processedfilename"; then
    echo "Already recorded: $unprocessed_recording"     
  else
    echo "Not recorded: $unprocessed_recording"   
    echo "$unprocessed_recording" >> "$RECORDED_MEETINGS_TEMP"
  fi
done < $RECORDED_MEETINGS

mv "$RECORDED_MEETINGS_TEMP" "$RECORDED_MEETINGS"

#echo "Rebuilding recording using GNU Parallel"
#parallel -j 6 --timeout 200% --joblog log/parallel_rebuild.log -a "$RECORDED_MEETINGS" bbb-record --rebuild &
