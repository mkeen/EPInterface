<?php
class EPMDRequest {
	
	/**
	 * Erlang Distribution Protocol: PORTPLEASE2_REQ
	 * @param string $node_name The name of the node that you want to know the port for
	 * @return array 
	 */
	function PortPlease2_Req($node_name) {
		return EPMDRequest::marshall(122, $node_name);
	}
	
	/**
	 * Erlang Distribution Protocol: ALIVE2_REQ
	 * @param integer $portno The port number that the PHP node is listening for incoming requests on. Right now, this doesn't do anything on the PHP side. It's just for Erlang's sake.
	 * @param integer $nodetype 77 = Normal Erlang Node, 72 = Hidden Node (C Node)
	 * @param string $protocol 0 = tcp/ip-v4. Don't use anything else.
	 * @param integer $lowestversion The lowest distribution version that this node can handle. See the next field for possible values.
	 * @param integer $highestversion The highest distribution version that this node can handle. The value in R6B and later is 5.
	 * @param integer $nodename What you want the name of the PHP node to be.
	 * @param integer $extra Not used yet.
	 * @return array 
	 */
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
	
	/**
	 * Frames all outgoing requests to the EPMD daemon. All requests are preceeded by a 2 byte length field, and a request code.
	 * @param integer $req_code 120 = ALIVE2_REQ, 122 = PORT_PLEASE2_REQ, 110 = NAMES_REQ, 100 = DUMP_REQ, 107 = KILL_REQ
	 * @param mixed $request Generally, you should pass an array of typed entities. This function encodes each entity in a different way depending on its data type. Strings aren't specially encoded, but integers are encoded to an unsigned short 16-bit int, and NULL values are encoded as unsigned short 16-bit ints as 0.
	 * @return array 
	 */
	function marshall($req_code, $request) {
		/**
		 * Just to keep marshall requests clean, allow a regular variable to be
		 * passed in instead of an array. This will stuff {@link $request} into an array
		 * if it isn't one.
		 */
		$request = (!is_array($request)) ? array($request) : $request;
		
		/**
		 * The {@link $req_code} should always be added to the byte array first. It
		 * should be encoded as a signed string.
		 */
		$marshalled = array(pack("C", $req_code));
		
		/**
		 * Start tallying the byte size of the request
		 */
		$binlen = mb_strlen($marshalled[0]);
		
		/**
		 * Depending on the characteristics of the chunks found in the
		 * {@link $request} array, apply the correct encoding and append it to the
		 * byte array to be returned.
		 */
		foreach($request as $request_item) {
			if(is_string($request_item)) {
				array_push($marshalled, $request_item);
			} elseif(is_int($request_item)) {
				array_push($marshalled, pack("n", $request_item));
			} elseif(is_null($request_item)) {
				array_push($marshalled, pack("n", 0));
			}
			
			/**
			 * Continue the byte size tally of the request
			 */
			$binlen = $binlen + mb_strlen($marshalled[count($marshalled) - 1]);
		}
		
		/**
		 * Prepend the binary length of the framed request as an unsigned 16-bit integer
		 */
		array_unshift($marshalled, pack("n", $binlen));
		return $marshalled;
	}
	
}