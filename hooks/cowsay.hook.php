<?php
if(empty($this->lastcowsay)){
        $this->lastcowsay = time()-30;
}
if((time() - $this->lastcowsay) > 3 ){
$this->lastcowsay = time();
$buffer = $this->buffer;
$string = explode("!cowsay ", $buffer);
$string = $string['1'];
$string = str_replace("'", "'\''", $string);
$f = explode("-f ", $string);
if(count($f) > "1"){
$f2 = explode(" ", $f['1']);
$font = escapeshellcmd($f2['0']);
$string = substr($string, strlen($font)+4);
$len = "-".(strlen($string)-1);
}
if($string != ""){
if(substr($string, 0, 1) == "-"){
        $string = "\\".$string;
}
$figlet = exec("cowsay ".(@$font?"-f $font ":@$nothing)."'".$string."' 2>&1", $figout);

foreach($figout as $data){
if($data == ''){
$data = " ";
}
$this->send("PRIVMSG $channel :$data");
}
} else {
$this->send("PRIVMSG $channel :You need to supply some text...");
}
} else {
$this->privmsg($channel, "Whoa, slow down there.");
}
?>
