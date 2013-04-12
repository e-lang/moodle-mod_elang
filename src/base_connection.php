<?php
try {
	$server = $CFG->dbhost;
	$user = $CFG->dbuser;
	$pwd = $CFG->dbpass;
	$database = $CFG->dbname;

	$bdd = new PDO('mysql:host='.$server.';dbname='.$database,
			$user,
			$pwd,
			array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8' )
	);
	}
catch (Exception $e) { die('Erreur : ' . $e->getMessage()); }	
