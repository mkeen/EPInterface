<?php
require_once '../EPInterface.php';
require_once 'PHPUnit/Framework.php';

define('RABBITMQ_SERVER_HOSTNAME', 'localhost');
define('RABBITMQ_ERLANG_NODENAME', 'test');
define('RABBITMQ_ERLANG_PORTNUMB', 4369);

class EPInterfaceTest extends PHPUnit_Framework_TestCase {	
	public function testTCPConnectionActive() {
		$epi = new EPInterface(RABBITMQ_SERVER_HOSTNAME, RABBITMQ_ERLANG_NODENAME, RABBITMQ_ERLANG_PORTNUMB);
		$this->assertNotNull($epi->epmd_connection_active());
		$epi->epmd_connection->disconnect();
    }

	public function testPortPlease2Req() {
		$epi = new EPInterface(RABBITMQ_SERVER_HOSTNAME, RABBITMQ_ERLANG_NODENAME, RABBITMQ_ERLANG_PORTNUMB);
		foreach($epi->epmd_connection->port_please2_req() as $response_frame) {
			$this->assertNotSame("", $response_frame);
		}
		
		$this->assertNotNull($epi->epmd_connection_active());
	}
	
	public function testAlive2Req() {
		$epi = new EPInterface(RABBITMQ_SERVER_HOSTNAME, RABBITMQ_ERLANG_NODENAME, RABBITMQ_ERLANG_PORTNUMB);
		foreach($epi->epmd_connection->alive2_req() as $response_frame) {
			$this->assertNotSame("", $reponse_frame);
		}
		
		$this->assertNotNull($epi->epmd_connection_active());
	}

}
?>