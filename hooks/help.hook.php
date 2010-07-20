<?php
$this->send("PRIVMSG $channel :$this->nick help:");
$this->send("PRIVMSG $channel :!help     Displays this help.");
$this->send("PRIVMSG $channel :!figlet   Run some text through figlet. Accepts -f for fonts.");
$this->send("PRIVMSG $channel :!cowsay   Run some text through cowsay. Accepts -f for cowfiles.");
$this->send("PRIVMSG $channel :!tweet    Twit a Tweet via @chatnets.");
$this->send("PRIVMSG $channel :!vote     Voting system. '!vote help' for more help.");
$this->send("PRIVMSG $channel :!ascii    Scroll some ascii. Accepts filename.");
$this->send("PRIVMSG $channel :!np       What's currently playing on #fm radio.");
?>
