<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED);
require_once dirname(dirname(__FILE__)) . '/app/Lpo_Controller.php';
Lpo_Controller::main('Lpo_Controller', 'index');
?>
