<?php

/**
 * from commandline: `php example/HonkerBot.php`
 *
 * this could easily be made to take CLI args vs a config array
 *
 */

require "vendor/autoload.php";

require __DIR__ . "/ExampleLogger.php";

$config = array(
	"password" => "",
	"username" => "",
	"nickname" => "",
	"hostname" => "",
	"hostport" => "",
);

//overwrite our examples
if(file_exists("conf/config.ini")){
	$config = parse_ini_file("conf/config.ini");
}

$honker = new HonkerBot\HonkerBot(new ExampleLogger);

//send our creds after the first response from the IRC then remove this event
$honker->addEvent("|.*|i", function($matches)use($honker, $config){
	static $joined;
	if(!$joined){
		$joined = true;
		$str  = $honker->pass($config["password"]);
		$str .= $honker->user($config["username"]);
		$str .= $honker->nick($config["nickname"]);
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
$honker->connect($config["hostname"], $config["hostport"]);

//loop
$honker->listen();