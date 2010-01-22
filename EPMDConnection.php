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
	
	/**
	 * Sets a random port to use as the accept port for this node (which doesn't actually work) and resolves the host
	 * @param string $host This can be an IP address or a hostname
	 * @param int $port The port that EPMD is listening on
	 * @param string $nodename The name of the Erlang node you want to interact with
	 */
	function __construct($host, $port, $node_name) {
		$this->node_name = $node_name;
		$this->php_port = ErlangNodeConnection::choose_port();
		$this->port = $port;
		$this->ip = (!filter_var($host, FILTER_VALIDATE_IP)) ? gethostbyname($host) : $host;
	}
	
	/**
	 * Builds socket and opens it
	 */
	function connect() {
		$this->socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname("tcp"));
		socket_connect($this->socket, $this->ip, $this->port);
	}
	
	/**
	 * Builds a PORTPLEASE2_REQ, writes it, saves the secret port, and returns a nice PORTPLEASE2_RESP or an error.
	 * @return array|string Either a framed PORTPLEASE2_RESP or a string containing error details
	 */
	function PortPlease2() {
		$this->connect();
		socket_write($this->socket, implode(EPMDRequest::PortPlease2_Req($this->node_name)));
		$result = EPMDResponse::PortPlease2_Resp($this->socket);
		socket_close($this->socket);
		$this->secret_port = $result["port"];
		return $result;
	}
	
	/**
	 * Builds a ALIVE2_REQ, writes it, and returns a nice ALIVE2_RESP or an error.
	 * @return array|string Either a framed ALIVE2_RESP or a string containing error details
	 */
	function Alive2() {
		$this->connect();
		socket_write($this->socket, implode(EPMDRequest::Alive2_Req($this->php_port, 72, 0, "", "", "php_" . rand(1, 9999999))));		
		$result = EPMDResponse::Alive2_Resp($this->socket);
		socket_close($this->socket);
		return $result;
	}
	
}