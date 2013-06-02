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
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

defined('MOODLE_INTERNAL') || die();

// Moodle core API

/**
 * Returns the information on whether the module supports a feature
 *
 * @param   string  $feature  FEATURE_xx constant for requested feature
 *
 * @return  mixed   true if the feature is supported, null if unknown
 *
 * @see plugin_supports() in lib/moodlelib.php
 */
function elang_supports($feature)
{
	switch ($feature)
	{
		case FEATURE_MOD_INTRO:
			return true;
		case FEATURE_SHOW_DESCRIPTION:
			return true;
		default:
			return null;
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
 * @param   object			  $elang  An object from the form in mod_form.php
 * @param   mod_elang_mod_form  $mform  The form
 *
 * @return  int  The id of the newly inserted elang record
 */
function elang_add_instance(stdClass $elang, mod_elang_mod_form $mform = null)
{
	global $DB;

	// Init some var :
	$elang->timemodified = time();
	$elang->timecreated = time();

	// Storage of the main table of the module :
	$elang->id = $DB->insert_record('elang', $elang);

	// Storage of files
	elang_save_files($elang);

	return $elang->id;
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
function elang_update_instance(stdClass $elang, mod_elang_mod_form $mform = null)
{
	global $DB;

	$elang->timemodified = time();
	$elang->id = $elang->instance;

	if ($DB->update_record('elang', $elang))
	{
		// Storage of files
		elang_save_files($elang);
		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Save files for an instance
 */
function elang_save_files(stdClass $elang)
{
	global $DB;
	$id = $elang->id;
	$cmid = $elang->coursemodule;
	$context = context_module::instance($cmid);

	// Storage of files from the filemanager (videos):
	$draftitemid = $elang->videos;
	if ($draftitemid)
	{
		file_save_draft_area_files(
			$draftitemid,
			$context->id,
			'mod_elang',
			'videos',
			$id
		);
	}

	// Storage of files from the filemanager (subtitle):
	$draftitemid = $elang->subtitle;
	if ($draftitemid)
	{
		file_save_draft_area_files(
			$draftitemid,
			$context->id,
			'mod_elang',
			'subtitle',
			$id
		);
	}

	// Storage of files from the filemanager (poster):
	$draftitemid = $elang->poster;
	if ($draftitemid)
	{
		file_save_draft_area_files(
			$draftitemid,
			$context->id,
			'mod_elang',
			'poster',
			$id
		);
	}

	$DB->delete_records('elang_cue', array('id_elang' => $id));
	$fs = get_file_storage();
	$files = $fs->get_area_files($context->id, 'mod_elang', 'subtitle', $id);
	foreach ($files as $file)
	{
		if ($file->get_source())
		{
			$contents = $file->get_content();
			//traitement du contenu du fichier
			require_once('parseWebVTT.php');
			$vtt = new parsewebvtt\WebVTT($contents);

			$cue = new stdClass();

			foreach($vtt->getCueList() as $i => $elt)
			{
				$cue->id_elang = $id;
				$cue->title	= $elt->getTitle();
				$cue->begin	=  $elt->getBegin();
				$cue->end = $elt->getend();
				$cue->text = json_encode(preg_split('/(\[[^\]]*\])/', strip_tags($elt->getText()), -1, PREG_SPLIT_DELIM_CAPTURE));

				$DB->insert_record('elang_cue', $cue);
			}
		}
	}
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

	if (!has_capability('mod/elang:view', $context))
	{
		send_file_not_found();
	}

	if ($filearea == 'subtitle')
	{
		require_once 'parseWebVTT.php';
		$vtt = new parsewebvtt\WebVTT;
		$cue = new parsewebvtt\Cue;

        $records = $DB->get_records('elang_cue', array('id_elang' => reset($args)), 'begin ASC');
        foreach ($records as $record)
        {
			$cue = new parsewebvtt\Cue;
			$cue->setTitle($record->id);
			$cue->setBegin($record->begin);
			$cue->setEnd($record->end);
			$text = json_decode($record->text);
			//var_dump($text);
			foreach ($text as &$element)
			{
				if ($element[0] == '[' && $element[strlen($element) - 1] == ']')
				{
					$element = str_repeat('_', ((int) (strlen($element) / 10) + 1) * 10);
				}
			}
			$cue->setText(implode($text));
			$vtt->addCue($cue);
        }

		send_file((string) $vtt, end($args), 0 , 0, true, false, 'text/vtt');
	}
	else
	{
		$fs = get_file_storage();
		$relativepath = implode('/', $args);
		$fullpath = rtrim('/' . $context->id . '/mod_elang/' . $filearea . '/' . $relativepath, '/');
		$file = $fs->get_file_by_hash(sha1($fullpath));
		if (!$file)
		{
			send_file_not_found();
		}
		send_stored_file($file, 86400, 0, $forcedownload, $options);
	}
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param object $coursemodule
 * @return object info
 */
function elang_get_coursemodule_info($coursemodule)
{
	global $DB;

	$elang = $DB->get_record('elang', array('id' => $coursemodule->instance));
	if (!$elang)
	{
		return null;
	}

	$info = new cached_cm_info();
	$info->name = $elang->name;
	$info->onclick = "window.open('" . new moodle_url('/mod/elang/view.php', array('id' => $coursemodule->id)) ."'); return false;";

	if ($coursemodule->showdescription)
	{
		// Convert intro to html. Do not filter cached version, filters run at display time.
		$info->content = format_module_intro('elang', $elang, $coursemodule->id, false);
	}

	return $info;
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
