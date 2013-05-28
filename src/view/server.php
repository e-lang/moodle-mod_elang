<?php


$task = isset($_GET['task']) ? $_GET['task'] : '';

switch ($task)
{
	case 'video':
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
//		echo json_encode(array('status'=>'200', 'content' => array('video' => 'url')));
		echo json_encode(array('status'=>'403', 'content' => array()));
		break;
	default:
		echo file_get_contents(__DIR__ . '/debug.html');
}
