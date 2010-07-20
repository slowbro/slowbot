<?php
if(empty($this->lasttweet)){
	$this->lasttweet = time()-30;
}
$msg = explode("!tweet ", $this->buffer);
$status = "[$this->sendnick"."@"."$channel] ".$msg['1'];
if(strlen($status) > 140){
	$old = $status;
	$status = substr($status, 0, 136);
	$status .= "...";
	$this->privmsg($channel, "Your message was too long. I didn't send: \"".substr($old, 136)."\"");
}
$status = urlencode(stripslashes($status));

if (strlen($status) > 0 && (time() - $this->lasttweet) > 3){
$tweetUrl = 'http://www.twitter.com/statuses/update.xml';
$this->lasttweet = time();
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, "$tweetUrl");
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, "status=$status");
curl_setopt($curl, CURLOPT_USERPWD, "chatnets:7NoCOSIYfpsZ");

$result = curl_exec($curl);
$resultArray = curl_getinfo($curl);

if ($resultArray['http_code'] == 200){
$this->privmsg($channel, '@chatnets: '.(strlen(urldecode($status))>160?substr(urldecode($status),0,160)."...":urldecode($status)));
} else {
$result = (array)simplexml_load_string($result);
$this->privmsg($channel, 'Could not post Tweet to Twitter, error returned was: '.$result['error']);
}
curl_close($curl);
} else {
$this->privmsg($channel, 'Woah, slow down there.');
}
?>
