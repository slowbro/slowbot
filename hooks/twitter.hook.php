<?php
if(empty($this->twitter_oauth)){
include_once "lib/OAuthStore.php";
include_once "lib/OAuthRequester.php";

// register at http://twitter.com/oauth_clients and fill these two 
define("TWITTER_CONSUMER_KEY", "user");
define("TWITTER_CONSUMER_SECRET", "passwd");

define("TWITTER_OAUTH_HOST","https://twitter.com");
define("TWITTER_REQUEST_TOKEN_URL", TWITTER_OAUTH_HOST . "/oauth/request_token");
define("TWITTER_AUTHORIZE_URL", TWITTER_OAUTH_HOST . "/oauth/authorize");
define("TWITTER_ACCESS_TOKEN_URL", TWITTER_OAUTH_HOST . "/oauth/access_token");
define("TWITTER_PUBLIC_TIMELINE_API", TWITTER_OAUTH_HOST . "/statuses/public_timeline.json");
define("TWITTER_UPDATE_STATUS_API", TWITTER_OAUTH_HOST . "/statuses/update.json");

define('OAUTH_TMP_DIR', function_exists('sys_get_temp_dir') ? sys_get_temp_dir() : realpath($_ENV["TMP"])); 

// Twitter test
$options = array('consumer_key' => TWITTER_CONSUMER_KEY, 'consumer_secret' => TWITTER_CONSUMER_SECRET);
OAuthStore::instance("2Leg", $options);

try
{
        // Obtain a request object for the request we want to make
        $request = new OAuthRequester(TWITTER_REQUEST_TOKEN_URL, "POST");
        $result = $request->doRequest(0);
        parse_str($result['body'], $this->t_params);

}
catch(OAuthException2 $e)
{
        echo "Exception" . $e->getMessage();
}
}

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
    $request = new OAuthRequester(TWITTER_UPDATE_STATUS_API, 'POST', $status);
    $result = $request->doRequest();


} else {
$this->privmsg($channel, 'Woah, slow down there.');
}
?>
