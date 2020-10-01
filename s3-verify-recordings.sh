#!/bin/bash

#Verify which of recordings from unprocessedfilename are not already recorded on S3

processedfilename='s3-processed-recordings.txt'
unprocessedfilename='bbb-unprocessed-recordings.txt'

echo "get list of recordings on S3 and print into processedfilename"
aws s3 ls s3://slate-recording | awk '{ print $4 }' | cut -f 1 -d '.' | egrep '[a-z0-9\-]{54}' > $processedfilename

while read unprocessed_recording; do
  if grep -q "$unprocessed_recording" "$processedfilename"; then
    echo "Already recorded: $unprocessed_recording"	
  else 
    echo "Not recorded: $unprocessed_recording"	  
    echo "$unprocessed_recording" >> "$unprocessedfilename.t"
  fi	
done < $unprocessedfilename

echo "Updating unprocessed file with recordings not processed yet";
mv "$unprocessedfilename.t" "$unprocessedfilename"

echo "Updated unprocessed recordings ready to be processed. Run bbb-mp4-bulk-parallel-input-file.sh";

rm "$processedfilename"
