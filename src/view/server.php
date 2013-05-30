<?php

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';


switch ($task)
{
	case 'video':
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		
		$videos = array('video' => 'url');
		$filtered_array = array_filter($videos);
		
		if (!empty($filtered_array))
		{
			echo json_encode(array('status'=>'200', 'content' => $videos));
		}		
		else
		{
			echo json_encode(array('status'=>'403', 'content' => array()));
		}
		include 'parseWebVTT.php';
		break;

	case 'data' :
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		
		$sequence1 = array('id'=>'1', 'titre'=>"Titre sequence 1", 'debut'=>'1', 'fin'=>'3');
		$text1A = array('type'=>'text','content'=>'I thought I would save time by purchasing my airline ticket online and');
		$input1A =  array('type'=>'input','content'=>'checking in','id'=>'1');
		$text1B = array('type'=>'text','content'=>'at the airport with my');
		$input1B =  array('type'=>'input','content'=>'e-ticket','id'=>'2');
		$text1C = array('type'=>'text','content'=>'. I');
		$input1C =  array('type'=>'input','content'=>'went onto','id'=>'3');
		$text1D = array('type'=>'text','content'=>'the McQ Air website and selected my flights.');
		$text1 = array('seq_id'=>'1','content'=> array($text1A,$input1A,$text1B,$input1B,$text1C,$input1C,$text1D));
		
		$sequence2 = array('id'=>'2', 'titre'=>"Titre sequence 2",'debut'=>'4', 'fin'=>'6');
		$text2A = array('type'=>'text','content'=>'The');
		$input2A =  array('type'=>'input','content'=>'screen','id'=>'1');
		$text2B = array('type'=>'text','content'=>'then');
		$input2B =  array('type'=>'input','content'=>'prompted me','id'=>'2');
		$text2C = array('type'=>'text','content'=>'to pay with a credit card.');
		$text2 = array('seq_id'=>'2','content'=> array($text2A,$input2A,$text2B,$input2B,$text2C));
		
		$sequence3 = array('id'=>'3', 'titre'=>"Titre sequence 3",'debut'=>'7', 'fin'=>'9');
		$text3A = array('type'=>'text','content'=>' After I typed in my payment information, I got a ');
		$input3A =  array('type'=>'input','content'=>'confirmation receipt','id'=>'1');
		$text3B = array('type'=>'text','content'=>' with my ');
		$input3B =  array('type'=>'input','content'=>'ticket number','id'=>'2');
		$text3C = array('type'=>'text','content'=>'.');
		$text3 = array('seq_id'=>'3','content'=> array($text3A,$input3A,$text3B,$input3B,$text3C));
		
		$title = 'Mon titre';
		$description='Ma description';
		$sequences = array($sequence1,$sequence2,$sequence3);
		$inputs = array($text1,$text2,$text3);
		echo json_encode(array('title'=>$title, 'description'=>$description, 'sequences'=>$sequences, 'inputs'=>$inputs));
		break;

	default:
		echo file_get_contents(__DIR__ . '/debug.html');
}
