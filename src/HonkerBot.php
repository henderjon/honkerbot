<?php

namespace HonkerBot;

use Psr\Log;
use WM;

class HonkerBot extends Commands implements \Countable {

	use Log\LoggerAwareTrait;

	/**
	 * our connection
	 */
	protected $handle;

	/**
	 * keep a stack of events
	 */
	protected $events = array();

	/**
	 * stream timeout
	 */
	protected $timeout = 3600;

	/**
	 * store the IP/Port for logging
	 */
	protected $sock;

	/**
	 * directional constants
	 */
	const INBOUND  = "inbound";

	/**
	 * directional constants
	 */
	const OUTBOUND = "outbound";

	/**
	 * add a default PING/PONG to our bot
	 * @return
	 */
	function __construct(Log\LoggerInterface $logger = null){
		$this->logger = $logger;
		$this->timeout = (WM\ONE_MINUTE * 6);

		$this->addEvent("|^PING :(?P<code>.*)$|i", function($matches){
			return sprintf("PONG :%s\n", $matches["code"]);
		});
	}

	/**
	 * connect to an IP:PORT
	 * @param string $ip The IP address
	 * @param string $port The PORT
	 * @return
	 */
	function connect( $ip, $port ){
		$errno = $errstr = "";
		$this->sock = $sock = "tcp://{$ip}:{$port}";
		$this->handle = stream_socket_client($sock, $errno, $errstr);

		if($errno){
			throw new HonkerBotException($errstr, $errno);
		}

		if( $this->handle === false){
			throw new HonkerBotException("stream_socket_client returned FALSE without an error.");
		}
	}
	/**
	 * write a string to the socket and to STDERR
	 * @param resource $handle The connection
	 * @param string $msg The string
	 * @return int
	 */
	function write( $handle, $msg ){
		$msg = rtrim( $msg, "\n" );
		$len = fwrite( $handle, "{$msg}\n" );
		$this->logIo($msg, static::OUTBOUND);
		return $len;
	}

	/**
	 * send a line through each of our events
	 * @param string $line The line to match
	 * @return
	 */
	function hook( $line ){
		if(!is_array($this->events)) return;

		$patterns = array_keys($this->events);

		foreach($patterns as $pattern){
			if(1 !== preg_match($pattern, $line, $matches)) continue;

			foreach($this->events[$pattern] as $k => $callback){
				$response = false;
				$response = call_user_func($callback, $matches);

				switch(true){
					case is_string($response) :
						$this->write($this->handle, $response);
						break;
					case is_null($response) :
						unset($this->events[$pattern][$k]);
						break;
				}
			}
		}
	}

	/**
	 * add an event. events are composed of a regex pattern to match and a
	 * callback. the callback is passed the array of matches from the regex.
	 * the callback should return the response string or NULL. if NULL the event
	 * will be removed from the events array. this is useful for events that
	 * ought to only fire once or after a certain number of calls.
	 *
	 * @param string $pattern The regex to use to match the lines coming through
	 * @param callable $callback The function to execute
	 * @return
	 */
	function addEvent( $pattern, callable $callback ){
		$this->events[$pattern][] = $callback;
	}

	/**
	 * infinite loop over each line that comes from the server. some servers
	 * have longer ping intervals, play with the server timeout as desired
	 * @return type
	 */
	function listen(){
		while( $line = trim( fgets( $this->handle ) ) ){
			stream_set_timeout( $this->handle, $this->timeout );
			$this->logIo($line, static::INBOUND);
			$this->hook($line);
		}
	}

	/**
	 * set the timeout of each iteration of the loop
	 * @param int $seconds The number of seconds
	 */
	function setTimeout($seconds){
		$this->timeout = (int)$seconds;
	}

	/**
	 * log IO using a PSR logger
	 */
	function logIo($message, $direction){
		if($this->logger instanceof Log\LoggerInterface){
			$this->logger->info($message, [
				"io.timestamp"    => date("c (e)"),
				"io.message"      => $message,
				"io.direction"    => $direction,
				"conn.socket"     => $this->sock,
				"conn.timeout"    => $this->timeout,
				"events.count"    => $this->count(),
				"events.patterns" => array_keys($this->events),
			]);
		}
	}

	/**
	 * implements \Countable
	 */
	function count(){
		$count = 0;
		$patterns = array_keys($this->events);
		foreach($patterns as $pattern){
			$count += count($this->events[$pattern]);
		}
		return $count;
	}

}