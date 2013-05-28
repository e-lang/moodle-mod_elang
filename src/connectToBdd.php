<?php
try {
	$server = "localhost";
	$user = "icone08";
	$pwd = "icone08";
	$database = 'moodle';
	
	$bdd = new PDO('mysql:host='.$server.';dbname='.$database,
			$user,
			$pwd,
			array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' )
	);
}
catch (Exception $e) { die('Erreur : ' . $e->getMessage()); }
?>