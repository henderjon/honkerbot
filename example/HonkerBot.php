<?php

$honker = new HonkerBot\HonkerBot;

//send our creds after the first response from the IRC then remove this event
$honker->addEvent("|.*|i", function($matches)use($honker){
	static $joined;
	if(!$joined){
		$joined = true;
		$str  = $honker->pass("password");
		$str .= $honker->user("honkerbot");
		$str .= $honker->nick("honkerbot");
		return $str;
	}
	return null;
});

//after our first ping, join a channel then remove this event
$honker->addEvent("|^PING :(?P<code>.*)$|i", function($matches)use($honker){
	static $joined;
	if(!$joined){
		$joined = true;
		return $honker->join("#honkerbot");
	}
	return null;
});

//every other PING send a third person message
$honker->addEvent("|^PING :(?P<code>.*)$|i", function($matches)use($honker){
	static $pings;
	if( ($pings++ % 2) == 0 ){
		return $honker->me("#honkerbot", "is so tired ...");
	}
	return true;
});

//connect
$honker->connect("irc.ircserver.com", "6667");

//loop
$honker->listen();