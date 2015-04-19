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
 * @copyright   2013-2015 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the information on whether the module supports a feature
 *
 * @param   string  $feature  FEATURE_xx constant for requested feature
 *
 * @return  mixed   true if the feature is supported, null if unknown
 *
 * @see plugin_supports() in lib/moodlelib.php
 *
 * @category  core
 *
 * @since   0.0.1
 */
function elang_supports($feature)
{
	switch ($feature)
	{
		case FEATURE_MOD_INTRO:
			return true;
		case FEATURE_SHOW_DESCRIPTION:
			return true;

/**
		// Usefull for backup, restore and clone action
		// TODO: implement backup
		case FEATURE_BACKUP_MOODLE2:
			return true;
*/
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
 * @param   object              $elang  An object from the form in mod_form.php
 * @param   mod_elang_mod_form  $mform  The form
 *
 * @return  int  The id of the newly inserted elang record
 *
 * @category  core
 *
 * @since   0.0.1
 */
function elang_add_instance(stdClass $elang, mod_elang_mod_form $mform = null)
{
	global $DB;

	// Init some fields
	$elang->timemodified = time();
	$elang->timecreated = time();
	$elang->options = json_encode(
		array(
			'showlanguage' => isset($elang->showlanguage) ? true : false,
			'repeatedunderscore' => isset($elang->repeatedunderscore) ? $elang->repeatedunderscore : 10,
			'titlelength' => isset($elang->titlelength) ? $elang->titlelength : 100,
			'limit' => isset($elang->limit) ? $elang->limit : 10,
			'left' => isset($elang->left) ? $elang->left : 20,
			'top' => isset($elang->top) ? $elang->top : 20,
			'size' => isset($elang->size) ? $elang->size : 16,
		)
	);

	// Storage of the main table of the module
	$elang->id = $DB->insert_record('elang', $elang);

	// Storage of files
	require_once dirname(__FILE__) . '/locallib.php';
	Elang\saveFiles($elang, $mform);

	return $elang->id;
}

/**
 * Updates an instance of the elang in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param   object              $elang  An object from the form in mod_form.php
 * @param   mod_elang_mod_form  $mform  The form
 *
 * @return  boolean  Success/Fail
 *
 * @category  core
 *
 * @since   0.0.1
 */
function elang_update_instance(stdClass $elang, mod_elang_mod_form $mform = null)
{
	global $DB;

	// Init some fields
	$elang->timemodified = time();
	$elang->id = $elang->instance;
	$elang->options = json_encode(
		array(
			'showlanguage' => isset($elang->showlanguage) ? true : false,
			'repeatedunderscore' => isset($elang->repeatedunderscore) ? $elang->repeatedunderscore : 10,
			'titlelength' => isset($elang->titlelength) ? $elang->titlelength : 100,
			'limit' => isset($elang->limit) ? $elang->limit : 10,
			'left' => isset($elang->left) ? $elang->left : 20,
			'top' => isset($elang->top) ? $elang->top : 20,
			'size' => isset($elang->size) ? $elang->size : 16,
		)
	);

	// Update of the main table of the module
	if ($DB->update_record('elang', $elang))
	{
		// Storage of files
		require_once dirname(__FILE__) . '/locallib.php';
		Elang\saveFiles($elang, $mform);

		return true;
	}
	else
	{
		return false;
	}
}

/**
 * Removes an instance of the elang from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param   integer  $id  Id of the module instance
 *
 * @return  boolean   Success/Failure
 *
 * @category  core
 *
 * @since   0.0.1
 */
function elang_delete_instance($id)
{
	global $DB;

	if (! $elang = $DB->get_record('elang', array('id' => $id)))
	{
		return false;
	}

	$result = true;

	// Delete any dependent records
	if (! $DB->delete_records('elang', array('id' => $elang->id)))
	{
		$result = false;
	}

	if (! $DB->delete_records('elang_cues', array('id_elang' => $elang->id)))
	{
		$result = false;
	}

	if (! $DB->delete_records('elang_users', array('id_elang' => $elang->id)))
	{
		$result = false;
	}

	if (! $DB->delete_records('elang_help', array('id_elang' => $elang->id)))
	{
		$result = false;
	}

	if (! $DB->delete_records('elang_check', array('id_elang' => $elang->id)))
	{
		$result = false;
	}

	return $result;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @param   stdClass  $course  the current course record
 * @param   stdClass  $user    the record of the user we are generating report for
 * @param   cm_info   $mod     course module info
 * @param   stdClass  $elang   the module instance record
 *
 * @return  stdClass|null
 *
 * @category  core
 *
 * @since   0.0.1
 */
function elang_user_outline($course, $user, $mod, $elang)
{
	$return = new stdClass;
	$return->time = 0;
	$return->info = '';

	return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param   stdClass  $course  the current course record
 * @param   stdClass  $user    the record of the user we are generating report for
 * @param   cm_info   $mod     course module info
 * @param   stdClass  $elang   the module instance record
 *
 * @return  void, is supposed to echp directly
 *
 * @category  core
 *
 * @since   0.0.1
 */
function elang_user_complete($course, $user, $mod, $elang)
{
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in elang activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @param   mixed    $course         the course to print activity for
 * @param   boolean  $viewfullnames  boolean to determine whether to show full names or not
 * @param   integer  $timestart      the time the rendering started
 *
 * @return  boolean
 *
 * @category  core
 *
 * @since   0.0.1
 */
function elang_print_recent_activity($course, $viewfullnames, $timestart)
{
	// True if anything was printed, otherwise false
	return false;
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link elang_print_recent_mod_activity()}.
 *
 * @param   array    &$activities  sequentially indexed array of objects with the 'cmid' property
 * @param   integer  &$index       the index in the $activities to use for the next record
 * @param   integer  $timestart    append activity since this time
 * @param   integer  $courseid     the id of the course we produce the report for
 * @param   integer  $cmid         course module id
 * @param   integer  $userid       check for a particular user's activity only, defaults to 0 (all users)
 * @param   integer  $groupid      check for a particular group's activity only, defaults to 0 (all groups)
 *
 * @return  void  adds items into $activities and increases $index
 *
 * @category  core
 *
 * @since   0.0.1
 */
function elang_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0)
{
}

/**
 * Prints single activity item prepared by {@see elang_get_recent_mod_activity()}
 *
 * @param   stdClass  $activity       The activity module
 * @param   integer   $courseid       The course id
 * @param   boolean   $detail         TRUE for details
 * @param   array     $modnames       Module names
 * @param   boolean   $viewfullnames  TRUE for full names
 *
 * @return void
 *
 * @since   0.0.1
 */
function elang_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames)
{
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return  boolean
 *
 * @todo Finish documenting this function
 *
 * @category  core
 *
 * @since   0.0.1
 */
function elang_cron ()
{
	return true;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 *
 * @return  array
 *
 * @category  core
 *
 * @since   0.0.1
 */
function elang_get_extra_capabilities()
{
	return array();
}

/**
 * Given a course_module object, this function returns any
 * "extra" information that may be needed when printing
 * this activity in a course listing.
 *
 * See {@link get_array_of_activities()} in course/lib.php
 *
 * @param   object  $coursemodule  Course module
 *
 * @return  object  info
 *
 * @since   0.0.1
 */
function elang_get_coursemodule_info($coursemodule)
{
	global $DB;

	$elang = $DB->get_record('elang', array('id' => $coursemodule->instance));

	if (!$elang)
	{
		return null;
	}

	$options = json_decode($elang->options, true);
	$info = new cached_cm_info;

	require_once dirname(__FILE__) . '/locallib.php';
	$info->name = Elang\generateTitle($elang, $options);

	$info->onclick = "window.open('" . new moodle_url('/mod/elang/view.php', array('id' => $coursemodule->id)) . "'); return false;";

	if ($coursemodule->showdescription)
	{
		// Convert intro to html. Do not filter cached version, filters run at display time.
		$info->content = format_module_intro('elang', $elang, $coursemodule->id, false);
	}

	return $info;
}

/**
 * Return the list of view actions
 *
 * @return array
 *
 * @catgory  code
 *
 * @since   0.0.1
 */
function elang_get_view_actions()
{
	return array('view', 'view help');
}

/**
 * Return the list of post actions
 *
 * @return array
 *
 * @catgory  code
 *
 * @since   0.0.1
 */
function elang_get_post_actions()
{
	return array('add check');
}

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param   stdClass  $course   the current course record
 * @param   stdClass  $cm       the course module record
 * @param   stdClass  $context  the current context
 *
 * @return  array of [(string)filearea] => (string)description
 *
 * @category  files
 *
 * @since   0.0.1
 */
function elang_get_file_areas($course, $cm, $context)
{
	return array(
		'poster' => get_string('filearea_poster', 'elang'),
		'videos' => get_string('filearea_videos', 'elang'),
		'subtitle' => get_string('filearea_subtitle', 'elang'),
	);
}

/**
 * File browsing support for elang file areas
 *
 * @param   file_browser  $browser   File browser object
 * @param   array         $areas     the areas
 * @param   stdClass      $course    the course
 * @param   stdClass      $cm        the course module
 * @param   stdClass      $context   the current context
 * @param   string        $filearea  file area
 * @param   integer       $itemid    item ID
 * @param   string        $filepath  file path
 * @param   string        $filename  file name
 *
 * @return  file_info instance or null if not found
 *
 * @category  files
 *
 * @since   0.0.1
 */
function elang_get_file_info($browser, $areas, $course, $cm, $context, $filearea, $itemid, $filepath, $filename)
{
	global $CFG;

	if (!has_capability('moodle/course:managefiles', $context))
	{
		// Students can not peak here!
		return null;
	}

	$fs = get_file_storage();

	if ($filearea === 'videos' || $filearea === 'poster' || $filearea === 'subtitle')
	{
		$filepath = is_null($filepath) ? '/' : $filepath;
		$filename = is_null($filename) ? '.' : $filename;

		$urlbase = $CFG->wwwroot . '/pluginfile.php';

		if (!$storedfile = $fs->get_file($context->id, 'mod_elang', $filearea, 0, $filepath, $filename))
		{
			// Not found
			return null;
		}

		return new file_info_stored($browser, $context, $storedfile, $urlbase, $areas[$filearea], false, true, true, false);
	}

	// Not found
	return null;
}

/**
 * Serves the files from the elang file areas
 *
 * @param   stdClass  $course         the course object
 * @param   stdClass  $cm             the course module object
 * @param   stdClass  $context        the elang's context
 * @param   string    $filearea       the name of the file area
 * @param   array     $args           extra arguments (itemid, path)
 * @param   boolean   $forcedownload  whether or not force download
 * @param   array     $options        additional options affecting the file serving
 *
 * @return  void
 *
 * @category  files
 *
 * @since   0.0.1
 */
function elang_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array())
{
	global $DB, $CFG, $USER;

	require_once dirname(__FILE__) . '/locallib.php';

	if ($context->contextlevel != CONTEXT_MODULE)
	{
		send_file_not_found();
	}

	require_login($course, true, $cm);

	if (!has_capability('mod/elang:view', $context))
	{
		send_file_not_found();
	}

	if ($filearea == 'subtitle')
	{
		$vtt = new \Captioning\Format\WebvttFile;

		$idlang = $cm->instance;
		$records = $DB->get_records('elang_cues', array('id_elang' => $idlang), 'begin ASC');
		$elang = $DB->get_record('elang', array('id' => $idlang));
		$options = json_decode($elang->options, true);
		$repeatedunderscore = isset($options['repeatedunderscore']) ? $options['repeatedunderscore'] : 10;
		$i = 0;
		$users = $DB->get_records('elang_users', array('id_elang' => $idlang, 'id_user' => $USER->id), '', 'id_cue,json');

		foreach ($records as $id => $record)
		{
			if (isset($users[$id]))
			{
				$data = json_decode($users[$id]->json, true);
			}
			else
			{
				$data = array();
			}

			$cue = new \Captioning\Format\WebvttCue(
				\Captioning\Format\WebvttCue::ms2tc($record->begin),
				\Captioning\Format\WebvttCue::ms2tc($record->end),
				Elang\generateCueText(json_decode($record->json, true), $data, '-', $repeatedunderscore)
			);

			$i++;
			$cue->setIdentifier($i);
			$vtt->addCue($cue);
		}

		send_file($vtt->build()->getFileContent(), end($args), 0, 0, true, false, 'text/vtt');
	}
	elseif ($filearea == 'pdf')
	{
		$idlang = $cm->instance;
		$records = $DB->get_records('elang_cues', array('id_elang' => $idlang), 'begin ASC');
		$elang = $DB->get_record('elang', array('id' => $idlang));
		$options = json_decode($elang->options, true);
		$repeatedunderscore = isset($options['repeatedunderscore']) ? $options['repeatedunderscore'] : 10;

		$cue = new Elang\Cue;

		require_once $CFG->libdir . '/pdflib.php';
		$doc = new pdf;
		$doc->SetMargins(isset($options['left']) ? $options['left'] : 20, isset($options['top']) ? $options['top'] : 20);
		$doc->SetFont('', '', isset($options['size']) ? $options['size'] : 16);
		$doc->setPrintHeader(false);
		$doc->setPrintFooter(false);
		$doc->AddPage();
		$doc->WriteHtml('<h1>' . sprintf(get_string('pdftitle', 'elang'), $course->fullname) . '</h1>');
		$doc->WriteHtml(
			'<h2>' . sprintf(
				get_string('pdfsubtitle', 'elang'),
				Elang\generateTitle($elang, $options),
				userdate($elang->timecreated, get_string('strftimedaydate'))
			) . '</h2>'
		);
		$doc->WriteHtml($elang->intro);

		$i = 1;

		foreach ($records as $id => $record)
		{
			$cue->setBegin($record->begin);
			$cue->setEnd($record->end);
			$doc->Write(5, '', '', false, '', true);
			$doc->WriteHtml(
				'<h3>' .
				sprintf(get_string('pdfcue', 'elang'), $i++, Elang\Cue::millisecondsToString($record->begin), Elang\Cue::millisecondsToString($record->end)) .
				'</h3>'
			);
			$doc->Write(5, Elang\generateCueText(json_decode($record->json, true), array(), '_', $repeatedunderscore), '', false, '', true);
		}

		send_file($doc->Output('', 'S'), end($args), 0, 0, true, false, 'application/pdf');
	}
	else
	{
		$fs = get_file_storage();
		$relativepath = implode('/', $args);
		$fullpath = rtrim('/' . $context->id . '/mod_elang/' . $filearea . '/0/' . $relativepath, '/');
		$file = $fs->get_file_by_hash(sha1($fullpath));

		if (!$file)
		{
			send_file_not_found();
		}

		send_stored_file($file, 86400, 0, $forcedownload, $options);
	}
}
