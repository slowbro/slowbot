<?php
if(empty($this->lastfiglet)){
        $this->lastfiglet = time()-30;
}
if((time() - $this->lastfiglet) > 3){
$buffer = $this->buffer;
$string = explode("!figlet ", $buffer);
$string = $string['1'];
$string = str_replace("'", "'\''", $string);
$f = explode("-f ", $string);
if(count($f) > "1"){
$f2 = explode(" ", $f['1']);
$font = escapeshellcmd($f2['0']);
$string = substr($string, strlen($font)+4);
}
if($string != ""){
$figlet = exec("figlet ".(@$font?"-f $font ":@$nothing)."'".$string."' 2>&1", $figout);

foreach($figout as $data){
if($data == ''){
$data = " ";
}
$this->send("PRIVMSG $channel :$data");
$this->lastfiglet = time();
}
} else {
$this->send("PRIVMSG $channel :You need to supply some text...");
}
} else {
$this->privmsg($channel, "Whoa, slow down there.");
}
?>
