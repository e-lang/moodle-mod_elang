<?php

/**
 * Library of interface functions and constants for module elang
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the elang specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package	mod
 * @subpackage elang
 * @copyright  2013 University of La Rochelle, France
 * @license	http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

defined('MOODLE_INTERNAL') || die();

//Include of the subtitles's function
//(WARNING : if the main page of the module is not displayed,
//it's because of the require_once) :
require_once('parseWebVTT.php');

/** example constant */
//define('NEWMODULE_ULTIMATE_ANSWER', 42);

////////////////////////////////////////////////////////////////////////////////
// Moodle core API															//
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function elang_supports($feature) {
	switch($feature) {
		case FEATURE_MOD_INTRO:		 return true;
		default:						return null;
	}
}

/**
 * Saves a new instance of the elang into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $elang An object from the form in mod_form.php
 * @param mod_elang_mod_form $mform
 * @return int The id of the newly inserted elang record
 */
function elang_add_instance(stdClass $elang, mod_elang_mod_form $mform = null) {
	global $CFG, $DB;
	require_once("$CFG->libdir/resourcelib.php");
	require_once("$CFG->dirroot/mod/resource/locallib.php");
   
	//Init some var :
	$cmid = $elang->coursemodule;
	$elang->timemodified = time();
	$elang->timecreated = time();
	
	//Storage of the main table of the module :
	$elang->id = $DB->insert_record('elang', $elang);
	
	//Storage of files from the filemanager (videos) :
	$fs = get_file_storage();
	$cmid = $data->coursemodule;
	$draftitemid = $data->videos;	
	$context = context_module::instance($cmid);
	if ($draftitemid) {
		file_save_draft_area_files($draftitemid, $context->id, 'mod_elang', 'videos', 0, array('subdirs'=>true));
	}
	
	//Recuperer le fichier des sous titres de la base de donnees moodle
	$itemid = file_get_submitted_draft_itemid('subtitle');//recuperer le itemid du fichier
	$filename=$mform->get_new_filename('subtitle');//recuperer le nom du fichier
	$fs = get_file_storage();//recuperer les fichiers sotckes dans la base moodle
	
	// preparer le fichier record object
	$fileinfo = array(
    'component' => 'user',     
    'filearea' => 'draft',    
    'itemid' => $itemid ,              
	'contextid' =>5,
    'filepath' => '/',           
    'filename' => $filename); 
 
	// Get file
	$file = $fs->get_file($fileinfo['contextid'],$fileinfo['component'], $fileinfo['filearea'],
						  $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
	 
	// lire le contenu du fichier
	if ($file) {
		$contents = $file->get_content();
		//traitement du contenu du fichier 
		$ab =new parsewebvtt\WebVTT($contents);
		
		$cue = new stdClass();
		$elang->id= $DB->insert_record('elang', $elang);//recuper id de l exercice aui sera attribue aux sequences
		
		foreach($ab->getCueList() as $i => $elt) 
		
		{
			$cue->id_elang=$elang->id;		//recuperer id de l exercice
			$cue->title	= $elt->getTitle();//le titre
			$cue->begin	=  $elt->getBegin();//begin
			$cue->end= $elt->getend();
			$cue->cuetext=$elt->getText();
			
			$lastInsertID = $DB->insert_record("elang_cue", $cue);//inserer dans la base
		}
		
	} else {
		print("file doesn't exist - do something");
	}
	
	//Use this for display out in a debug file :
	/*ob_start();
	print_object($elang);
	echo "test";
	$result = ob_get_clean();
	debug($result);*/
	
	return $elang->id;
}

//This function write a string in a debug file at the root :
function debug($str)
{
	$debug = fopen('/debug.txt', 'a');
	fputs($debug,  $str . "\n");
}

/**
 * Updates an instance of the elang in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $elang An object from the form in mod_form.php
 * @param mod_elang_mod_form $mform
 * @return boolean Success/Fail
 */
function elang_update_instance(stdClass $elang, mod_elang_mod_form $mform = null) {
	global $DB;

	$elang->timemodified = time();
	$elang->id = $elang->instance;

	# You may have to add extra stuff in here #

	return $DB->update_record('elang', $elang);
}

/**
 * Removes an instance of the elang from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function elang_delete_instance($id) {
	global $DB;

	if (! $elang = $DB->get_record('elang', array('id' => $id))) {
		return false;
	}

	# Delete any dependent records here #

	$DB->delete_records('elang', array('id' => $elang->id));

	return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function elang_user_outline($course, $user, $mod, $elang) {

	$return = new stdClass();
	$return->time = 0;
	$return->info = '';
	return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $elang the module instance record
 * @return void, is supposed to echp directly
 */
function elang_user_complete($course, $user, $mod, $elang) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in elang activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function elang_print_recent_activity($course, $viewfullnames, $timestart) {
	return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link elang_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function elang_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see elang_get_recent_mod_activity()}

 * @return void
 */
function elang_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function elang_cron () {
	return true;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function elang_get_extra_capabilities() {
	return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API															  //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of elang?
 *
 * This function returns if a scale is being used by one elang
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $elangid ID of an instance of this module
 * @return bool true if the scale is used by the given elang instance
 */
function elang_scale_used($elangid, $scaleid) {
	global $DB;

	/** @example */
	if ($scaleid and $DB->record_exists('elang', array('id' => $elangid, 'grade' => -$scaleid))) {
		return true;
	} else {
		return false;
	}
}

/**
 * Checks if scale is being used by any instance of elang.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any elang instance
 */
function elang_scale_used_anywhere($scaleid) {
	global $DB;

	/** @example */
	if ($scaleid and $DB->record_exists('elang', array('grade' => -$scaleid))) {
		return true;
	} else {
		return false;
	}
}

/**
 * Creates or updates grade item for the give elang instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $elang instance object with extra cmidnumber and modname property
 * @return void
 */
function elang_grade_item_update(stdClass $elang) {
	global $CFG;
	require_once($CFG->libdir.'/gradelib.php');

	/** @example */
	$item = array();
	$item['itemname'] = clean_param($elang->name, PARAM_NOTAGS);
	$item['gradetype'] = GRADE_TYPE_VALUE;
	$item['grademax']  = $elang->grade;
	$item['grademin']  = 0;

	grade_update('mod/elang', $elang->course, 'mod', 'elang', $elang->id, 0, null, $item);
}

/**
 * Update elang grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $elang instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function elang_update_grades(stdClass $elang, $userid = 0) {
	global $CFG, $DB;
	require_once($CFG->libdir.'/gradelib.php');

	/** @example */
	$grades = array(); // populate array of grade objects indexed by userid

	grade_update('mod/elang', $elang->course, 'mod', 'elang', $elang->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API																   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function elang_get_file_areas($course, $cm, $context) {
	return array();
}

/**
 * File browsing support for elang file areas
 *
 * @package mod_elang
 * @category files
 *
 * @param file_browser $browser
 * @param array $areas
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param int $itemid
 * @param string $filepath
 * @param string $filename
 * @return file_info instance or null if not found
 */
function elang_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename) {
	return null;
}

/**
 * Serves the files from the elang file areas
 *
 * @package mod_elang
 * @category files
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the elang's context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 */
function elang_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
	global $DB, $CFG;

	if ($context->contextlevel != CONTEXT_MODULE) {
		send_file_not_found();
	}

	require_login($course, true, $cm);

	send_file_not_found();
}

////////////////////////////////////////////////////////////////////////////////
// Navigation API															 //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding elang nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the elang module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function elang_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
}

/**
 * Extends the settings navigation with the elang settings
 *
 * This function is called when the context for the page is a elang module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $elangnode {@link navigation_node}
 */
function elang_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $elangnode=null) {
}
