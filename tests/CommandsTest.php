<?php

require_once("tests/bootstrap.php");

use HonkerBot\HonkerBot;

FUnit::test("HonkerBot::pong()", function(){
	$bot = new HonkerBot;

	$code = "a67s67d89f09";
	$response = $bot->pong($code);
	$expected = "PONG :{$code}\n";

	FUnit::equal($expected, $response);
});

FUnit::test("HonkerBot::user()", function(){
	$bot = new HonkerBot;

	$nick = "HonkerBot";
	$response = $bot->user($nick);
	$expected = "USER {$nick} 0 * :{$nick}\n";

	FUnit::equal($expected, $response);
});

FUnit::test("HonkerBot::nick()", function(){
	$bot = new HonkerBot;

	$nick = "HonkerBot";
	$response = $bot->nick($nick);
	$expected = "NICK {$nick}\n";

	FUnit::equal($expected, $response);
});

FUnit::test("HonkerBot::pass()", function(){
	$bot = new HonkerBot;

	$pass = "a67s67d89f09";
	$response = $bot->pass($pass);
	$expected = "PASS {$pass}\n";

	FUnit::equal($expected, $response);
});

FUnit::test("HonkerBot::join()", function(){
	$bot = new HonkerBot;

	$chan = "#HonkerBot";
	$response = $bot->join($chan);
	$expected = "JOIN {$chan}\n";

	FUnit::equal($expected, $response);
});

FUnit::test("HonkerBot::notice()", function(){
	$bot = new HonkerBot;

	$user = "HonkerBot";
	$msg  = "Test Message";
	$response = $bot->notice($user, $msg);
	$expected = "NOTICE {$user} :{$msg}\n";

	FUnit::equal($expected, $response);
});

FUnit::test("HonkerBot::msg()", function(){
	$bot = new HonkerBot;

	$user = "HonkerBot";
	$msg  = "Test Message";
	$response = $bot->msg($user, $msg);
	$expected = "PRIVMSG {$user} :{$msg}\n";

	FUnit::equal($expected, $response);
});

FUnit::test("HonkerBot::me()", function(){
	$bot = new HonkerBot;

	$user = "HonkerBot";
	$msg  = "Test Message";
	$response = $bot->me($user, $msg);
	$expected = "PRIVMSG {$user} :\x01ACTION {$msg}\x01\n";

	FUnit::equal($expected, $response);
});
