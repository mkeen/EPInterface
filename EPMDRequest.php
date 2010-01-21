<?php
class EPMDRequest {
	
	function PortPlease2_Req($node_name) {
		return EPMDRequest::marshall(122, $node_name);
	}
	
	function Alive2_Req($portno, $nodetype, $protocol, $lowestversion, $highestversion, $nodename, $extra = NULL) {
		return EPMDRequest::marshall(120, array((int) $portno,
									(string) $nodetype,
									(string) $protocol,
									(int) $lowestversion,
									(int) $highestversion,
									(int) mb_strlen(pack("n", $nodename)),
									(string) $nodename,
									(!is_null($extra)) ? (string) mb_strlen(pack("n", $extra)) : NULL));
	}
	
	function marshall($req_code, $request) {
		// Just to keep marshall requests clean, allow a regular variable to be
		// passed in instead of an array. This will stuff $request into an array
		// if it isn't one.
		$request = (!is_array($request)) ? array($request) : $request;
		
		// The request code should always be added to the byte array first. It
		// should be encoded as a signed string.
		$marshalled = array(pack("C", $req_code));
		
		// Start tallying the byte size of the request
		$binlen = mb_strlen($marshalled[0]);
		
		// Depending on the characteristics of the request chunks found in the
		// $request array, apply the correct encoding and append it to the
		// byte array.
		foreach($request as $request_item) {
			if(is_string($request_item)) {
				array_push($marshalled, $request_item);
			} elseif(is_int($request_item)) {
				array_push($marshalled, pack("n", $request_item));
			} elseif(is_null($request_item)) {
				array_push($marshalled, pack("n", 0));
			}
			
			// Continue the byte size tally of the request
			$binlen = $binlen + mb_strlen($marshalled[count($marshalled) - 1]);
		}
		
		// Prepend the binary length of the request as an unsigned 16-bit integer
		// and return it.
		array_unshift($marshalled, pack("n", $binlen));
		return $marshalled;
	}
	
}