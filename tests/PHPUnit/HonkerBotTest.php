<?php

use HonkerBot\HonkerBot;

use Psr\Log;

class NoOutPutLogger extends Log\AbstractLogger {
	protected $context;
	function log($level, $message, array $context = []){
		$context["log.level"]   = $level;
		$context["log.message"] = $message;
		$this->context = $context;
	}
	function getContext(){ return $this->context; }
}

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
		$bot->write($handle, $text);

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

	function test_logIo(){
		$handle = fopen("php://memory", "rw+");
		$bot = $this->getInst($handle);

		$logger = new NoOutPutLogger;

		$bot->setLogger($logger);

		$text = "This is some sample text.";
		$bot->write($handle, $text, true);

		$expected["log.level"]       = "info";
		$expected["log.message"]     = "This is some sample text.";
		$expected["io.timestamp"]    =  date("c (e)");
		$expected["io.message"]      = "This is some sample text.";
		$expected["io.direction"]    = $bot::OUTBOUND;
		$expected["conn.socket"]     = null;
		$expected["conn.timeout"]    = $bot::TIMEOUT;
		$expected["events.count"]    = $bot->count();
		$expected["events.patterns"] = ["|^PING :(?P<code>.*)$|i"];

		$this->assertEquals($expected, $logger->getContext());

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

