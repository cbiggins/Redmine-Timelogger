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

class Time_Entry extends ActiveResource {
  var $site = 'https://redmine.previousnext.com.au/';
  var $user = '';
  var $password = '';
  var $request_format = 'xml'; // REQUIRED!
}

$options = getopt('i:d:h:a:c:v');

if (empty($options['h']) || empty($options['i'])) {
  print 'Minimum options are -i <issue number> and -h <hours>' . PHP_EOL;
  exit;
}

$new_entry = array(
             'issue_id'     => $options['i'],
             'spent_on'     => (!empty($options['d']) ? $options['d'] : date('Y-m-d')),
             'hours'        => $options['h'],
             'activity_id'  => (!empty($options['a']) ? $options['a'] : 'Development'),
             'comments'     => (!empty($options['c']) ? $options['c'] : null)
             );

$time_entry = new Time_Entry($new_entry);
$time_entry->save();

if (empty($time_entry->id)) {
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
