<?php
class ErlangNodeConnection {
	
	private $port;
	
	function __construct($port, $nodename) {
		$m = 0;
	}
	
	function choose_port() {
		return rand(30000, 70000);
	}
	
}