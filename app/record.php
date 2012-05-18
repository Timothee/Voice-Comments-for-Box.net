<?php
require "Config.php";
require	"Services/Twilio.php";

$file_id    = $_GET['file_id'];
$auth_token = $_GET['auth_token'];
$is_text_selected = $_GET['is_text_selected'];

if ($is_text_selected) {
  // $anchor_node    = $_GET['anchor_node'];
  // $anchor_offset  = $_GET['anchor_offset'];
  // $focus_node     = $_GET['focus_node'];
  // $focus_offset   = $_GET['focus_offset'];
  $selection_text = $_GET['selection_text'];
}


$response = new Services_Twilio_Twiml();
if ($is_text_selected) {
  $response
  ->record(array(
    'action' => Config::root_url."/recording_finished.php?file_id=$file_id&auth_token=$auth_token&selection_text=$selection_text",
    'method' => 'GET',
    'timeout' => '30',
    'transcribe' => 'true',
    'transcribeCallback' => Config::root_url."/transcription_finished.php?file_id=$file_id&auth_token=$auth_token&selection_text=$selection_text"));  
} else {
  $response
  ->record(array(
    'action' => Config::root_url."/recording_finished.php?file_id=$file_id&auth_token=$auth_token",
    'method' => 'GET',
    'timeout' => '30',
    'transcribe' => 'true',
    'transcribeCallback' => Config::root_url."/transcription_finished.php?file_id=$file_id&auth_token=$auth_token"));
}

print $response;

?>
