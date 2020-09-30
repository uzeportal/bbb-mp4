#!/bin/bash

record_mp4() {
	nohup /usr/bin/node --unhandled-rejections=none bbb-mp4.js $1 > "log/$1.out" 2> "log/$1.err" &
}

# find /mnt/scalelite-recordings/var/bigbluebutton/published/presentation/ -maxdepth 1 -newerct "22 Sep 2020" ! -newerct "23 Sep 2020" -printf "%f\n"
#record_mp4 "526de92a97199a94266681f44cfa1a3563b94d53-1600749281137" &
record_mp4 "39379d1440ea008fa03204efa8ca8417ce49ec2f-1600854946499" &
record_mp4 "18a25e352a0165453b63a10ff912e3e439d03276-1600929439152" &
record_mp4 "bb067befb7a8cff0ede05043b75ea19c8dd777a7-1601091754281" &
record_mp4 "f74ace5a585cdb321f4be5d22dcadedd3c7bface-1600928196508" &
record_mp4 "f329fc207cf03f6d4063fb6fcaee247cd21ad49f-1600919100348" &
record_mp4 "2aee038831a5109f06b7bfc39e0b4a2e0c2f232d-1600835403014"
