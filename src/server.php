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
		$i = 0;
        $records = $DB->get_records('elang_cue', array('id_elang' => $elang->id), 'begin ASC');
        foreach ($records as $record)
        {
			$text = json_decode($record->json);
			$elements = array();
			foreach ($text as $element)
			{
				if ($element[0] == '[' && $element[strlen($element) - 1] == ']')
				{
					$elements[] = array(
						'type' => 'input',
						'size' => ((int) (mb_strlen($element, 'UTF-8') - 1) / 10 + 1) * 10
					);
				}
				else
				{
					$elements[] = array(
						'type' => 'text',
						'content' => $element
					);
				}
			}

        	$sequences[] = array(
        		'number' => $i++,
        		'id' => $record->id,
        		'title' => $record->title,
        		'begin' => $record->begin / 1000,
        		'end' => $record->end / 1000,
        		'elements' => $elements
        	);
        }

		echo json_encode(array(
			'title' => $elang->name,
			'description' => $elang->intro,
			'number' => 150,
			'success' => 50,
			'error' => 20,
			'help' => 10,
			'sequences' => $sequences,
			'page' => 10,
			'sources' => $sources,
			'poster' => $poster,
			'track' => $subtitle,
			'language' => $elang->language
		));
		die;
		break;
	case 'check':
		header('Content-type: application/json');
		echo json_encode(array('status' => 'success', 'cue' => '<c.error>abcd</c>', 'text' => 'ici'));
		die;
		break;
	default:
		header('HTTP/1.1 400 Bad Request');
		die;
		break;
}
