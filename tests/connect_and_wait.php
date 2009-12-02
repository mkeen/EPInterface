<?php
require_once('unit_tester.php');
require_once('reporter.php');
require_once('../EPInterface.php');

define(RABBITMQ_SERVER_HOSTNAME, 'rabbit.beacon.io');
define(RABBITMQ_ERLANG_NODENAME, 'rabbit');

class EPInterfaceTests extends UnitTestCase {
	function __construct() {
		$this->UnitTestCase();
	}
	
	function BasicSocketConnect() {
		$this->assertNotNull($epi->epmd_connection_active());
	}
	
}
?>