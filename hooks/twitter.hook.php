<?php
require_once('lib/twitteroauth.php');

$connection = new TwitterOAuth(consumer_key, consumer_secret, oauth_token, oauth_token_secret);

/* If method is set change API call made. Test is called by default. */
$content = $connection->get('account/rate_limit_status');
echo "Current API hits remaining: {$content->remaining_hits}.";

/* Get logged in user to help with tests. */
$user = $connection->get('account/verify_credentials');

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

if (strlen($status) > 0 && (time() - $this->lasttweet) > 3){
    $parameters = array('status' => $status);
    $connection->post('statuses/update', $parameters);
} else {
    $this->privmsg($channel, 'Woah, slow down there.');
}
?>
