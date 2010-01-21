<?php
require_once '../EPInterface.php';
require_once 'PHPUnit/Framework.php';

define('RABBITMQ_SERVER_HOSTNAME', 'localhost');
define('RABBITMQ_ERLANG_NODENAME', 'test');
define('RABBITMQ_ERLANG_PORTNUMB', 4369);

class EPInterfaceTest extends PHPUnit_Framework_TestCase {

	public function testAlive2() {
		$epi = new EPInterface(RABBITMQ_SERVER_HOSTNAME, RABBITMQ_ERLANG_NODENAME, RABBITMQ_ERLANG_PORTNUMB);
		
		echo "Sending ALIVE2_REQ...\n";
		$result = $epi->epmd_connection->Alive2();
		$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $result);
		
		echo "Making sure parsed response is reasonably well formed...\n";
		$this->assertArrayHasKey("response_code", $result);
		$this->assertArrayHasKey("result", $result);
		
		echo "Making sure parsed response values are consistent with success...\n";
		$this->assertEquals(121, $result['response_code']);
		$this->assertEquals(0, $result['result']);
		$this->assertArrayHasKey("creation", $result);
		$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_NUMERIC, $result['creation']);
	}

	public function testPortPlease2() {
		$epi = new EPInterface(RABBITMQ_SERVER_HOSTNAME, RABBITMQ_ERLANG_NODENAME, RABBITMQ_ERLANG_PORTNUMB);
		
		echo "\nSending PORT_PLEASE2_REQ...\n";
		$result = $epi->epmd_connection->PortPlease2();
		$this->assertType(PHPUnit_Framework_Constraint_IsType::TYPE_ARRAY, $result);
		
		echo "Making sure parsed response is reasonably well formed...\n";
		$this->assertArrayHasKey("response_code", $result);
		$this->assertArrayHasKey("result", $result);
		
		echo "Making sure parsed response values are consistent with success...\n";
		$this->assertEquals(119, $result['response_code']);
		$this->assertEquals(0, $result['result']);
		$this->assertArrayHasKey("port", $result);
		$this->assertArrayHasKey("nodetype", $result);
		$this->assertArrayHasKey("protocol", $result);
		$this->assertArrayHasKey("highestversion", $result);
		$this->assertArrayHasKey("lowestversion", $result);
		$this->assertArrayHasKey("nlen", $result);
		$this->assertArrayHasKey("nodename", $result);
		$this->assertArrayHasKey("elen", $result);
		$this->assertArrayHasKey("extra", $result);
	}

}
?>