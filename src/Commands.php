<?php

namespace HonkerBot;

class Commands {

	/**
	 * properly format a PONG response
	 * @param string $code The response code
	 * @return string
	 */
	function pong($code){ return "PONG :{$code}\n"; }

	/**
	 * properly format a USER response
	 * @param string $nick The nick to use
	 * @return string
	 */
	function user($nick){ return "USER {$nick} 0 * :{$nick}\n"; }

	/**
	 * properly format a NICK response
	 * @param string $nick The nick to use
	 * @return string
	 */
	function nick($nick){ return "NICK {$nick}\n"; }

	/**
	 * properly format a PASS response
	 * @param string $pass The password to use
	 * @return string
	 */
	function pass($pass){ return "PASS {$pass}\n"; }

	/**
	 * properly format a JOIN response
	 * @param string $chan The channel to join
	 * @return string
	 */
	function join($chan){ return "JOIN {$chan}\n"; }

	/**
	 * properly format a notice response
	 * @param string $user The recipient of the message
	 * @param string $msg The message
	 * @return string
	 */
	function notice($user, $msg){ return "NOTICE {$user} :{$msg}\n"; }

	/**
	 * properly format a PRIVMSG response
	 * @param string $user The recipient of the message
	 * @param string $msg The message
	 * @return string
	 */
	function msg($user, $msg){ return "PRIVMSG {$user} :{$msg}\n"; }

	/**
	 * properly format a ME response
	 * @param string $user The recipient of the message (usually a channel)
	 * @param string $msg The message
	 * @return string
	 */
	function me($user, $msg){ return "PRIVMSG {$user} :\x01ACTION {$msg}\x01\n"; }

	// function query($user, $msg){ return "QUERY {$user} {$msg}\n"; }

}