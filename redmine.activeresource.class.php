<?php

require_once ('phpactiveresource/ActiveResource.php');

define('REDMINE_FULLNAME', '');

class RedmineActiveResource extends ActiveResource {
  var $site = ''; // URL to Redmine - WITH trailing slash and https (if requd)
  var $user = ''; // Redmine username
  var $password = ''; // Redmine pass
  var $request_format = 'xml'; // REQUIRED!
}