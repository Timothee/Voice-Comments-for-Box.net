<?php

require "config.php";

require_once('boxlibphp5.php');

$api_key              = Config::boxDotNetApiKey();
$auth_token           = $_GET['auth_token'];
$file_id              = $_GET['file_id'];
$transcription_text   = $_POST['TranscriptionText'];
$transcription_status = $_POST['TranscriptionStatus'];
$recording_url        = $_POST['RecordingUrl'];
$selection_text     = $_GET['selection_text'] || '';

$box = new boxclient($api_key, $auth_token);

$keys = parse_url($recording_url, PHP_URL_PATH);
$path = explode("/", $keys);
$recording_id = end($path);

$params = array('api_key' => $api_key,
  'auth_token' => $auth_token,
  'target'     => 'file',
  'target_id'  => $file_id);

if ($selection_text) {
  $url = "http://".$_SERVER['SERVER_NAME']."/collaborate.php?id=$file_id#$recording_id";
} else {
  $url = $recording_url;
}

if ($transcription_status == 'failed') {
  $params['message'] = "I recorded the following comment: $url";
} else {
  $params['message'] = "\"$transcription_text\" (Play message: $url )";
  // $params['message'] = "transcription";
}
$adding_comment = $box->makeRequest('action=add_comment', $params);

?>
