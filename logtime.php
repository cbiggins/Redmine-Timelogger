#! /usr/bin/php
<?php
/**
 * Simple, small command line script for logging time in redmine 
 *
 * Arguments:
 *
 * -i Issue number
 * -d Date
 * -h Hours spent
 * -a Activity Id
 * -c Comments
 * -v verbose - displays confirmations and error messages
 * 
 * Examples: To log time against an issue, run the command like the following;
 *
 * logtime.php -i {issue_number} -h .5
 **/
require_once ('phpactiveresource/ActiveResource.php');

$activity_ids = array(
                'daily scrum'               => 39,
                'development'               => 9,
                'project management'        => 14,
                'new business/non billable' => 38,
                'new business'              => 38,
                'non billable'              => 38,
                'consulting'                => 18,
                'visual design'             => 8,
                'documentation'             => 19,
                'information architecture'  => 13,
                'reviewing'                 => 17,
                'internal communications'   => 20,
                'client communications'     => 16,
                'training'                  => 15,
                'bug investigation'         => 31,
                'qa'                        => 32,);

class Time_Entry extends ActiveResource {
  var $site = '';
  var $user = '';
  var $password = '';
  var $request_format = 'xml'; // REQUIRED!
}

$options = getopt('i:d:h:a:c:v');

if (empty($options['h']) || empty($options['i'])) {
  print 'Minimum options are -i <issue number> and -h <hours>' . PHP_EOL;
  exit;
}

if (empty($options['a'])) {
  $options['a'] = $activity_ids['development'];
}
else {
  $options['a'] = $activity_ids[strtolower($options['a'])];
}

$new_entry = array(
             'issue_id'     => $options['i'],
             'spent_on'     => (!empty($options['d']) ? $options['d'] : date('Y-m-d')),
             'hours'        => $options['h'],
             'activity_id'  => (string)$options['a'],
             'comments'     => (!empty($options['c']) ? $options['c'] : null)
             );

$time_entry = new Time_Entry($new_entry);
$time_entry->save();

$tmp_id = $time_entry->id;
$success = !empty($tmp_id);

if (!$success) {
  if (!isset($options['v'])) {
    print 'There was an error. Run with -v to see output' . PHP_EOL;
  }
  else {
    print 'There was an error; ' . PHP_EOL;
    var_dump($time_entry);
  }
}
else {
  if (isset($options['v'])) {
    print 'Time entry added: id ' . $time_entry->id . PHP_EOL;  
  }
}
