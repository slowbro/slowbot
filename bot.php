#!/usr/bin/php -q
<?php
require_once("classes/bot.class.php");
require_once("classes/vote.class.php");
$bot = new Bot();
$vote = new voting('45');

//settings
include("config.php");

//init
$bot->parent = posix_getpid();
$bot->init();
//fork
if($bot->pid){
    $bot->addHook('!help','hooks/help.hook.php', TRUE);
    //$bot->addHook('h','$this->privmsg($channel, "h");');
    $bot->addHook('penis','$this->privmsg($channel, "YES");');
    $bot->addHook('pump','$this->privmsg($channel, "YES");');
    //admin
    $bot->addHook('!join','hooks/admin.hook.php', TRUE);
    $bot->addHook('!part','hooks/admin.hook.php', TRUE);
    $bot->addHook('!nick','hooks/admin.hook.php', TRUE);
    $bot->addHook('!restart','hooks/admin.hook.php', TRUE);
    $bot->addHook('!votestop','hooks/admin.hook.php', TRUE);
    //fun
    $bot->addHook('same','$this->privmsg($channel, "resame");');
    $bot->addHook('!figlet','hooks/figlet.hook.php',TRUE);
    $bot->addHook('!cowsay','hooks/cowsay.hook.php', TRUE);
    $bot->addHook('!hook','hooks/hook.hook.php', TRUE);
    $bot->addHook('!ascii','hooks/ascii.hook.php',TRUE);
    $bot->addHook('h','$this->privmsg($channel, "h");');
    //vote
    $bot->addHook('!vote','hooks/vote.hook.php',TRUE);
    //loop
    $bot->addLoopItem('include("loops/vote.loop.php");');
    $bot->connect();
} else {
sleep(3);
while(1){
    if(!$cf['input'])
        break;
	$stat = `ps -p {$bot->parent} | grep bot.php`;
	if(empty($stat)){
		$bsod = file("ascii/bsod.txt.speshul");
		foreach($bot->_channels as $channel){
			echo $channel;
			foreach($bsod as $value){
				$bot->privmsg($channel, $value);
			}
		}
		print_r($bot->_channels);
		
		$bot->send("QUIT :ERROR:Parent died\n");
		exit();
	}
        $in = fread(STDIN, 1024);
        if($in){
        	$del = array("\r\n", "\n", "\r");
	        $in = str_replace($del, "", $in);
        	$in = $in . "\r\n";
		if(substr($in, 0, 1) == "/"){
        		$in = substr($in, 1);
			$ex = explode(" ", $in);
			switch($ex['0']){
			case 'quit';
			$message = substr($in, 5);
			$bot->send("QUIT :$message");
			break;
			case 'set';
			$in = substr($in, 4);
                        $_in = explode(" ", $in);
	                        switch($_in['0']){
        	                case 'chan';
		                $send_channel = trim($_in[1]);
		                echo "Channel set to $send_channel\n";
		                break;
		                }
			break;
			case 'nick';
			$bot->send("NICK $ex[1]");
			break;
			
			case 'raw';
			$message = substr($in, 4);
			$bot->send($message);
			break;

			case 'msg';
			$who = $ex['1'];
			$ln = strlen($who)+5;
			$message = substr($in, $ln);
			$bot->send("PRIVMSG $who :$message");
			break;
			default:
			echo "Unknown command: $ex[0]";
			}
        	} else {
        		if(isset($send_channel)){
	           		$bot->send("PRIVMSG $send_channel :$in", FALSE);
	        	} else {
 		        	echo "No channel selected! Use /set chan <#channel> to select.\n";
        		}
       		}
        }
        usleep(700);
}

exit();
}
?>
