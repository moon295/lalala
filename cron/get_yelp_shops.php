<?php
/**
 * YELP API からショップデータを取得しDBに登録
 */
require_once dirname(dirname(__FILE__)) . '/app/Lpo_Controller.php';
Lpo_Controller::main_CLI('Lpo_Controller', 'batch_getyelpshops');
?>
