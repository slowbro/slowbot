<?php
$time = microtime();

$time = explode(" ", $time);

$time = $time[1] + $time[0];

$start = $time;

if(file_exists("ascii/".$this->buffex['4'].".txt")){
	$file = file("ascii/".$this->buffex['4'].".txt");
	foreach($file as $value){
		$this->privmsg($channel, $value);
	}
$time = microtime();

$time = explode(" ", $time);

$time = $time[1] + $time[0];

$finish = $time;

$totaltime = ($finish - $start);
//$this->privmsg($channel, "Time elapsed: $totaltime");
} else {
	$this->privmsg($channel, "Could not stat '".$this->buffex['4'].".txt'.");
}
?>
