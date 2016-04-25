<?php

/**
 * This is a one-line short description of the file
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2016 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */

require_once dirname(dirname(dirname(__FILE__))) . '/config.php';
require_once dirname(__FILE__) . '/lib.php';

// Get the course number
$id = required_param('id', PARAM_INT);

// Get the course
$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

// Verify the login
require_course_login($course);

// Get the moodle version
$version = moodle_major_version(true);

// Add a view all log
if (version_compare($version, '2.7') < 0)
{
	add_to_log($course->id, 'elang', 'view all', 'index.php?id=' . $course->id, '');
}
else
{
	$event = \mod_elang\event\course_module_instance_list_viewed::create(array('context' => context_course::instance($course->id)));
	$event->trigger();
}

// Get the context from the course id
$coursecontext = context_course::instance($course->id);

// Prepare the page output
$PAGE->set_url('/mod/elang/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

// Send the header
echo $OUTPUT->header();

// Detect existence of elang modules in this course
if (! $elangs = get_all_instances_in_course('elang', $course))
{
	notice(get_string('noelangs', 'elang'), new moodle_url('/course/view.php', array('id' => $course->id)));
}

// Prepare an html table
$table = new html_table;

if ($course->format == 'weeks')
{
	$table->head  = array(get_string('week'), get_string('name'));
	$table->align = array('center', 'left');
}
elseif ($course->format == 'topics')
{
	$table->head  = array(get_string('topic'), get_string('name'));
	$table->align = array('center', 'left', 'left', 'left');
}
else
{
	$table->head  = array(get_string('name'));
	$table->align = array('left', 'left', 'left');
}

// For all elang instance, prepare table rows
foreach ($elangs as $elang)
{
	// Decode elang options
	$options = json_decode($elang->options, true);

	// Prepare the activity module name
	if ($options['showlanguage'])
	{
		require_once dirname(__FILE__) . '/locallib.php';
		$languages = Elang\getLanguages();
		$name = sprintf(get_string('formatname', 'elang'), $elang->name, $languages[$elang->language]);
	}
	else
	{
		$name = $elang->name;
	}

	// Detect activity module visibility
	if (!$elang->visible)
	{
		$link = html_writer::link(
			new moodle_url('/mod/elang.php', array('id' => $elang->coursemodule)),
			format_string($name, true),
			array('class' => 'dimmed')
		);
	}
	else
	{
		$link = html_writer::link(
			new moodle_url('/mod/elang.php', array('id' => $elang->coursemodule)),
			format_string($name, true)
		);
	}

	// Detect activity module format
	if ($course->format == 'weeks' || $course->format == 'topics')
	{
		$table->data[] = array($elang->section, $link);
	}
	else
	{
		$table->data[] = array($link);
	}
}

// Print table
echo $OUTPUT->heading(get_string('modulenameplural', 'elang'), 2);
echo html_writer::table($table);
echo $OUTPUT->footer();
