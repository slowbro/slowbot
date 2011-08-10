<?php
declare(encoding='UTF-8');
class Bot {

var $host;
var $port;
var $nick;
var $pass;
var $channels;
var $_channels = array();
var $char;
var $socket;
var $pid;
var $buffer;
var $hooks = array();
var $parent;
var $loop;
var $admins = array();

function init(){
    $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	socket_connect($this->socket, $this->host, $this->port);
    socket_set_nonblock($this->socket);
	$this->pid = pcntl_fork();
    usleep(500);
}

function connect(){
	if($this->socket){
	$this->send("NICK $this->nick");
	$this->send("USER $this->nick dix dix LIKES THE COCK");
	while(true){
        $reads = array($this->socket);
        if(socket_select($reads, $nul = null, $nul = null, 0) > 0){
		$this->buffer = str_replace(array("\n","\r"), "", socket_read($this->socket, 1024, PHP_NORMAL_READ));
		if(substr($this->buffer,0,6) == "PING :"){
			$this->parseOutput("PING");
        	        $this->send("PONG :".substr($this->buffer,6), FALSE);
			echo " --> PONG\n";
                } else {
		$this->buffex = explode(" ", $this->buffer);
		$this->sender = substr($this->buffex['0'], 1);
		$this->sendprefix = explode("!", $this->sender); $this->sendprefix = @$this->sendprefix['1'];
		$this->sendnick = explode("!", $this->sender); $this->sendnick = @$this->sendnick['0'];
		$this->command = @$this->buffex['1'];
		$this->target = @$this->buffex['2'];
		$this->parseOutput($this->command);
        }
        }
		foreach($this->loop as $val){
			@eval($val);
		}
		usleep(500);
	}
	echo "Retrying connection after 5 seconds...";
	sleep(5);
	include("bot.php");
	posix_kill($this->pid, 9);
	}
}

function addChannel($channel){
	if(!in_array($channel, $this->_channels)){
	$this->_channels[] = $channel;
	}
}

function removeChannel($channel){
        if(in_array($channel, $this->_channels)){
        $key = array_search($channel, $this->_channels);
	unset($this->_channels[$key]);
	}
}


function join($channel){
	$this->send("JOIN $channel");
}

function parseOutput($command){
	switch($command){
	case '001':
        case '002':
	case '003':
	case '251':
	case '255':
	case '265':
	case '266':
	$this->message = explode("$this->nick :", $this->buffer);
        echo "\033[1;35m*\033[0m " . $this->message['1'] . "\n";
        break;

	case '376': //end of motd
	case '422': //no motd error
	$this->message = explode("$this->nick :", $this->buffer);
        echo "\033[1;35m*\033[0m " . $this->message['1'] . "\n";
	if(!empty($this->oper)){
		$oper = explode(":", $this->oper);
		$this->send("OPER $oper[0] $oper[1]");
	}
	if(!empty($this->pass)){
		$this->send("PRIVMSG nickserv identify $this->pass");	
	}
	usleep(700);
	foreach($this->channels as $channel){
	$this->join($channel);
	}
	break;

        case '004':
        case '005':
	case '252':
	case '253':
	case '254':
	case '366':
        $this->message = explode("$this->nick ", $this->buffer);
        echo "\033[1;35m*\033[0m " . $this->message['1'] . "\n";
        break;
	case '332': //topic
		$this->message = explode(" ", $this->buffer);
		$topic = explode($this->message['3']." :", $this->buffer);
		echo "\033[1;35m*\033[0m Topic for " . $this->message['3'] . ": " . $topic['1'] . "\n";
	break;
	case '333'; //topic set by
		#:irc.711chan.org 333 slowbot #goodjob Frank 1277632322
		$channel = $this->buffex['3'];
		$user = $this->buffex['4'];
		$when = date("M d, Y \a\\t H:i:s", $this->buffex['5']);
		echo "\033[1;35m*\033[0m Topic set by ". $user . " on " . $when . "\n";

	break;
	case '353'; //names
        $msg = explode("353 $this->nick = ", $this->buffer);
        if(!isset($msg['1'])){ $msg = explode("353 $this->nick * ", $this->buffer);}
	if(!isset($msg['1'])){ $msg = explode("353 $this->nick @ ", $this->buffer);}
        $msg = explode(" :", $msg['1']);
        $channel = $msg['0'];
        $users = explode(" ", $msg['1']);
        echo "\033[0;35m*\033[0m Users in $channel:\n";
        //find longest string
        $mapping = array_combine($users, array_map('strlen', $users));
        $longest = array_keys($mapping, max($mapping));
        $width = strlen($longest['0']);
        $users = array_pad($users, -(count($users)+1), 0);
        for($i=1;$i<count($users)-1;$i++){
        if(($i%4) == "0" && $i != "0"){ //start a new row
        $userlen = strlen($users[$i]);
        $diff = $width - $userlen;
        if($diff%2){
        $left = floor($diff/2);
        $right = $diff - $left;
        } else {
        $left = $diff/2;
        $right = $diff/2;
        }
        echo "[ ";
        for($o=0;$o<$left;$o++){
        echo " ";
        }
        echo $users[$i];
        for($o=0;$o<$right;$o++){
        echo " ";
        }
        echo " ]\n";
        } elseif($i == count($users)-2){
        $userlen = strlen($users[$i]);
        $diff = $width - $userlen;
        if($diff%2){
        $left = floor($diff/2);
        $right = $diff - $left;
        } else {
        $left = $diff/2;
        $right = $diff/2;
	}
        echo "[ ";
        for($o=0;$o<$left;$o++){
        echo " ";
        }
        echo $users[$i];
        for($o=0;$o<$right;$o++){
        echo " ";
        }
        echo " ]\n";

        } else {
        $userlen = strlen($users[$i]);
        $diff = $width - $userlen;
        if($diff%2 != "0"){
        $left = floor($diff/2);
        $right = $diff - $left;
        } else {
        $left = $diff/2;
        $right = $diff/2;
        }
        echo "[ ";
        for($o=0;$o<$left;$o++){
        echo " ";
        }
        echo $users[$i];
        for($o=0;$o<$right;$o++){
        echo " ";
        }
        echo " ]";
        }
        }
	break;
	
	case 'INVITE':
	if($this->target == $this->nick){
		$chan = $this->buffex['3'];
		if(!empty($chan)){
			if($chan[0] == ":"){
				$chan = substr($chan, 1);
			}
			$this->join($chan);
		}
	}
	break;
	case 'JOIN':
	$address = explode("!", $this->sender); $address = $address['1'];
        $channel = substr($this->target, 1);
	if($this->sendnick == $this->nick){ //we were forced to join a channel
	$this->addChannel($channel);
	}
	echo "\033[0;32m--> $this->sendnick has joined $channel ($address)\033[0m\n";
	break;
	
	case 'KICK':
	if($this->buffex['3'] == $this->nick){
		$this->removeChannel($this->target);
	}
	if(count($this->buffex) > 4){
		$buf = $this->buffex;
		for($i=0;$i<4;$i++){
			unset($buf[$i]);
		}
		$message = implode(" ", $buf);
		$message = ($message[0] == ":"?substr($message, 1):$message);
	}
	echo "\033[1;31m<-- $this->sendnick has kicked {$this->buffex['3']} from {$this->target}".(!empty($message)?" ($message)":"")."\033[0m\n";
	break;
	case 'NICK':
	$newnick = substr($this->target, 1);
	if($this->sendnick == $this->nick){//our nick was changed
                $this->nick = $newnick;
        }
	echo "\033[1;34m$this->sendnick\033[0m is now known as \033[1;34m$newnick\033[0m\n";
        break;
	
	case 'NOTICE':
	if($this->target == "AUTH"){
	$this->message = explode("NOTICE AUTH :", $this->buffer);
	$this->message['1'] = $this->message['1'];
	$this->sendnick = explode("!", $this->sender);
        echo "\033[1;35m*\033[0m " . $this->message['1'] . "\n";
	} elseif(@$this->buffex['3'] == ":***"){
	$this->message = explode("$this->nick :", $this->buffer);
	echo "\033[1;35m*\033[0m " . @$this->message['1'] . "\n";
	} else {
	$this->message = explode("$this->nick :", $this->buffer);
	$this->sendnick = explode("!", $this->sender);
	echo "\033[1;34m-\033[0;35m" . $this->sendnick['0'] . "\033[1;34m-\033[0m " . $this->message['1'] . "\n";
	}
	break;
	
	case 'PING':
	echo "PING";
	break;

	case 'PRIVMSG':
        $this->message = explode("PRIVMSG $this->target :", $this->buffer);
		$this->message['1'] = $this->colorize($this->message['1']);
    echo "<\033[1;34m" . $this->sendnick . "@$this->target\033[0m> " . $this->message['1'] . "\033[0m\033[40m\n";

    foreach($this->_channels as $channel){
        foreach($this->hooks as $k => $v){
            if(@$v['regex'] == true){
                if(preg_match($v['trigger'], end(explode("PRIVMSG $this->target :", $this->buffer)))){
                    if($v['type'] == 'file'){
                        $this->doHook($v['trigger'], $channel, TRUE);
                    } else {
                        $this->doHook($v['trigger'], $channel);
                    }
                }
            } else {
                $this->firstword = strtolower(substr($this->buffex['3'], 1));
                if($this->firstword == $v['trigger'] && $this->target == $channel){
                    if($v['type'] == 'file'){
                        $this->doHook($v['trigger'], $channel, TRUE);
                    } else {
                        $this->doHook($v['trigger'], $channel);
                    }
                }
            }
        }
    }

	break;
	
	case 'QUIT':
        $address = explode("!", $this->sender); $address = $address['1'];
        echo "\033[1;31m<-- $this->sendnick has quit ($address)\033[0m\n";
	break;
	
	default:
	if(!empty($this->buffer)){
	echo $this->buffer . "\n";
	}
	}
}

function privmsg($channel, $message){
	$cmd = "PRIVMSG $channel :$message";
	$this->send($cmd);
}

function send($cmd, $output=TRUE){
	$cmd = $cmd . "\r\n";
    socket_set_block($this->socket);
	socket_write($this->socket, $cmd);
    socket_set_nonblock($this->socket);
	if($output){
		echo trim($cmd) . "\n";
	}
}

function isAdmin(){
	global $channel;
	if(array_search($this->sendnick, $this->admins) !== FALSE){
		return TRUE;
	} else {
		$this->send("PRIVMSG $this->target :$this->sendnick: sorry, you don't have access to that command.");
		return FALSE;
	}
}

function addLoopItem($ev){
	$this->loop[] = $ev;
}

function deleteHook($com){
    if(!($k = $this->hookExists($com))){
        $this->send("PRIVMSG $channel :Hook $com does not exist.");
    } else {
        unset($this->hooks[$k]);
        $this->send("PRIVMSG $channel :Hook $com deleted.");
    }
}

function hookExists($com){
    foreach($this->hooks as $k => $v){
        if($v['trigger'] == $com)
            return $k;
    }   
    return false;
}

function addHook($com, $file, $isfile=FALSE){
    $hook=array();
    if(!$this->hookExists($com)){
        $hook['trigger'] = $com;
        $hook['data'] = $file;
        if($isfile){
            $hook['type'] = 'file';
        } else {
            $hook['type'] = 'eval';
        }   
        $hook['status'] = 'enabled';
        $this->hooks[] = $hook;
    } else {
        echo "ERROR: attempt to create hook \"$com\" failed: collision with pre-existing hook.\n";
        return false;
    }
    return true;
}

function addRegexHook($com, $file, $isfile=FALSE){
    if($this->addHook($com, $file, $isfile))
        $this->hooks[$this->hookExists($com)]['regex'] = TRUE;
}

function disableHook($hook, $channel){
    if(($k = $this->hookExists($hook))){
        $this->hooks[$k]['status'] = 'disabled';
        $this->send("PRIVMSG $channel :Hook '$hook' disabled.");
    } else {
        $this->send("PRIVMSG $channel :Hook '$hook' does not exist.");
    }
}

function enableHook($hook, $channel){
        if(($k = $this->hookExists($hook))){
                $this->hooks[$k]['status'] = 'enabled';
                $this->send("PRIVMSG $channel :Hook '$hook' enabled.");
        } else {
                $this->send("PRIVMSG $channel :Hook '$hook' does not exist.");
        }
}

function doHook($hook, $channel, $file=FALSE){ 
    $k = $this->hookExists($hook);
    if($this->hooks[$k]['status'] == 'enabled'){
        if(!$file){
            try {
                eval($this->hooks[$k]['data']);
            } catch(Exception $ex){
                $this->privmsg($channel, "Caught exception: $ex");
            }
        } else {
            try {
                include($this->hooks[$k]['data']);
            } catch(Exception $ex){
                $this->privmsg($channel, "Caught exception: $ex");
            }
        }
    }
}

function colorize($msg){
$font = array(
   //       "\x03" => "^C",
          "\x0f" => "\033[0m",
          "\x02" => "",//^B
          "\x1f" => "",//^U
          "\x16" => ""//^R
        );
        $msg = strtr($msg, $font);
        while(strpos($msg, "\x03") === 0 || strpos($msg, "\x03") > 0){
$pos = strpos($msg, "\x03");
$txt = array(
"00" => "1;37",//white
"01" => "0;30",//black
"02" => "0;34",//dk blu
"03" => "0;32",//dk grn
"04" => "1;31",//red
"05" => "0;31",//dk red
"06" => "0;35",//purple
"07" => "0;33",//orange
"08" => "1;33",//yellow
"09" => "1;32",//lt grn
"10" => "0;36",//teal
"11" => "1;36",//sky
"12" => "1;34",//blu
"13" => "1;35",//pink
"14" => "1;30",//gray
"15" => "0;37"//lt gray
);
$bg = array(
"00" => "r;5;47m",//white
"01" => "r;40m",//black
"02" => "r;44m",//dk blu
"03" => "r;42m",//dk grn
"04" => "r;5;41m",//red
"05" => "r;41m",//dk red
"06" => "r;45m",//purple
"07" => "r;43m",//orange
"08" => "r;5;43m",//yellow
"09" => "r;5;42m",//lt grn
"10" => "r;46m",//teal
"11" => "r;5;46m",//sky
"12" => "r;5;44m",//blu
"13" => "r;5;45m",//pink
"14" => "r;5;40m",//gray
"15" => "r;47m",//lt gray
"nul" => "r"//no bg
);
                $cc_test = explode("\x03", substr($msg, $pos, 7));
                if(count($cc_test) > 2){
                        $cc = $cc_test['1'];
                } else {
               $cc = str_replace("\x03", "", substr($msg, $pos, 7));
               }
               if(strpos($cc, ",") === FALSE){
                if(strlen($cc) == 0){
                $cc_orig = $cc;
                $cc_final = "0m";
                $ca = "\x03";
                } else {
                $cc_orig = $cc;
                $cc = substr($cc, 0, 2);
                if(is_numeric($cc)){
                  $ca = "\x03" . $cc;
                        $cc_final = strtr($cc, $txt)."m";
                } else {
                  $cc = substr($cc, 0, 1);
                  $ca = "\x03" . $cc;
                  $cc_final = strtr("0".$cc, $txt)."m";
                }
                }
               } else {
               $cc_orig = $cc;
               $cc = explode(",", $cc);
               $ca = "\x03".$cc['0'];
               if(strlen($cc['0']) == 1){
                $cc['0'] = "0".$cc['0'];
               }
               $cc_txt = strtr($cc['0'], $txt);
               if(!isset($cc['1']) && $cc['1'] != 0){
                $cc['1'] = "nul";
                $cc_bg = strtr($cc['1'], $bg);
                if(is_numeric($cc['0'][1])){
                  $cc_txt = strtr(substr($cc['0'], 0, 2), $txt);
                  $ca = "\x03".substr($cc['0'], 0, 2);
                } else {
                  $cc_txt = strtr("0".substr($cc['0'], 0, 1), $txt);
                  $ca = "\x03".substr($cc['0'], 0, 1);
                }
               } else {
               $cc['1'] = substr($cc['1'], 0, 2);
               if(is_numeric($cc['1']) && strlen($cc['1']) < 3){
                $ca .= ','.$cc['1'];
                if(strlen($cc['1']) == 1){
                  $cc['1'] = "0".$cc['1'];
                }
                $cc_bg = strtr($cc['1'], $bg);
               } else {
                $cc['1'] = substr($cc['1'], 0, strlen($cc['1'])-1);
                if(is_numeric($cc['1'])){
                  $ca .= ','.$cc['1'];
                                if(strlen($cc['1']) == 1){
                                       $cc['1'] = "0".$cc['1'];
                          }
                  $cc_bg = strtr($cc['1'], $bg);
                } else {
                  $cc['1'] = substr($cc['1'], 0, strlen($cc['1'])-1);
                  $ca .= ','.$cc['1'];
                  if(strlen($cc['1']) == 1){
                         $cc['1'] = "0".$cc['1'];
                  }
                  $cc_bg = strtr($cc['1'], $bg);
                }
               }
               }
               $cc_final = str_replace("r", $cc_txt, $cc_bg);
               }
               if($cc_final == "m"){
                $cc_final = "0m";
               }
               $msg = str_replace($ca, "\033[$cc_final", $msg);
        }
return $msg;
}

}//end bot
?>
