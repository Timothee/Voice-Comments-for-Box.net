<?php

require "config.php";

session_start();

require	"Services/Twilio.php";

list($sid, $token, $application_sid) = Config::twilioConfig();

$capability = new Services_Twilio_Capability($sid, $token);
$capability->allowClientOutgoing($application_sid);

$capabilityToken = $capability->generateToken();


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

if (!$is_logged_in) {
  header('Location: index.php');
}

$file_id = $_GET['id'];

$file_content = file_get_contents("https://www.box.net/api/1.0/download/$auth_token/$file_id");
$file_content = htmlspecialchars($file_content);

$db = new PDO("sqlite:../db/voicecomments.db");
$comments = $db->prepare("SELECT * FROM VoiceComments WHERE fileId = :fileId");
$comments->bindParam(':fileId', $file_id);
$comments->execute();

while ($comment = $comments->fetch()) {
  $selected_text = $comment['selectionText'];
  $recording_url = $comment['recordingUrl'];
  $keys = parse_url($recording_url, PHP_URL_PATH);
  $path = explode("/", $keys);
  $recording_id = end($path);
  
  if (trim($selected_text) != '') {
    $file_content = str_replace($selected_text, "<span><span class='highlight'>".$selected_text."</span><span id='".$recording_id."' class='recording'><audio controls='controls'><source src='".$recording_url.".mp3' type='audio/mp3'/></audio></span></span>", $file_content);
  } // if
} // while
?>
<html>
<head>
  <title>Voice Comments for Box</title>
  <link rel="stylesheet" type="text/css" href="bootstrap-1.0.0.min.css" />
  <link rel="stylesheet" type="text/css" href="style.css" />
	<script src="swfobject.js" type="text/javascript" charset="utf-8"></script>
	<script src="jquery-1.6.2.min.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" src="scripts/shCore.js"></script>
	<script type="text/javascript" src="scripts/shAutoloader.js"></script>
	<script type="text/javascript" src="scripts/shBrushPhp.js"></script> 
	<link type="text/css" rel="stylesheet" href="styles/shCoreDefault.css"/> 
	<script type="text/javascript" src="http://static.twilio.com/libs/twiliojs/1.0/twilio.min.js"></script>
  <!-- <script type="text/javascript">SyntaxHighlighter.all();</script>  -->
</head>
<body class="collaborate">
  <div class="topbar">
    <div class="container">
      <h3 style="display: inline; float:left; margin-right:20px"><a href="index.php">&laquo; Back to list</a></h3>
      <!-- <form action onsubmit="return false;">
        <label for="pageurl">Send this link to collaborate:</label>
        <input id="pageurl" type="text" value="<?php echo "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']; ?>" onclick="$(this).select();"/>
      </form> -->
      <input id="recordTop" type="submit" value="Record" class="primary btn small record"/>
      <input type="submit" value="Log out" class="btn small" onclick="window.location='logout.php';"/>
    </div>
  </div>
  <div class="container-fluid">
    <div class="sidebar">
      Press "Record" whenever you want to add a comment to this file. It will be added on Box.net as a comment with a link to the recording.
    </div>
    <div id="filecontent" class="content filecontent">
      <pre class="brush: php">
        <?php echo $file_content; ?>
      </pre>
    </div>
    <div id="floatingRecord">
      <span>
        <input type="submit" value="Record" class="primary btn small record"/>
      </span>
    </div>
  </div>
<script type="text/javascript" charset="utf-8">
  function getTextSelection() {
    if (window.getSelection) {
      return window.getSelection();
    } else if (document.getSelection) {
      return document.getSelection();
    } else {
      var selection = document.selection && document.selection.createRange();
      if (selection.text) {
        return selection.text;
      }
      return false;
    }
    return false;
  } // getTextSelection

  var floatingRecordY;
  
  $('#filecontent').mousedown(function(event) {
    floatingRecordY = event.clientY + $("body").scrollTop();
  });

  $('#filecontent').mouseup(function(event){
    var selection;
    if (selection = getTextSelection()) {
      if (!selection.isCollapsed) {
    		$('#floatingRecord').show().animate({top: floatingRecordY+"px"}, 200);
      } else {
        if (Twilio.Device.status() != "busy") {
          $('#floatingRecord').hide();
        }
      }
    }
    return false;
  });
  
  Twilio.Device.setup('<?php echo $capabilityToken; ?>');
  $('.record').bind('click', record);
  
  function record() {
    var selection = getTextSelection();
    if (!selection.isCollapsed) {
  		var connection = Twilio.Device.connect({
  		  "file_id": "<?php echo $file_id ?>",
  		  "auth_token": "<?php echo $auth_token; ?>",
  		  "is_text_selected": "1",
  		  "selection_text": escape(selection.toString().split(/\n/, 1)[0])
      });
    } else {
      var connection = Twilio.Device.connect({
        "file_id": "<?php echo $file_id ?>",
        "auth_token": "<?php echo $auth_token; ?>",
  		  "is_text_selected": "0"
      });
    }
    
		$('.record').attr('value', 'Stop').unbind('click', record).bind('click', stop);
  } // record
  
  function stop() {
    Twilio.Device.disconnectAll();
    $('.record').attr('value', 'Record').unbind('click', stop).bind('click', record);
  } // stop
  
  if (location.hash != "") {
    $(location.hash+' audio').each(function() {this.play();});
  }
  
</script>
<?php if (Config::isProductionEnv() && file_exists('google_analytics.php')) {
	include('google_analytics.php');
} ?>
</body>
</html>