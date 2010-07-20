<?php

class voting {

var $active;
var $creator;
var $limit;
var $voters;
var $votes;
var $question;
var $start;
var $channel;

function __construct($limit){
	$this->limit = $limit;
	$this->init();
}

function init(){
	$this->active = FALSE;
	$this->creator = NULL;
	$this->voters = array();
	$this->votes = array();
	$this->question = NULL;
	$this->options = array();
	$this->start = NULL;
	$this->channel = NULL;
}

function create($channel, $p){
	global $bot;
	$this->creator = $bot->sendnick;
	$this->question = $p['0'];
	unset($p['0']);
	$m = array();
	$m[] = "New vote started in $channel by $this->creator!";
	$m[] = "The question: {$this->question}";
	foreach($p as $key => $value){
		$m[] = "$key) $value";
		$this->options[$key] = $value;
	}
	$m[] = "To vote say !vote <option number>. You may only vote once.";
	$pm[] = "You vote in $channel has been started.";
	$pm[] = "This vote will auto-close in {$this->limit} seconds. Say !vote stop in $channel to end it sooner.";
	$pm[] = "Results will be posted to the channel and PMed to you.";
	foreach($m as $value){
		$bot->privmsg($channel, $value);
	}
	foreach($pm as $value){
        	$bot->privmsg($bot->sendnick, $value);
	}
	$this->active = TRUE;
	$this->channel = $channel;
	$this->start = time();
}

function help($channel){
	global $bot;
	$m = array();
	$m[] = "Voting System Help";
	$m[] = "syntax: !vote <command> <params>";
	$m[] = "Commands:";
	$m[] = "* create <question>::<option1>::<option2>[::<option3>...]";
	$m[] = "  - create a vote. :: is the delimiter for quesions/options.";
	$m[] = "  - by default a vote lasts {$this->limit} seconds before stopping automatically.";
	$m[] = "* stop";
	$m[] = "  - stop a vote in progress, and return the results.";
	foreach($m as $value){
		$bot->privmsg($channel, $value);
	}
}

function stop($channel, $force=false){
	global $bot;
	if($bot->sendnick == $this->creator || $force){
		$vote_total = count($this->votes);
		$op_num = $this->options;
		foreach($op_num as $op => $crap){
			$op_num[$op] = 0;
			foreach($this->votes as $key => $value){
				if($value == $op){
					$op_num[$op]++;
					unset($this->votes[$key]);
				}
			}
		}
		$m = array();
		$m[] = "Vote in $channel finished!";
		$m[] = "Results:";
		$m[] = $this->question;
		$max = array_keys($op_num, max($op_num));
		if(count($max) == 1){$max = $max['0'];}
		foreach($op_num as $key => $value){
			if(is_array($max)){
				if(array_search($key, $max) !== FALSE && $value != 0){
					$m[] = "{$this->options[$key]}: $value votes.";
				} else {
					$m[] = "{$this->options[$key]}: $value votes.";
				}
			} else {
				if($max == $key && $value != 0){
					$m[] = "{$this->options[$key]}: $value votes.";
				} else {
					$m[] = "{$this->options[$key]}: $value votes.";
                                }
			}
		}
		foreach($m as $value){
			$bot->privmsg($this->creator, $value);
			$bot->privmsg($channel, $value);
		}
		$this->init();
	}
}

function vote($channel, $vote){
	global $bot;
	$voter = $bot->sendprefix;
	if(empty($vote) || !array_key_exists($vote, $this->options)){
		$bot->privmsg($channel, "$bot->sendnick: That option is invalid.");
		return;
	}
	if(array_search($voter, $this->voters) !== FALSE){
		$bot->privmsg($channel, "$bot->sendnick: You have already voted.");
		return;
	}
	$this->voters[] = $voter;
	$this->votes[] = $vote;
	$bot->privmsg($channel, "$bot->sendnick: Your vote has been counted, thanks!");
}

}
?>
