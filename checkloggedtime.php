#! /usr/bin/php
<?php
ini_set('display_errors', 1);
require_once ('phpactiveresource/ActiveResource.php');

// Install using Pear; sudo pear install Net_Growl
require_once ('Net/Growl/Autoload.php');

/**
 * REDMINE Section
 */

$me = 'Christian Biggins'; // Your full name as it appears in Redmine
$growl_pass = 'pass'; // Your Growl server password, see README 
$full_day = 8; // How many hours makes up 100% utilisation?

if (!empty($argv[1])) {
  $today = strftime("%Y-%m-%d"); // Todays date in Redmines date format
}
else {
  $today = $argv[1];
}

class Time_Entry extends ActiveResource {
  var $site = ''; // URL to Redmine - WITH trailing slash and https (if requd)
  var $user = ''; // Redmine username
  var $password = ''; // Redmine pass
  var $request_format = 'xml'; // REQUIRED!
  var $element_name = 'time_entries'; 
}

$time = new Time_Entry;
$time->extra_params = '?period_type=2&from=' . $today . '&to=' . $today . '&limit=1000'; // limit 1000 seems excessive, but we need to make sure we get all time entries.

$times = $time->find('all');

$time_logged = 0;

// Loop over each entry. We can't limit the API call to a particular user, so we need to compare it with the $me var
foreach ($times as $k => $time) {

  if (!empty($time->_data['user'])) {
    $user = $time->_data['user'];
    $name_attr = 'name';

    // Get the user that logged this time entry
    $time_user = (string)$user->attributes()->$name_attr;

    // If the user is $me, add the logged hours to our total
    if ($time_user == $me) {
      $time_logged += $time->_data['hours']; 
    }
  }
}

// Calculate a percentage using the $full_day val from above
$logged_percent = ($time_logged / $full_day) * 100;


/**
 * GROWL Section!
 */

// Notification Type definitions
define('GROWL_NOTIFY_STATUS',   'STATUS');
define('GROWL_NOTIFY_ERROR',    'ERROR');

// define a PHP application that sends notifications to Growl
$appName = 'PEAR/Net_Growl ' . basename(__FILE__, '.php');

$notifications = array(
    GROWL_NOTIFY_STATUS => array(
        'display' => 'Status',
    ),
    GROWL_NOTIFY_ERROR => array(
        'display' => 'Error',
    ),
);

$password = $growl_pass;
$options  = array(
    'protocol' => 'gntp', 'timeout' => 15,
    'AppIcon'  => 'http://www.redmine.org/attachments/3462/redmine_fluid_icon.png',
);

try {
  // Create our Growl notification and display it.

  $growl = Net_Growl::singleton($appName, $notifications, $password, $options);
  $growl->register();

  $title       = 'Todays time';
  if ($time_logged > 0) {
    $name        = GROWL_NOTIFY_STATUS;
    $description = $me . ', You have only logged ' . $time_logged . 'hrs (' . $logged_percent . '%) today!';
  }
  else {
    $name        = GROWL_NOTIFY_ERROR;    
    $description = $me . ', You have not logged any time today!'; 
  }

  $options     = array('sticky' => TRUE);

  $growl->publish($name, $title, $description, $options);

} catch (Net_Growl_Exception $e) {
  echo 'Caught Growl exception: ' . $e->getMessage() . PHP_EOL;
}
