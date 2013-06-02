<?php

$task = isset($_REQUEST['task']) ? $_REQUEST['task'] : '';

$sequence1 = array('id'=>'1', 'titre'=>"Titre sequence 1 4343", 'debut'=>'1', 'fin'=>'3');
$text1A = array('type'=>'text','content'=>'I thought I would save time by purchasing my airline ticket online and');
$input1A =  array('type'=>'input','id'=>'1');
$answer1A = array('content'=>'checking in','id'=>'1');
$text1B = array('type'=>'text','content'=>'at the airport with my');
$input1B =  array('type'=>'input','id'=>'2');
$answer1B = array('content'=>'e-ticket','id'=>'1');
$text1C = array('type'=>'text','content'=>'. I');
$input1C =  array('type'=>'input','id'=>'3');
$answer1C = array('content'=>'went onto','id'=>'3');
$text1D = array('type'=>'text','content'=>'the McQ Air website and selected my flights.');
$text1 = array('seq_id'=>'1','content'=> array($text1A,$input1A,$text1B,$input1B,$text1C,$input1C,$text1D));

$answers_seq1 = array($answer1A,$answer1B,$answer1C);


$sequence2 = array('id'=>'2', 'titre'=>"Titre sequence 2",'debut'=>'4', 'fin'=>'6');
$text2A = array('type'=>'text','content'=>'The');
$input2A =  array('type'=>'input','id'=>'1');
$answer2A =  array('content'=>'screen','id'=>'1');
$text2B = array('type'=>'text','content'=>'then');
$input2B =  array('type'=>'input','id'=>'2');
$answer2B =  array('content'=>'prompted me','id'=>'1');
$text2C = array('type'=>'text','content'=>'to pay with a credit card.');
$text2 = array('seq_id'=>'2','content'=> array($text2A,$input2A,$text2B,$input2B,$text2C));

$answers_seq2 = array($answer2A,$answer2B);

$sequence3 = array('id'=>'3', 'titre'=>"Titre sequence 3",'debut'=>'7', 'fin'=>'9');
$text3A = array('type'=>'text','content'=>' After I typed in my payment information, I got a ');
$input3A =  array('type'=>'input','id'=>'1');
$answer3A =  array('content'=>'confirmation receipt','id'=>'1');
$text3B = array('type'=>'text','content'=>' with my ');
$input3B =  array('type'=>'input','id'=>'2');
$answer3B =  array('content'=>'ticket number','id'=>'1');
$text3C = array('type'=>'text','content'=>'.');
$text3 = array('seq_id'=>'3','content'=> array($text3A,$input3A,$text3B,$input3B,$text3C));

$answers_seq3 = array($answer3A,$answer3B);


$list_answers = array($answers_seq1,$answers_seq2,$answers_seq3);
		
switch ($task)
{
	// case 'video':
		// header('Cache-Control: no-cache, must-revalidate');
		// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		// header('Content-type: application/json');
		
		// $videos = array('video' => 'url');
		// $filtered_array = array_filter($videos);
		
		// if (!empty($filtered_array))
		// {
			// echo json_encode(array('status'=>'200', 'content' => $videos));
		// }		
		// else
		// {
			// echo json_encode(array('status'=>'403', 'content' => array()));
		// }
		// include 'parseWebVTT.php';
		// echo json_encode(array('status'=>'403', 'content' => array()));
		// break;

	case 'data' :
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		
		$title = 'Mon titre';
		$description='Ma description';
		$sequences = array($sequence1,$sequence2,$sequence3);
		$inputs = array($text1,$text2,$text3);
		echo json_encode(array(
			'title'=>$title,
			'description'=>$description,
			'sequences'=>$sequences,
			'inputs'=>$inputs,
			'sources' => array(
				array('url' => 'arduino.ogv', 'type' => 'video/ogg')
			),
			'poster' => 'icon.png',
			'track' => 'arduino-en.vtt',
			'language' => 'en-UK'
		));
		break;
		
	case 'check' :
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		
		$answer = isset($_REQUEST['answer']) ? $_REQUEST['answer'] : '';
		$seq_id = isset($_REQUEST['seq_id']) ? $_REQUEST['seq_id'] : '';
		$input_id = isset($_REQUEST['input_id']) ? $_REQUEST['input_id'] : '';
		
				
	
		
		$rep = $list_answers[$seq_id][$input_id]['content'];
		if($rep==$answer)
		{
			echo json_encode(array('check'=>'true'));

		}
		else
		{
			echo json_encode(array('check'=>'false'));
	
		}
		break;
		
	case 'help' : 
	
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');
		
		$seq_id = isset($_REQUEST['seq_id']) ? $_REQUEST['seq_id'] : '';
		$input_id = isset($_REQUEST['input_id']) ? $_REQUEST['input_id'] : '';
		
			
		$rep = $list_answers[$seq_id][$input_id]['content'];
		
		echo json_encode(array('help'=>$rep));
		break;

	default:
		echo file_get_contents(__DIR__ . '/debug.html');
}
