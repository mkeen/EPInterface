<?php
require_once "EPMDConnection.php";
require_once "ErlangNodeConnection.php";

class EPInterface {
	
	public $epmd_connection;
	public $erlang_node_connection;
	
	private $alive2_res;
	private $portplease2_res;
	
	function __construct($remote_addr, $node_name, $port = 4369) {
		$this->epmd_connection = new EPMDConnection($remote_addr, $port, $node_name);
	}
	
}