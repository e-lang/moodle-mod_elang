<?php
try {
	$server = "localhost";
	$user = "root";
	$pwd = "";
	$database = 'moodle';

	$bdd = new PDO('mysql:host='.$server.';dbname='.$database,
			$user,
			$pwd,
			array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' )
	);
	}
catch (Exception $e) { die('Erreur : ' . $e->getMessage()); }	
?>