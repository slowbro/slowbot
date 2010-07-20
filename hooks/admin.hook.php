<?php
if($this->isAdmin()){
switch($hook){

case '!join':
if(in_array($this->buffex['4'], $this->_channels)){
$this->send("PRIVMSG $channel I'm already in " . $this->buffex['4'] . ", DUMB SHIT.");
break;
}
if($this->buffex['4'] == "0" || strpos($this->buffer, ",")){
$this->send("PRIVMSG $channel Don't be a dick.");
} elseif( $this->buffex['4'][0] != "#"){
$this->send("PRIVMSG $channel xcus me sir, dats not a channel");
} else {
$this->send("JOIN " . $this->buffex['4']);
$this->send("PRIVMSG $channel OK, I joined " . $this->buffex['4']);
$this->addChannel($this->buffex['4']);
}
break;

case '!part':
if($this->buffex['4'][0] != "#"){
$this->send("PRIVMSG $channel xcus me sir, dats not a channel");
} else {
if(!in_array($this->buffex['4'], $this->_channels)){
$this->send("PRIVMSG $channel I'M NOT IN " . $this->buffex['4'] . ", IDIOT.");
break;
}
$this->send("PART " . $this->buffex['4']);
$this->send("PRIVMSG $channel OK, I parted " . $this->buffex['4']);
$this->removeChannel($this->buffex['4']);
}
break;

case '!nick':
$this->send("NICK ".$this->buffex['4']);
break;

case '!restart':
posix_kill($this->pid, 9);
$this->send("QUIT :Restarting");
include("bot.php");
break;

case '!votestop':
global $vote;
$vote->stop($vote->channel, true);
break;

case '!clrdj':
$wh = fopen("includes/dj.db", "wb");
fwrite($wh, 'AutoDJ');
fclose($wh);
$this->privmsg($channel, "$this->sendnick: DJ cleared.");
break;

default:
$this->send("PRIVMSG $channel :SHUT UP");
}
}
?>
