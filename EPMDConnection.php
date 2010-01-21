<?php
require_once "EPMDRequest.php";
require_once "EPMDResponse.php";

class EPMDConnection {
	
	private $node_name;
	private $socket;
	private $ip;
	private $port;
	public $secret_port;
	public $php_port;
	
	function __construct($host, $port, $node_name) {
		$this->node_name = $node_name;
		$this->php_port = ErlangNodeConnection::choose_port();
		$this->port = $port;
		$this->ip = (!filter_var($host, FILTER_VALIDATE_IP)) ? gethostbyname($host) : $host;
	}
	
	function connect() {
		$this->socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));
		socket_connect($this->socket, $this->ip, $this->port);
	}
	
	function PortPlease2() {
		$this->connect();
		socket_write($this->socket, implode(EPMDRequest::PortPlease2_Req($this->node_name)));
		$result = EPMDResponse::PortPlease2_Resp($this->socket);
		socket_close($this->socket);
		$this->secret_port = $result["port"];
		return $result;
	}
	
	function Alive2() {
		$this->connect();
		socket_write($this->socket, implode(EPMDRequest::Alive2_Req($this->php_port, 72, 0, "", "", "php_" . rand(1, 9999999))));		
		$result = EPMDResponse::Alive2_Resp($this->socket);
		socket_close($this->socket);
		return $result;
	}
	
}