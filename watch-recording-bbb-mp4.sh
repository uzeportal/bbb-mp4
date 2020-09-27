#!/bin/bash


echo "Watching New BBB Meetings"
echo `date`

#Published directory to observe for when a meeting is published.
#For BigBlueButton single instance, change this to /var/bigbluebutton/published/presentaation
DIRECTORY_TO_OBSERVE="/mnt/scalelite-recordings/var/bigbluebutton/published/presentation"
#Directory where bbb-mp4 is installed
DIRECTORY_BBB_MP4="/root/bbb-mp4"

watch() {

  inotifywait -m -r -e create -e moved_to $DIRECTORY_TO_OBSERVE | while read path action file;do
    echo "Change detected date $(date) in ${path} action ${action} in file ${file}"
    convert_mp4 "${file}"
  done

}

convert_mp4() {
    # Absolute path to node - execute command "which node" to find out
    cd $DIRECTORY_BBB_MP4 && /usr/bin/node bbb-mp4.js $1 

}

# This script is called by the supervisor.
watch
