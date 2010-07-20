<?php
$pdj = file("includes/dj.db");
$dj = @$pdj['0'];
if($hook == "!dj"){
  if($dj == "" || !isset($dj)){
    $this->privmsg($channel, "$this->sendnick, I do not know who the DJ is.");
  }
  else {
    $this->privmsg($channel, "$this->sendnick, the DJ is $dj.");
  }
} else {
echo 'cock';
$this->send("NAMES $channel", FALSE);
$buffer = fgets($this->socket, 1024);
$test = fgets($this->socket, 1024);
if(strpos($test, "/NAMES") === FALSE){
  $buffer2 = explode(" $channel :", $test);
  $buffer .= " ".$buffer2;
}
$prenames = explode(" $channel :", $buffer);
$names = explode(" ", $prenames[1]);
$num_names = count($names);
$isowner = 0;
for($namesi = 0; $namesi <= $num_names; $namesi++){
  $names_del = array("@", "~", "%", "+", "&");
  $name_stripped = str_replace($names_del, "", @$names[$namesi]);
  if($name_stripped == $this->sendnick){
    $test = $names[$namesi];
    if($names[$namesi] == "@".$this->sendnick){
      $isop = 1;
      break;
    } elseif($names[$namesi] == "~".$this->sendnick) {
      $isop = 1;
      $isowner = 1;
      break;
    } elseif($names[$namesi] == "&".$this->sendnick){
      $isop = 1;
      break;
    } else {
      $isop = 0;
    }
  } else {
   $isop = 0;
  }
}
if($isop == 1){
if($hook == "!djon") {
      if($isowner == 1 && !empty($this->buffex['4'])){
        $dj = $this->buffex['4'];
        $wh = fopen("includes/dj.db", "wb");
        fwrite($wh, $dj);
        fclose($wh);
        $this->privmsg($channel, "$dj is now DJing!");
      } else {
      if($dj == "" || !isset($dj) || $dj == "AutoDJ"){
      $dj = $this->sendnick;
      $wh = fopen("includes/dj.db", "wb");
      fwrite($wh, $dj);
      fclose($wh);
      $this->privmsg($channel, "$dj is now DJing!");
      } elseif($dj != $this->sendnick) {
        $this->privmsg($channel, "$this->sendnick: $dj is already on!");	
      } elseif($dj == $this->sendnick) {
        $this->privmsg($channel, "$this->sendnick: you are already DJing!");
      }
      }
    }
  elseif($hook == "!djoff") {
    if(empty($dj) || $dj == "AutoDJ"){
    }
    else {
    if($dj == $this->sendnick) {
      $wh = fopen("includes/dj.db", "wb");
      fwrite($wh, 'AutoDJ');
      fclose($wh);
      $this->privmsg($channel, "$dj is no longer DJing.");
      $dj = "";
    } elseif ($isowner == 1){
      $wh = fopen("includes/dj.db", "wb");
      fwrite($wh, 'AutoDJ');
      fclose($wh);
      $this->privmsg($channel, "$this->sendnick: $dj is no longer DJing.");
      $dj = "";
    }
  }
  }
} else {
echo 'h';
}
}
?>
