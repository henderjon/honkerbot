<?php

use HonkerBot\HonkerBot;

FUnit::test("HonkerBot::construct()", function(){

	$bot = new HonkerBot;
	$bot->suppress = true;
	$reflection = new ReflectionClass($bot);
	$events = $reflection->getProperty("events");
	$events->setAccessible(true);
	$events = $events->getValue($bot);

	FUnit::equal(1, count($events));

});

FUnit::test("HonkerBot::write()", function(){
	$bot = new HonkerBot;
	$bot->suppress = true;
	$handle = fopen("php://memory", "rw+");
	$text = "This is some sample text.";
	$bot->write($handle, $text, true);

	rewind($handle);
	$result = fread($handle, strlen($text));
	FUnit::equal($text, $result);

});

FUnit::test("HonkerBot::hook() write string", function(){
	$bot = new HonkerBot;
	$bot->suppress = true;
	$reflection = new ReflectionClass($bot);
	$handle = fopen("php://memory", "rw+");
	$botHandle = $reflection->getProperty("handle");
	$botHandle->setAccessible(true);
	$botHandle->setValue($bot, $handle);

	$toParse = "PING :irc.honkerbot.com";

	$bot->hook($toParse);

	rewind($handle);
	$result = fread($handle, strlen($toParse));
	$expected = "PONG :irc.honkerbot.com";
	FUnit::equal($expected, $result);

});

FUnit::test("HonkerBot::add_event()", function(){

	$bot = new HonkerBot;
	$bot->suppress = true;
	$reflection = new ReflectionClass($bot);
	$events = $reflection->getProperty("events");
	$events->setAccessible(true);

	$pattern = "|^PING :(?P<code>.*)$|i";

	$bot->addEvent($pattern, function($matches){
		return "a string";
	});

	$events = $events->getValue($bot);

	FUnit::equal(2, count($events[$pattern]));

});

FUnit::test("HonkerBot::hook() null after dispatch", function(){
	$bot = new HonkerBot;
	$bot->suppress = true;
	$reflection = new ReflectionClass($bot);
	$events = $reflection->getProperty("events");
	$events->setAccessible(true);
	$handle = fopen("php://memory", "rw+");
	$botHandle = $reflection->getProperty("handle");
	$botHandle->setAccessible(true);
	$botHandle->setValue($bot, $handle);

	$pattern = "|^PING :(?P<code>.*)$|i";

	$bot->addEvent($pattern, function($matches){
		return null;
	});

	$toParse = "PING :irc.honkerbot.com";

	$bot->hook($toParse);

	$events = $events->getValue($bot);

	FUnit::equal(1, count($events[$pattern]));

});

