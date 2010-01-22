<?php
class EPMDResponse {
	
	/**
	 * Erlang Distribution Protocol: PORTPLEASE2_RESP
	 * @param socket_resource &$socket Reference to the active socket with a buffer ready to be read.
	 * @return array|string Framed response array from EPMD on success, or an error string if something goes wrong.
	 */
	function PortPlease2_Resp(&$socket) {
		return EPMDResponse::read_socket($socket, 122);
	}
	
	/**
	 * Erlang Distribution Protocol: ALIVE2_RESP
	 * @param socket_resource &$socket Reference to the active socket with a buffer ready to be read.
	 * @return array|string Framed response array from EPMD on success, or an error string if something goes wrong.
	 */
	function Alive2_Resp(&$socket) {
		return EPMDResponse::read_socket($socket, 120);
	}
	
	/**
	 * Frames all responses from the EPMD daemon. The code isn't very aesthetically pleasing, but it's easy to figure out what's going on.
	 * @param socket_resource &$socket Reference to the active socket with a buffer ready to be read.
	 * @param int $resp_code The request code for the corresponding response code. 120 = ALIVE2_REQ, 122 = PORT_PLEASE2_REQ, 110 = NAMES_REQ, 100 = DUMP_REQ, 107 = KILL_REQ
	 * @return array|string Framed response array from EPMD on success, or an error string if something goes wrong.
	 */
	function read_socket(&$socket, $resp_code) {
		$error = NULL;
		$response = NULL;
		switch($resp_code) {
			case 120:
				$response['response_code'] = implode(unpack("C", socket_read($socket, 1)));
				if($response['response_code'] == 121) {
					$response['result'] = implode(unpack("C", socket_read($socket, 1)));
					if($response['result'] < 1) {
						$response['creation'] = implode(unpack("n", socket_read($socket, 2)));
					} else {
						$error = "<strong>EPMD Error:</strong> Request 120 (Alive2) responded with an unspecified error. Good luck.";
					}
					
				} else {
					$error = "<strong>EPMD Error:</strong> Request 120 (Alive2) expected a response code of 121. Got " . $response['response_code'];
				}
				
			break;
			
			case 122:
				$response['response_code'] = implode(unpack("C", socket_read($socket, 1)));
				if($response['response_code'] == 119) {
					$response['result'] = implode(unpack("C", socket_read($socket, 1)));
					if($response['result'] < 1) {
						$response['port'] = implode(unpack("n", socket_read($socket, 2)));
						$response['nodetype'] = implode(unpack("C", socket_read($socket, 1)));
						$response['protocol'] = implode(unpack("C", socket_read($socket, 1)));
						$response['highestversion'] = implode(unpack("n", socket_read($socket, 2)));
						$response['lowestversion'] = implode(unpack("n", socket_read($socket, 2)));
						$response['nlen'] = implode(unpack("n", socket_read($socket, 2)));
						$response['nodename'] = EPMDResponse::decode_string(unpack("C*", socket_read($socket, $response['nlen'])));
						$response['elen'] = implode(unpack("C", socket_read($socket, 1)));
						$response['extra'] = ($response['elen'] > 0) ? EPMDResponse::decode_string(unpack("C*", socket_read($socket, $response['elen']))) : NULL;
					} else {
						$error = "<strong>EPMD Error:</strong> Request 112 (PortPlease2) responded with an unspecified error. This usually happens if you have requested information about a nonexistent or down node.";
					}
					
				} else {
					$error = "<strong>EPMD Error:</strong> Request 122 (PortPlease2) expected a response code of 119. Got " . $response['response_code'];
				}
				
			break;
		}
		
		return (!is_null($error)) ? $error : $response;
		
	}
	
	/**
	 * Given an array of ASCII character codes, returns the string it represents
	 * @param array $byte_array Array of ASCII character codes
	 * @return string The characters, nicely crashed n' smashed for your reading pleasure.
	 */
	function decode_string($byte_array) {
		$decoded = "";
		foreach($byte_array as $char) {
			$decoded .= chr($char);
		}
		
		return $decoded;		
	}
	
}