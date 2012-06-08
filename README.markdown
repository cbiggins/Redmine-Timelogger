This repo comprises of two scripts, one for easily and quickly logging time against a Redmine issue (logtime) and one for checking how much time has been logged for the day (checkloggedtime.)

# Redmine Timelogger
This is for logging time against Redmine tickets from the command line. Its very fast and very easy to use.

### Installation
We need to get the code...

`git clone git@github.com:cbiggins/Redmine-Timelogger.git`


Now we need to get PHP ActiveResource...

`cd Redmine-Timelogger`

`git submodule init`

`git submodule update`


Then just add the username / password / Redmine URL for your Redmine install in lines 40, 41, 42.

Also, the 'activities' array is specific to our installation, you can update these to be yours as they will not correspond correctly with your id/name maps.

Voila! You're ready to go!

### Usage

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
 
# Check Logged Time

This is a bit more tricky to set up. You need to have PHP and Pear installed on your machine, along with Growl. 

### Installation 
This is the same as above, get the repo and configure it. The additional tasks are;

Install Net_Growl from Pear;
`sudo pear install Net_Growl`
If you are using MAMP, you may need to provide the path to Pear, such as;
`sudo /Applications/MAMP/bin/php/php5.2.17/bin/pear`
Also, if you are using MAMP 2+ you may need to ditch the Pear config if you get errors;
`sudo mv /Applications/MAMP/bin/php/php5.2.17/conf/pear.conf /Applications/MAMP/bin/php/php5.2.17/conf/pear.conf.bak`

### Config

Now, open your Growl app, click 'Network' and tick 'Listen for incoming notifications' - Lastly, add a password and add this in the config below.

Open checkloggedtime.php in an editor and update these variables;

    $me = ''; // Your full name as it appears in Redmine
    $growl_pass = ''; // Your Growl server password, see README 
    $full_day = 8; // How many hours makes up 100% utilisation?
    $today = strftime("%Y-%m-%d"); // Todays date in Redmines date format
    
    class Time_Entry extends ActiveResource {
      var $site = ''; // URL to Redmine
      var $user = ''; // Redmine username
      var $password = ''; // Redmine pass

Now, you can just run this script to ensure it is working;
`php checkloggedtime.php`
See if you get a growl notification about todays logged time.

The last step is to create the cron or scheduled task. I have mine in my crontab to run at 5pm daily;
`crontab -e`
Then add something like this;
`* 17 * * * php ~/bin/logtime/checkloggedtime.php`

Or, you can actually use iCal to run scripts, by creating a new event, editing it and in the 'alert' options, select 'run script' - then just schedule the event for whenever you want. 

Thats it!

