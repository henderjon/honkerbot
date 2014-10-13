<?php

use HonkerBot\HonkerBot;

class HonkerBotTest extends PHPUnit_Framework_TestCase{

	function test_construct(){

		$bot = new HonkerBot;
		$bot->suppress = true;
		$reflection = new ReflectionClass($bot);
		$events = $reflection->getProperty("events");
		$events->setAccessible(true);
		$events = $events->getValue($bot);

		$this->assertEquals(1, count($events));

	}

	function test_write(){
		$bot = new HonkerBot;
		$bot->suppress = true;
		$handle = fopen("php://memory", "rw+");
		$text = "This is some sample text.";
		$bot->write($handle, $text, true);

		rewind($handle);
		$result = fread($handle, strlen($text));
		$this->assertEquals($text, $result);

	}

	function test_hook(){
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
		$this->assertEquals($expected, $result);

	}

	function test_add_event(){

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

		$this->assertEquals(2, count($events[$pattern]));

	}

	function test_hook_null(){
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

		$this->assertEquals(1, count($events[$pattern]));

	}
}

