# bbb-mp4

This app helps in recording a BigBlueButton meeting as MP4 video and upload to S3.

## Dependencies

1. xvfb (`apt install xvfb`)
2. Google Chrome stable
3. npm modules listed in package.json
4. Everything inside `dependencies_check.sh` (run `./dependencies_check.sh` to install all)

The latest Google Chrome stable build should be use.

```sh
curl -sS -o - https://dl-ssl.google.com/linux/linux_signing_key.pub | apt-key add
echo "deb [arch=amd64] http://dl.google.com/linux/chrome/deb/ stable main" > /etc/apt/sources.list.d/google-chrome.list
apt-get -y update
apt-get -y install google-chrome-stable
```

FFmpeg (if not installed already & have plan for mp4 or RTMP)
```sh
sudo add-apt-repository ppa:jonathonf/ffmpeg-4
sudo apt-get update
sudo apt-get install ffmpeg
```

## Usage

Clone the project first:

```javascript
git clone https://github.com/manishkatyan/bbb-mp4.git
cd bbb-mp4
npm install --ignore-scripts
cp .env.example .env
```

Update .env file:
1) playBackURL is https://<domain>/playback/presentation/2.0/playback.html?meetingId= for default playback of BBB 2.2.x or https://<domain>/playback/presentation/2.3/ if you are using [bbb-playback](https://github.com/bigbluebutton/bbb-playback) that would be part of BBB 2.3
2) By default Chrome downloads meeting recording in Downloads direcotry of the user or /tmp, when executed in the background. Hence, we explicetly set copyFromPath i.e. download location of the recording so that bbb-mp4 can correctly read the downloaded file and proceed with conversion into MP4   
3) copyToPath is where MP4 files are kept
4) S3BucketName is the bucket name of S3. Default file permission is --acl public-read. You can change permisson in bbb-mp4.js > uploadToS3

Setup AWS CLI to upload to S3
1) Install [AWS CLI Version 2](https://docs.aws.amazon.com/cli/latest/userguide/cli-chap-install.html)
2) Configure AWS with this command: $ aws configure
3) If needed, get S3 region name code from [here](https://docs.aws.amazon.com/general/latest/gr/rande.html#s3_region)

## Record Meetings in MP4 and upload to S3

```sh
node bbb-mp4 MEETING_ID
```

### Options

1) MEETING_ID is the internal meeting id of the recording that you want to convert into MP4. 
2) The MP4 file of the given meeting id is kept in mp4 directory
3) The command above will record meeting in webm format, convert to MP4 and upload to S3. 

## Convert MP4 in Bulk

```sh
apt-get install parallel
```

We use [GNU Parallel](https://www.gnu.org/software/parallel/) to convert multiple MP4 recordings simultaneously. So Install GNU Paralle.  

You have the following two methods:

### Method 1: Convert MP4 from recordings in /published/presentation/ directory

```sh
./bbb-mp4-bulk-parallel.sh
```

This method is useful when you are running bbb-mp4 on BigBlueButton or Scalelite server. You can set from and to date to convert into MP4 recordings from the given date range.

Open file bbb-mp4-bulk-parallel.sh and verify from date, to date and recordings directory, which will be different for BigBlueButton and Scalelite. 

Once you are ready, execute bbb-mp4-bulk-parallel.sh.
 

### Method 2: Convert MP4 from recording Ids listed in a file 

```sh
apt-get install parallel
find /var/bigbluebutton/published/presentation -maxdepth 1 -newerct "22 Sep 2020" ! -newerct "23 Sep 2020" -printf "%f\n" > bbb-target-recordings.txt
parallel -j 0 -a bbb-target-recordings.txt node bbb-mp4
aws s3 sync video/ s3://S3_BUCKET_NAME  --acl public-read
```

1) Install GNU Parallel: $sudo apt-get install parallel
2) Create a file bbb-target-recordings.txt meeting ids of the recordigs that you want to convert into MP4. Here is one way to create list of meetings on 22 Sep that is to be converted into MP4: find /var/bigbluebutton/published/presentation -maxdepth 1 -newerct "22 Sep 2020" ! -newerct "23 Sep 2020" -printf "%f\n" > bbb-target-recordings.txt
4) In case you are using Scalelite, change presentation directory to /mnt/scalelite-recordings/var/bigbluebutton/published/presentation/ in the find command above
5) Now execute parallel command to convert in bulk: parallel -j 0 -a bbb-target-recordings.txt node bbb-mp4 
6) By default, parallel will execute as many number of jobs as the number of CPU Cores. Keep -j as 0 so that parallel can execute as many jobs as needed in parallel
7) To ensure that all MP4 files are uploaded to S3, you can also sync-up mp4 files in video directory to to AWS S3 bucket:  aws s3 sync video/ s3://S3_BUCKET_NAME  --acl public-read

## Automate MP4 Conversion

```sh
apt-get install supervisor
service supervisor restart
cp watch-recording-bbb-mp4.conf /etc/supervisor/conf.d/watch-recording-bbb-mp4.conf
supervisorctl reread
supervisorctl update
tail -f /root/bbb-mp4/log/watch-recording-bbb-mp4.out.log
```

1) Install [supervisor](https://www.digitalocean.com/community/tutorials/how-to-install-and-manage-supervisor-on-ubuntu-and-debian-vps)
2) Copy watch-recording-bbb-mp4.conf to /etc/supervisor/conf.d
3) Inform Supervisor of our new program through the supervisorctl command. At this point our program should now be running to watch for any new meetings and we can check this is the case by looking at the output log file: /root/bbb-mp4/log/watch-recording-bbb-mp4.out.log
4) You can test whether watch is working using the test script - watch-recording-bbb-mp4-test.sh - update DIRECTORY_TO_TEST and TEST_MEETING_ID as appropriate

## How it will work?
When you will run the command that time `Chrome` browser will be open in background & visit the link to perform screen recording in WEBM formar. After compeltion of recording, we use FFMEG to convert to MP4 and use AWS CLI3 to upload to S3.

**Note: It will use extra CPU to process chrome & ffmpeg.**



### Thanks to

[puppetcam](https://github.com/muralikg/puppetcam)

[Canvas-Streaming-Example](https://github.com/fbsamples/Canvas-Streaming-Example)

[bbb-recorder](https://github.com/jibon57/bbb-recorder)
