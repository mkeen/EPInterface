<?php
require_once('../EPInterface.php');
require_once 'PHPUnit/Framework.php';

define(RABBITMQ_SERVER_HOSTNAME, 'dev.rabbitmq.com');
define(RABBITMQ_ERLANG_NODENAME, 'rabbit');
define(RABBITMQ_ERLANG_PORTNUMB, '5672');

class EPInterfaceTest extends PHPUnit_Framework_TestCase {
	public function basic_tcp_connection() {
		$epi = new EPInterface(RABBITMQ_SERVER_HOSTNAME, RABBITMQ_ERLANG_NODENAME, RABBITMQ_ERLANG_PORTNUMB);
		$this->assertNotNull($epi->epmd_connection_active());
    }

}
?>