<?php
session_start();
date_default_timezone_set('America/Los_Angeles');

require "config.php";
require_once('boxlibphp5.php');

$api_key = Config::boxDotNetApiKey();
$auth_token = isset($_SESSION['auth_token']) ? $_SESSION['auth_token'] : '';

$is_logged_in = !($auth_token == '');

$box = new boxclient($api_key, $auth_token);

$ticket_return = $box->getTicket();

if ($box->isError()) {
  echo $box->getErrorMsg();
} else {
  $ticket = $ticket_return['ticket'];
}

if (!$is_logged_in) {
  $params = array();
  $params['api_key']  = $api_key;
  $params['ticket']   = $ticket;

  $ret_array = array();
  $data = $box->makeRequest('action=get_auth_token', $params);

  if ($box->_checkForError($data)) {
    $is_logged_in = false;
  }

  foreach ($data as $a) {
    switch ($a['tag']) {
      case 'STATUS':
      $ret_array['status'] = $a['value'];
      break;
      case 'AUTH_TOKEN':
      $ret_array['auth_token'] = $a['value'];
      break;
    } // switch
  } // foreach

  if ($ret_array['status'] == 'get_auth_token_ok') {
    $auth_token = $box->auth_token = $_SESSION['auth_token'] = $ret_array['auth_token'];
    $is_logged_in = true;
  } else {
    $is_logged_in = false;   
  }
}

?>
<html>
<head>
  <title>Voice Comments for Box</title>
  <link rel="stylesheet" type="text/css" href="bootstrap-1.0.0.min.css" />
  <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div id="masthead" class="masthead">
  <div class="inner">
    <div class="container">
      <h1>Voice Comments for Box.net</h1>
      <p class="lead">Important people <strong>say</strong> what they <strong>think</strong>, they don't type it. And you're important, aren't you?</p>
      <p>Just don't feel self-conscious about your voice. It's just fine.</p>
    </div>
  </div>
</div>
<?php

if (!$is_logged_in) {
?>
  <div class="container">
    <div class="alert-message error">
      This is a proof-of-concept. Use at your own risk. Also, don't use with sensitive documents.
    </div>
    <div class="btn large primary" onclick="window.location='http://www.box.net/api/1.0/auth/<?php echo $ticket ?>';">
      Sign in with Box.net
    </div>
  </div>
<?php } else { ?>
<?php
$tree = $box->getAccountTree();
?>
  <div class="container">
    <div class="alert-message error">
      This is a proof-of-concept. Use at your own risk. Also, don't use with sensitive documents.
    </div>
    <input type="submit" value="Log out" class="btn" onclick="window.location='logout.php';"/>
  </div>
  <div class="container">
    <table class="common-table zebra-striped">
      <tr>
        <th>Filename</th>
        <th>Description</th>
        <th>Created on</th>
        <th>Updated on</th>
      </tr>

<?php
    foreach ($tree['file_id'] as $file_number => $file_id) {
      $params = array('api_key' => $api_key,
          'auth_token' => $auth_token,
          'file_id' => $file_id);
      $fileinfo = $box->makeRequest('action=get_file_info', $params);
      
      foreach ($fileinfo as $a) {
        switch ($a['tag']) {
          case 'FILE_NAME':
            $file_name = $a['value'];
            break;
          case 'DESCRIPTION':
            $description = $a['value'];
            break;
          case 'CREATED':
            $created_on = date('M j, Y \a\t g:ia', $a['value']);
            break;
          case 'UPDATED':
            $updated_on = date('M j, Y \a\t g:ia', $a['value']);
            break;            
          default:
            break;
        } // switch
      } // foreach
?>
      <tr>
        <td><a href="collaborate.php?id=<?php echo $file_id; ?>"><?php echo $file_name; ?></a></td>
        <td><?php echo $description; ?></td>
        <td><?php echo $created_on; ?></td>
        <td><?php echo $updated_on; ?></td>
      </tr>
<?php      
    } // foreach
?>
    </table>

<?php } // $is_logged_in? ?>
<?php if (Config::isProductionEnv() && file_exists('google_analytics.php')) {
	include('google_analytics.php');
} ?>
</body>
</html>
