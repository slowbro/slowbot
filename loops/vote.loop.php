<?php
global $vote;
if($vote->active){
	if(time() - $vote->start > $vote->limit){
		$vote->stop($vote->channel, true);
	}
}
?>
