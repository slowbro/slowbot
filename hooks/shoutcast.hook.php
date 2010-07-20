<?php
$err = false;
if(!function_exists('ConvertSeconds')){
function ConvertSeconds($seconds) {
	$tmpseconds = substr("00".$seconds % 60, -2);
	if ($seconds > 59) {
		if ($seconds > 3599) {
			$tmphours = substr("0".intval($seconds / 3600), -2);
			$tmpminutes = substr("0".intval($seconds / 60 - (60 * $tmphours)), -2);
			
			return ($tmphours.":".$tmpminutes.":".$tmpseconds);
		} else {
			return ("00:".substr("0".intval($seconds / 60), -2).":".$tmpseconds);
		}
	} else {
		return ("00:00:".$tmpseconds);
	}
}
}
global $shoutcast;
$shoutcast->host = "72.52.102.227";
$shoutcast->port = 8000;
$shoutcast->passwd = "heyguyz";

if($shoutcast->openstats()){
if ($shoutcast->GetStreamStatus()) {
$Song = $shoutcast->GetCurrentSongTitle();
$Bitrate = $shoutcast->GetBitRate();
$CurListeners = $shoutcast->GetCurrentListenersCount();
$MaxListeners = $shoutcast->GetMaxListenersCount();
$PeakListeners = $shoutcast->GetPeakListenersCount();
} else {
$this->privmsg($channel, $this->sendnick.": No one is streaming.");
$err = true;
break;
}
} else {
$this->privmsg($channel, "$this->sendnick: Error connecting to server: $shoutcast->_error");
$err = true;
}
if(!$err){
switch($hook){
case '!np';
$string = "Now playing: " . $Song . ". Current Listeners: " . $CurListeners . " Tune in! http://fm.chatnets.net:8000/listen.pls";
$this->privmsg($channel, $string);
break;
case '!listeners';
$string = "Current Listeners: " . $CurListeners . " ($PeakListeners peak)";
$this->privmsg($channel, $string);
break;
}//end switch
}//end if
?>
