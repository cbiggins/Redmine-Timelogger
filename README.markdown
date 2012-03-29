Redmine Timelogger

This is for logging time against Redmine tickets from the command line. Its very fast and very easy to use.

## Installation

We need to get the code...

`git clone git@github.com:cbiggins/Redmine-Timelogger.git`


Now we need to get PHP ActiveResource...

`cd Redmine-Timelogger`

`git submodule init`

`git submodule update`


Then just add the username / password / Redmine URL for your Redmine install in lines 40, 41, 42.

Also, the 'activities' array is specific to our installation, you can update these to be yours as they will not correspond correctly with your id/name maps.

Voila! You're ready to go!

## Usage

 Arguments:
 
 -i Issue number

 -d Date

 -h Hours spent

 -a Activity Id

 -c Comments

 -v verbose - displays confirmations and error messages

 
 Examples: To log time against an issue, run the command like the following;

 logtime.php -i {issue_number} -h .5


 NOTE: If you ommit the activity, it will default to Development