<?php

use HonkerBot\HonkerBot;

class HonkerBotTest extends PHPUnit_Framework_TestCase{

	function getInst($handle){
		$bot = new HonkerBot;
		$reflection = new ReflectionClass($bot);
		$botHandle = $reflection->getProperty("handle");
		$botHandle->setAccessible(true);
		$botHandle->setValue($bot, $handle);
		return $bot;
	}

	function test_construct(){

		$handle = fopen("php://memory", "rw+");
		$bot = $this->getInst($handle);

		$this->assertEquals(1, count($bot));

	}

	function test_write(){
		$bot = new HonkerBot;
		$handle = fopen("php://memory", "rw+");
		$text = "This is some sample text.";
		$bot->write($handle, $text, true);

		rewind($handle);
		$result = fread($handle, strlen($text));
		$this->assertEquals($text, $result);

	}

	function test_hook(){
		$handle = fopen("php://memory", "rw+");
		$bot = $this->getInst($handle);

		$toParse = "PING :irc.honkerbot.com";

		$bot->hook($toParse);

		rewind($handle);
		$result = fread($handle, strlen($toParse));
		$expected = "PONG :irc.honkerbot.com";
		$this->assertEquals($expected, $result);

	}

	function test_add_event(){
		$handle = fopen("php://memory", "rw+");
		$bot = $this->getInst($handle);

		$pattern = "|^PING :(?P<code>.*)$|i";

		$bot->addEvent($pattern, function($matches){
			return "a string";
		});

		$this->assertEquals(2, count($bot));

	}

	function test_hook_null(){
		$handle = fopen("php://memory", "rw+");
		$bot = $this->getInst($handle);

		$pattern = "|^PING :(?P<code>.*)$|i";

		$bot->addEvent($pattern, function($matches){
			return null;
		});

		$this->assertEquals(2, count($bot));

		$toParse = "PING :irc.honkerbot.com";

		$bot->hook($toParse);

		$this->assertEquals(1, count($bot));

	}
}

