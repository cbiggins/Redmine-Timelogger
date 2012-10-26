#! /usr/bin/php
<?php
ini_set('display_errors', 1);
require_once ('phpactiveresource/ActiveResource.php');

class Time_Entry extends ActiveResource {
  var $site = ''; // URL to Redmine - WITH trailing slash and https (if requd)
  var $user = ''; // Redmine username
  var $password = ''; // Redmine pass
  var $request_format = 'xml'; // REQUIRED!
  var $element_name = 'time_entries';
}

$time_array = get_entries();
$month_total = array('billable' => 0, 'spent' => 0);

// Output our data...
foreach($time_array as $date => $time) {
  print $date . PHP_EOL;
  $billable = str_repeat('||', round($time['billable_total'])) . '> ' . $time['billable_total'];
  $spent = str_repeat('||', round($time['spent'])) . '> ' . $time['spent'];
  print 'Min Billable Hrs:' . $billable . PHP_EOL;
  print 'Total Logged Hrs:' . $spent . PHP_EOL;
  print PHP_EOL;

  $month_total['billable'] += $time['billable_total'];
  $month_total['spent'] += $time['spent'];
}

print 'Total Min Billable for the month: ' . $month_total['billable'] . PHP_EOL;
print 'Time spent for the month: ' . $month_total['spent'] . PHP_EOL;

function get_entries($time_array = array(), $offset = FALSE) {

  $me = ''; // Your full name as it appears in Redmine
  $full_day = 8; // How many hours makes up 100% utilisation?
  $from = strftime("%Y-%m-01");

  if (empty($argv[1])) {
    $today = strftime("%Y-%m-%d"); // Todays date in Redmines date format
  }
  else {
    $today = $argv[1];
  }

  if ($offset === FALSE) {
    $offset = 0;
  }
  else {
    $offset += 100;
  }

  $limit = 100;
  $time = new Time_Entry;
  $query = '?period_type=2&from=' . $from . '&to=' . $today . '&limit='.$limit.'&offset=' . $offset;

  $time->extra_params = $query;
  $times = $time->find('all');

  $time_logged = 0;

  // Loop over each entry. We can't limit the API call to a particular user, so we need to compare it with the $me var
  $total_count = 0;
  foreach ($times as $k => $time) {
    if (!empty($time->_data['user'])) {
      $user = $time->_data['user'];
      $name_attr = 'name';

      // Get the user that logged this time entry
      $time_user = (string)$user->attributes()->$name_attr;

      // If the user is $me, add the logged hours to our total
      if ($time_user == $me) {
        // If its a weekend, billable is 0.
        $dow = date('w', strtotime($time->_data['spent_on']));
        $billable = ($dow > 0 && $dow < 6 ? 8 : 0);

        if (empty($time_array[$time->_data['spent_on']])) $time_array[$time->_data['spent_on']] = array('spent' => 0, 'billable_total' => $billable);
        $time_array[$time->_data['spent_on']]['spent'] += $time->_data['hours'];
      }
    }
    $total_count++;
  }

  // Are there more?
  if ($total_count == $limit) {
    $time_array = get_entries($time_array, $offset);
  }

  return $time_array;
}
