<?php
switch($this->buffex['4']){

case 'list';
$hooks = array_keys($this->hooks);
$this->send("PRIVMSG $channel :Hooks I have installed:");
foreach($hooks as $value){
$this->send("PRIVMSG $channel :$value: ".$this->hooks[$value]['status']);
}
break;

case 'disable':
if(!$this->isAdmin()){
break;
}
$this->disableHook($this->buffex['5'], $channel);
break;

case 'enable':
if(!$this->isAdmin()){
break;
}
$this->enableHook($this->buffex['5'], $channel);
break;

case 'add':
if(!$this->isAdmin()){
break;
}
if(strtolower($this->buffex['6']) == "file"){
	$bool = (bool)TRUE;
} else {
	$bool = (bool)FALSE;
}
$what = explode($this->buffex['5']." ".$this->buffex['6']." ", $this->buffer);
$this->addHook($this->buffex['5'], $what['1'], $bool);
break;

case 'addregex':
if(!$this->isAdmin()){
break;
}
if(strtolower($this->buffex['6']) == "file"){
	$bool = (bool)TRUE;
} else {
	$bool = (bool)FALSE;
}
$what = explode($this->buffex['5']." ".$this->buffex['6']." ", $this->buffer);
$this->addRegexHook($this->buffex['5'], $what['1'], $bool);
}
?>
