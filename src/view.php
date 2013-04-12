<?php

/**
 * Prints a particular instance of elang
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage elang
 * @copyright  2013 University of La Rochelle, France
 * @license    http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/parseWebVTT.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // elang instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('elang', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $elang  = $DB->get_record('elang', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $elang  = $DB->get_record('elang', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $elang->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('elang', $elang->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);

add_to_log($course->id, 'elang', 'view', "view.php?id={$cm->id}", $elang->name, $cm->id);

///// The way to display files url /////
$contextid = $context->id;
$component = 'mod_elang';
$filearea = 'videos';
$fs = get_file_storage();
$files = $fs->get_area_files($contextid, $component, $filearea, 0);

$i = 0;
foreach($files as $file)
{
	//TODO :
	$fullurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename());
	echo $fullurl . '<br/>';
	
	if($i > 0)
	{
		//redirect($fullurl);
	}
	$i++;
}
///// END /////

/// Print the page header

$PAGE->set_url('/mod/elang/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($elang->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('elang-'.$somevar);
		
//Ajax

//tache a effectuer
$task=$_REQUEST['task'];

//Evite les injections SQL
if(isset($_REQUEST['idEx'])){$idEx = (int)$_REQUEST['idEx'];}
if(isset($_REQUEST['idVideo'])){$idVideo = (int)$_REQUEST['idVideo'];}
if(isset($_REQUEST['idCue'])){$idCue = (int)$_REQUEST['idCue'];}
if(isset($_REQUEST['numSeq'])){$numSeq = (int)$_REQUEST['numSeq'];}

if(!empty($task)){
	
	switch ($task)
	{
		case 'format':
			//Get formats from identicals videos
			$req = 'SELECT v.format FROM mdl_elang_video v
			WHERE v.id_elang ='.$idEx;
			break;
		case 'time_seq':
			//Get the time of a sequence
			$req = 'SELECT (c.end - c.begin) FROM mdl_elang_cue c
			WHERE c.id ='.$idCue;
			break;
			
		case 'cue':
			//Get all cue's datas taking a video as parameter
			$req = 'SELECT c.id, c.id_elang, c.begin, c.end, c.title, c.cuetext
			FROM mdl_elang_video v, mdl_elang_cue c 
			WHERE v.id_elang = c.id_elang
			AND v.id='.$idVideo;
			break;
			
		case 'cue2':
			//Get all cue's datas taking a video as parameter
			$req = 'SELECT c.id, c.id_elang, c.begin, c.end, c.title, c.cuetext
			FROM mdl_elang_video v, mdl_elang_cue c 
			WHERE v.id_elang = c.id_elang
			AND c.id = '.$numSeq.'
			AND v.id= '.$idVideo;
			break;	
		
		case 'display_webvtt':
			$vtt = new WebVTT();
			$req1 = 'SELECT c.begin, c.end, c.title, c.cuetext FROM mdl_elang_cue c WHERE c.id_elang = '.$idEx;
			$result1 = get_records_sql($req1);
			foreach($result1 as $line){
				$seq = new Cue;
				$seq->setBegin($line->begin);
				$seq->setEnd($line->end);
				$seq->setTitle($line->title);
				$seq->setText($line->cuetext);
				$vtt->addCue($seq);
			}
			header('Cache-Control: no-cache, must-revalidate');
			header('Content-type: text/vtt');
			echo $vtt;
			break;
			
		case 'date':
			//Put the date of the end of the exercise in the database 
			global $USER;
			$user = 2;
			$record = new stdClass();
			$record->idcue = $idCue;
			$record->iduser = $user;
			$record->date = '2013-04-12 00:00:00';//date('l /t/h/e js');
			
			/*
			$user = 2;
			$req = 'INSERT INTO mdl_elang_ask_correction (idcue,iduser,date) VALUES ('.$idCue.','.$user.', CURRENT_DATE)';*/
			break;
			
		case 'WebVTT':
			$req = 'SELECT e.language FROM mdl_elang e
			WHERE e.id = '.$idEx;
			$req2 = 'SELECT c.begin, c.end FROM mdl_elang_cue c
			WHERE c.id_elang ='.$idEx;
			$lang = get_records_sql($req);
			$time = get_records_sql($req2);
			$answer = new stdClass;
			$answer->status = 200;
			$answer->result = new stdClass;
			$answer->result->lang = $lang;
			$answer->result->time = $time;
			$answer->result->url = '../moodle/elang/view.php?id=idEx&task=display_webvtt';
			header('Cache-Control: no-cache, must-revalidate');
			header('Content-type: application/json');
			echo json_encode($result);
			break;
			
		default:$rien=true;
	}//fin switch

	if(!isset($rien)){
		$detail = explode(" ",$req);
		if($detail[0]=='SELECT'){
	
			$result = get_records_sql($req);
			$result = json_encode($result);
			header('Cache-Control: no-cache, must-revalidate');
			header('Content-type: application/json');
			
		}else{
		
			$DB->insert_record('elang_ask_correction', $record, false);
		
		}//fin if/else
	
	}//fin if

}//fin if

//FIN Ajax

// Output starts here
echo $OUTPUT->header();

if ($elang->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('elang', $elang, $cm->id), 'generalbox mod_introbox', 'elangintro');
}

// Replace the following lines with you own code
echo $OUTPUT->heading('Yay! It works!');

// Finish the page
echo $OUTPUT->footer();
