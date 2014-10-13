<?php

use HonkerBot\HonkerBot;

class CommandsTest extends PHPUnit_Framework_TestCase {

	function test_pong(){
		$bot = new HonkerBot;

		$code = "a67s67d89f09";
		$response = $bot->pong($code);
		$expected = "PONG :{$code}\n";

		$this->assertEquals($expected, $response);
	}

	function test_user(){
		$bot = new HonkerBot;

		$nick = "HonkerBot";
		$response = $bot->user($nick);
		$expected = "USER {$nick} 0 * :{$nick}\n";

		$this->assertEquals($expected, $response);
	}

	function test_nick(){
		$bot = new HonkerBot;

		$nick = "HonkerBot";
		$response = $bot->nick($nick);
		$expected = "NICK {$nick}\n";

		$this->assertEquals($expected, $response);
	}

	function test_pass(){
		$bot = new HonkerBot;

		$pass = "a67s67d89f09";
		$response = $bot->pass($pass);
		$expected = "PASS {$pass}\n";

		$this->assertEquals($expected, $response);
	}

	function test_join(){
		$bot = new HonkerBot;

		$chan = "#HonkerBot";
		$response = $bot->join($chan);
		$expected = "JOIN {$chan}\n";

		$this->assertEquals($expected, $response);
	}

	function test_notice(){
		$bot = new HonkerBot;

		$user = "HonkerBot";
		$msg  = "Test Message";
		$response = $bot->notice($user, $msg);
		$expected = "NOTICE {$user} :{$msg}\n";

		$this->assertEquals($expected, $response);
	}

	function test_msg(){
		$bot = new HonkerBot;

		$user = "HonkerBot";
		$msg  = "Test Message";
		$response = $bot->msg($user, $msg);
		$expected = "PRIVMSG {$user} :{$msg}\n";

		$this->assertEquals($expected, $response);
	}

	function test_me(){
		$bot = new HonkerBot;

		$user = "HonkerBot";
		$msg  = "Test Message";
		$response = $bot->me($user, $msg);
		$expected = "PRIVMSG {$user} :\x01ACTION {$msg}\x01\n";

		$this->assertEquals($expected, $response);
	}

}
