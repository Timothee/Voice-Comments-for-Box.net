<?php

require "config.php";

require_once('boxlibphp5.php');

$api_key            = Config::boxDotNetApiKey();
$auth_token         = $_GET['auth_token'];
$file_id            = $_GET['file_id'];
$recording_url      = $_GET['RecordingUrl'];
$recording_duration = $_GET['RecordingDuration'];
$selection_text     = $_GET['selection_text'];

$lines = preg_split('/\n/', $selection_text);
$selection_text = $lines[0];
$box = new boxclient($api_key, $auth_token);

$keys = parse_url($recording_url, PHP_URL_PATH);
$path = explode("/", $keys);
$recording_id = end($path);


// Saving to DB
$db = new PDO("sqlite:../db/voicecomments.db");

$new_comment = $db->prepare("INSERT INTO VoiceComments (fileId, selectionText, recordingUrl) VALUES (:fileId, :selectionText, :recordingUrl);");
$new_comment->bindParam(':fileId', $file_id);
$new_comment->bindParam(':selectionText', $selection_text);
$new_comment->bindParam(':recordingUrl', $recording_url);

$new_comment->execute();


// Saving to Box.net
if ($recording_duration > 120) {
  $params = array('api_key' => $api_key,
    'auth_token' => $auth_token,
    'target'     => 'file',
    'target_id'  => $file_id);
  if ($selection_text) {
    $params['message'] = "I recorded the following comment: http://".$_SERVER['SERVER_NAME']."collaborate.php?id=$file_id#$recording_id";
  } else {
    $params['message'] = "I recorded the following comment: $recording_url";
  }

  $adding_comment = $box->makeRequest('action=add_comment', $params);
}

require	"Services/Twilio.php";

$response = new Services_Twilio_Twiml();
$response
  ->hangup();

print $response;

?>
