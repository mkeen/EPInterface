<?php
class EPInterface {
	
	public $epmd_connection;
	
	function __construct($remote_addr, $node_name, $port = 4369) {
		$this->epmd_connection = new EPMDConnection($remote_addr, $node_name, $this, $port);
	}
	
	function epmd_connection_active() {
		return $this->epmd_connection->socket;
	}
	
}

class EPMDConnection {
	
	public $port = NULL;
	public $ip = NULL;
	public $domain = NULL;
	public $node_name = NULL;
	public $hidden_node_name = "php";
	public $socket = NULL;
	
	private $node_port = NULL;
	private $epinterface = NULL;
	
	function __construct($domain, $node_name, $interface, $port) {
		$this->port = $port;
		$this->ip = gethostbyname($this->domain = $domain);
		$this->epinterface = $interface;
		$this->node_name = $node_name;
		$this->socket = socket_create(AF_INET, SOCK_STREAM, getprotobyname('tcp'));
		$this->connect();
	}
	
	function connect() {
		socket_connect($this->socket, $this->ip, $this->port);
	}
	
	function disconnect() {
		socket_close($this->socket);
	}
	
	function request($req_type_id, $request_parts) {
		foreach($request_parts as $part) {
			switch($part[0]) {
				case "C":
					$byte_count_basis['C'] += strlen($part[1]);
				break;
				case "n":
					$byte_count_basis['n'] += 2;
				break;
			}
			
			$request_buffer[] = $part;
		}
		
		foreach($byte_count_basis as $op) {
			$byte_count += $op;
		}
		
		socket_write($this->socket, pack('n', $byte_count + 1));
		socket_write($this->socket, pack('C', $req_type_id));
		foreach($request_buffer as $request) {
			if($request[0] != "C") {
				socket_write($this->socket, pack($request[0], $request[1]));
			} else {
				socket_write($this->socket, $request[1]);
			}
			
		}
		
		return $req_type_id;
	}
	
	function response($req_type_id) {
		return $this->frame_response($req_type_id);
	}
	
	function frame_response($req_type_id) {
		switch($req_type_id) {
			case 120:
				$named_map = array('Code', 'Result', 'Creation');
				$byte_sequence = array(array("C", 1), array("C", 1), array('n', 2));
			break;

			case 122:
				$named_map = array('Code', 'Result', 'PortNo', 'NodeType', 'Protocol', 'HighestVersion', 'LowestVersion', 'Nlen', 'NodeName');
				$byte_sequence = array(array("C", 1), array("C", 1), array("n", 2), array("C", 1), array("C", 1), array("n", 2), array("n", 2), array("n", 2), array("C*", strlen($this->node_name) + 1));
			break;
		}
		
		return $this->map_names_to_response($this->read_response_buffer($byte_sequence), $named_map);
	}
	
	function read_response_buffer($byte_sequence) {
		foreach($byte_sequence as $bytes) {
			if($bytes[0] == "C*") {
				foreach(unpack($bytes[0], socket_read($this->socket, $bytes[1])) as $ord) {
					$double_buffer[] = chr($ord);
				}
				
				$buffer[] = implode($double_buffer);
			} else {
				$buffer[] = implode(unpack($bytes[0], socket_read($this->socket, $bytes[1])));
			}
			
		}
		
		return $buffer;
	}
	
	function map_names_to_response($buffer, $names) {
		for($i = 0; $i < sizeof($buffer); $i++) {
			$mapped[$names[$i]] = $buffer[$i];
		}
		
		return $mapped;		
	}
	
	function alive2_req() {
		$this->request(120, array(array('C', 8574), array('n', 72), array('n', 0), array('C', 5), array('C', 5), array('n', strlen($this->hidden_node_name)), array('C', $this->hidden_node_name)));
		return $this->response(120);
	}
	
	function port_please2_req() {
		$this->request(122, array(array('C', $this->node_name)));
		return $this->response(122);
	}
	
}