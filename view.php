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

/// Print the page header

$PAGE->set_url('/mod/elang/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($elang->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('elang-'.$somevar);

// Output starts here
echo $OUTPUT->header();

if ($elang->intro) { // Conditions to show the intro can change to look for own settings or whatever
    echo $OUTPUT->box(format_module_intro('elang', $elang, $cm->id), 'generalbox mod_introbox', 'elangintro');
}

// Replace the following lines with you own code
echo $OUTPUT->heading('Yay! It works!');

// Finish the page
echo $OUTPUT->footer();
