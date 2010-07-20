<?php
switch(@$this->buffex['4']){

case 'create';
global $vote;
$p = explode("create ", $this->buffer);
if($vote->active){
	$this->privmsg($channel, "$this->sendnick: Vote already in progress in $vote->channel, I can only do one vote at a time currently.");
	break;
}
if(empty($p['1'])){
	$this->privmsg($channel, "You're missing some parameters (hint: the only parameter you need).");
	break;
}
$h = explode("::",$p['1']);
$p = array();
foreach($h as $value){
	if(!empty($value)){
		$p[] = $value;
	}
}
if(count($p) < 3){
	$this->privmsg($channel, "$this->sendnick: You need at least two options.");
        break;
}
$vote->create($channel, $p);
break;

case 'stop';
global $vote;
$vote->stop($channel);
break;

case 'help':
global $vote;
$vote->help($channel);
break;
default:
global $vote;
if(!$vote->active){
	$this->privmsg($channel, "$this->sendnick: No active vote. Try !vote help.");
} else {
	$vote->vote($channel, $this->buffex['4']);
}
}// end switch

?>
