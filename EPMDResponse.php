<?php
class EPMDResponse {
	
	function PortPlease2_Resp(&$socket) {
		return EPMDResponse::read_socket($socket, 122);
	}
	
	function Alive2_Resp(&$socket) {
		return EPMDResponse::read_socket($socket, 120);
	}
	
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
	
	function decode_string($byte_array) {
		$decoded = "";
		foreach($byte_array as $char) {
			$decoded .= chr($char);
		}
		
		return $decoded;		
	}
	
}