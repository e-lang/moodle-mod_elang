<?php

/**
 * Server for ajax request of elang
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
require_once dirname(__FILE__) . '/lib.php';

$task = optional_param('task', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

// Detect if there is no course module id
if ($id == 0)
{
	header('HTTP/1.1 400 Bad Request');
	die;
}

// Get the course module, the elang instance and the context
$cm = get_coursemodule_from_id('elang', $id, 0, false);

// Detect if the course module exists
if (!$cm)
{
	header('HTTP/1.1 404 Not Found');
	die;
}

// Detect if the user is logged in
if (!isloggedin())
{
	header('HTTP/1.1 401 Unauthorized');
	die;
}


// Get the context
$context = context_module::instance($cm->id);

// Detect if the user has the capability to view this course module
if (!has_capability('mod/elang:view', $context))
{
	header('HTTP/1.1 403 Forbidden');
	die;
}

// Get the elang instance and the course
$course = $DB->get_record('course', array('id' => $cm->course), '*');
$elang = $DB->get_record('elang', array('id' => $cm->instance), '*');

// Detect an internal server error
if (!$course || !$elang)
{
	header('HTTP/1.1 500 Internal Server Error');
	die;
}

// Log action
add_to_log($course->id, 'elang', 'view', 'server.php?id=' . $cm->id . '&task=' . $task, $elang->id, $cm->id);

switch ($task)
{
	case 'data':
		header('Content-type: application/json');
		$fs = get_file_storage();
		$files = $fs->get_area_files($context->id, 'mod_elang', 'videos', $elang->id);
		$sources = array();
		
		foreach ($files as $file)
		{
			if ($file->get_source())
			{
				$sources[] = array(
					'url' => (string) moodle_url::make_pluginfile_url(
						$file->get_contextid(),
						$file->get_component(),
						$file->get_filearea(),
						$file->get_itemid(),
						$file->get_filepath(),
						$file->get_filename()
					),
					'type' => $file->get_mimetype()
				);
			}
		}

		$files = $fs->get_area_files($context->id, 'mod_elang', 'poster', $elang->id);
		$poster = '';

		foreach ($files as $file)
		{
			if ($file->get_source())
			{
				$poster = (string) moodle_url::make_pluginfile_url(
					$file->get_contextid(),
					$file->get_component(),
					$file->get_filearea(),
					$file->get_itemid(),
					$file->get_filepath(),
					$file->get_filename()
				);
				break;
			}
		}

		$files = $fs->get_area_files($context->id, 'mod_elang', 'subtitle', $elang->id);
		$subtitle = '';

		foreach ($files as $file)
		{
			if ($file->get_source())
			{
				$subtitle = (string) moodle_url::make_pluginfile_url(
					$file->get_contextid(),
					$file->get_component(),
					$file->get_filearea(),
					$file->get_itemid(),
					$file->get_filepath(),
					$file->get_filename()
				);
				break;
			}
		}

		$sequences = array();
        $records = $DB->get_records('elang_cue', array('id_elang' => $elang->id), 'begin ASC');
        foreach ($records as $record)
        {
        	$sequences[] = array('id'=> $record->id, 'titre'=> $record->title, 'debut'=>$record->begin / 1000, 'fin'=>$record->end / 1000);
        }

		echo json_encode(array(
			'title' => $elang->name,
			'description' => $elang->intro,
			'sequences' => $sequences,
			'inputs' => array(),
			'sources' => $sources,
			'poster' => $poster,
			'track' => $subtitle,
			'language' => $elang->language
		));
		die;
		break;
	default:
		header('HTTP/1.1 400 Bad Request');
		die;
		break;
}

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
//header('HTTP/1.1 404 Not Found');
//header('HTTP/1.1 403 Forbidden');
//header('HTTP/1.1 400 Bad Request');
//header('HTTP/1.1 401 Unauthorized');
//header('HTTP/1.1 500 Internal Server Error');
//header('HTTP/1.1 501 Not Implemented');
//header('HTTP/1.1 503 Service Unavailable');
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
