<?php

/**
 * Display the report
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2016 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       1.1.0
 */

// This file is included from view.php
defined('MOODLE_INTERNAL') || die();

// Get the moodle form library
require_once $CFG->libdir . '/formslib.php';

/**
 * Module report form
 *
 * @since  1.1.0
 */
class mod_elang_report_form extends moodleform
{
	/**
	 * Define this form - called from the parent constructor.
	 *
	 * @return  void
	 */
	public function definition()
	{
		global $COURSE, $DB;

		$mform = $this->_form;
		$instance = $this->_customdata;
		$dirtyclass = array('class' => 'ignoredirty');

		$mform->addElement('header', 'general', get_string('reportoptions', 'elang'));

		// Visible elements.

		// Group
		$db_groups = $DB->get_records('groups', array('courseid' => $COURSE->id));
		$groups = array(0 => get_string('all'));

		foreach ($db_groups as $group)
		{
			$groups[$group->id] = $group->name;
		}

		$mform->addElement('select', 'group', get_string('studentsgroup', 'elang'), $groups, $dirtyclass)->setSelected(0);

		// Per page
		$options = array(0 => get_string('all'), 10 => '10', 20 => '20', 50 => '50', 100 => '100');
		$mform->addElement('select', 'perpage', get_string('studentsperpage', 'elang'), $options, $dirtyclass)->setSelected(20);

		// Buttons.
		$this->add_action_buttons(false, get_string('updatetable', 'elang'));
	}
}

// Get the user id
$id_user = optional_param('id_user', 0, PARAM_INT);

// Get the solutions
$solutions = $DB->get_records('elang_cues', array('id_elang' => $cm->instance), 'number');

// Prepare the solutions
$count = 0;

foreach ($solutions as $solution)
{
	$json = json_decode($solution->json, true);
	$solution->json = array();

	foreach ($json as $i => $data)
	{
		if ($data['type'] == 'input')
		{
			$solution->json[$i] = $data['content'];
			$count++;
		}
	}
}

// Get an optional format
$format = optional_param('format', '', PARAM_ALPHA);

if ($format == 'csv')
{
	// Log report action
	if (version_compare($version, '2.7') < 0)
	{
		add_to_log($course->id, 'elang', 'view report', 'mod/elang/view.php?id=' . $cm->id . '&format=csv', 'csv', $cm->id, $USER->id);
	}
	else
	{
		// Log this request.
		$params = array(
			'context' => $context,
			'courseid' => $course->id,
			'other' => array('action' => 'csv')
		);
		$event = \mod_elang\event\report_viewed::create($params);
		$event->trigger();
	}

	// Set the http header for csv file
	header('Content-type: application/vnd.ms-excel');
	header('Content-disposition: attachment; filename="' .
		Elang\generateTitle($elang, json_decode($elang->options, true)) . ' ' . date('Y-m-d H:i:s') .
		'.csv"'
	);

	$handle = fopen('php://output', 'w');

	// Prepare first row
	$data = array();
	$data[] = get_string('csvfirstname', 'elang');
	$data[] = get_string('csvlastname', 'elang');
	$data[] = get_string('csvemail', 'elang');

	foreach ($solutions as $solution)
	{
		foreach ($solution->json as $input)
		{
			$data[] = $solution->number;
		}
	}

	// Write header
	fputcsv($handle, $data);

	// Get the group id
	$id_group = optional_param('id_group', 0, PARAM_INT);

	// Get the enrolled users
	$users = get_enrolled_users($context, 'mod/elang:isinreport', $id_group, 'u.id, u.firstname, u.lastname, u.email', 'lastname, firstname, email');

	// Get answers from the users
	if (!empty($users))
	{
		$answers = $DB->get_records('elang_users', array('id_elang' => (int) $cm->instance));
	}
	else
	{
		$answers = array();
	}

	// Prepare the users
	foreach ($users as $user)
	{
		$user->answers = array();
	}

	// Process the answers
	foreach ($answers as $answer)
	{
		if (isset($users[$answer->id_user]))
		{
			$number = $solutions[$answer->id_cue]->number;
			$users[$answer->id_user]->answers[$number] = array();

			$json = json_decode($answer->json, true);

			foreach ($json as $n => $data)
			{
				if ($data['help'])
				{
					$users[$answer->id_user]->answers[$number][$n] = -2;
				}
				elseif ($data['content'] == $solutions[$answer->id_cue]->json[$n])
				{
					$users[$answer->id_user]->answers[$number][$n] = 1;
				}
				elseif ($data['content'] != '')
				{
					$users[$answer->id_user]->answers[$number][$n] = -1;
				}
				else
				{
					$users[$answer->id_user]->answers[$number][$n] = 0;
				}
			}
		}
	}

	// Add the users to the table
	foreach ($users as $user)
	{
		$data = array();
		$data[] = $user->firstname;
		$data[] = $user->lastname;
		$data[] = $user->email;

		foreach ($solutions as $solution)
		{
			if (isset($user->answers[$solution->number]))
			{
				foreach ($solution->json as $n => $input)
				{
					if (isset($user->answers[$solution->number][$n]))
					{
						$data[] = $user->answers[$solution->number][$n];
					}
					else
					{
						$data[] = 0;
					}
				}
			}
			else
			{
				foreach ($solution->json as $input)
				{
					$data[] = 0;
				}
			}
		}

		fputcsv($handle, $data);
	}

	die;
}
else
{
	// Prepare standard output for report
	$PAGE->set_url('/mod/elang/view.php', array('id' => $cm->id));
	$PAGE->set_title(format_string($elang->name));
	$PAGE->set_heading(format_string($course->fullname));

	if ($id_user)
	{
		if (version_compare($version, '2.7') < 0)
		{
			// Log report action
			add_to_log(
				$course->id,
				'elang',
				'view report',
				'mod/elang/view.php?id=' . $cm->id . '&id_user=' . $id_user,
				'user ' . $id_user,
				$cm->id,
				$USER->id
			);
		}
		else
		{
			// Log this request.
			$params = array(
				'context' => $context,
				'courseid' => $course->id,
				'other' => array('action' => 'one', 'id_user' => $id_user)
			);
			$event = \mod_elang\event\report_viewed::create($params);
			$event->trigger();
		}

		// Output starts here.
		echo $OUTPUT->header();

		// Display the report for one user
		if (is_enrolled($context, $id_user, 'mod/elang:isinreport'))
		{
			$student = $DB->get_record('user', array('id' => $id_user), 'firstname, lastname, email');

			// Add one node to the navigation bar
			$coursenode = $PAGE->navigation->find($cm->id, navigation_node::TYPE_ACTIVITY);
			$studentnode = $coursenode->add(
				sprintf(
					get_string('reportonestudent', 'elang'),
					$student->firstname,
					$student->lastname,
					$student->email
				)
			);
			$studentnode->make_active();

			// Prepare the table
			$table = new html_table;

			// Prepare the header for the table
			$table->head = array(
				get_string('number', 'elang'),
				get_string('studenttitle', 'elang'),
				get_string('studentsuccess', 'elang'),
				get_string('studenthelp', 'elang'),
				get_string('studenterror', 'elang'),
				get_string('studentremaining', 'elang')
			);

			// Get the answers
			$answers = $DB->get_records('elang_users', array('id_elang' => $cm->instance, 'id_user' => $id_user));

			$answers2 = array();

			foreach ($answers as $answer)
			{
				$answers2[$answer->id_cue] = json_decode($answer->json, true);
			}

			// Prepare the table data
			$table->data = array();

			foreach ($solutions as $cue => $solution)
			{
				$count = 0;
				$success = 0;
				$help = 0;
				$error = 0;

				// Compute data
				foreach ($solution->json as $n => $data)
				{
					$count++;

					if (isset($answers2[$cue][$n]))
					{
						if ($answers2[$cue][$n]['help'])
						{
							$help++;
						}
						elseif ($answers2[$cue][$n]['content'] == $data)
						{
							$success++;
						}
						elseif ($answers2[$cue][$n]['content'] != '')
						{
							$error++;
						}
					}
				}

				// Add data
				$table->data[] = array($solution->number, $solution->title, $success, $help, $error, $count - $success - $help - $error);
			}

			// Prepare align
			$table->align = array('right', null, 'right', 'right', 'right', 'right');

			// Output the table
			echo html_writer::table($table);
		}
		else
		{
			throw new moodle_exception('unenrolled_user', 'elang');
		}

		// Finish the page.
		echo $OUTPUT->footer();
	}
	else
	{
		if (version_compare($version, '2.7') < 0)
		{
			// Log report action
			add_to_log($course->id, 'elang', 'view report', 'mod/elang/view.php?id=' . $cm->id, 'all', $cm->id, $USER->id);
		}
		else
		{
			// Log this request.
			$params = array(
				'context' => $context,
				'courseid' => $course->id,
				'other' => array('action' => 'all')
			);
			$event = \mod_elang\event\report_viewed::create($params);
			$event->trigger();
		}

		// Output starts here.
		echo $OUTPUT->header();

		// Create the form
		$mform = new mod_elang_report_form((string) new moodle_url('/mod/elang/view.php', array('id' => $cm->id)));

		// Create the session cache
		$cache = cache::make_from_params(cache_store::MODE_SESSION, 'mod_elang', 'useridlist');

		// Get the date from the form
		if ($fromform = $mform->get_data())
		{
			$perpage = $fromform->perpage;
			$id_group = $fromform->group;

			// Store the data in the session
			$cache->set('perpage', $perpage);
			$cache->set('id_group', $id_group);
		}
		else
		{
			// Get the data from the session or get the default
			if ($cache->has('perpage'))
			{
				$perpage = $cache->get('perpage');
			}
			else
			{
				$perpage = 20;
			}

			if ($cache->has('id_group'))
			{
				$id_group = $cache->get('id_group');
			}
			else
			{
				$id_group = 0;
			}

			$toform = array(
				'perpage' => $perpage,
				'id_group' => $id_group
			);

			// Set default data (if any)
			$mform->set_data($toform);
		}

		// Display link to the player
		echo html_writer::link(
			new moodle_url(
				'/mod/elang/view.php',
				array('id' => $cm->id, 'view' => 'player')
			),
			get_string('showplayer', 'elang'),
			array('target' => '_blank')
		);

		echo $OUTPUT->help_icon('showplayer', 'elang');

		// Display the report for a list of users
		echo $OUTPUT->heading(get_string('reportallstudents', 'elang'));

		// Display download link
		$variables = array('id' => $cm->id, 'format' => 'csv');

		if ($id_group != 0)
		{
			$variables['id_group'] = $id_group;
		}

		echo html_writer::link(new moodle_url('/mod/elang/view.php', $variables), get_string('download', 'elang'));

		echo $OUTPUT->help_icon('download', 'elang');

		// Prepare the table
		$table = new html_table;

		// Prepare the header for the table
		$table->head = array(
			sprintf(
				get_string('student', 'elang'),
				html_writer::link(
					new moodle_url(
						'/mod/elang/view.php',
						array('id' => $cm->id, 'sort' => 'firstname')
					),
					get_string('sortfirstname', 'elang')
				),
				html_writer::link(
					new moodle_url(
						'/mod/elang/view.php',
						array('id' => $cm->id, 'sort' => 'lastname')
					),
					get_string('sortlastname', 'elang')
				)
			),
			html_writer::link(
				new moodle_url(
					'/mod/elang/view.php',
					array('id' => $cm->id, 'sort' => 'email')
				),
				get_string('email', 'elang')
			),
			get_string('studentsuccess', 'elang'),
			get_string('studenthelp', 'elang'),
			get_string('studenterror', 'elang'),
			get_string('studentremaining', 'elang')
		);

		// Get the page parameter
		$page = optional_param('page', 0, PARAM_INT);

		// Get the sort parameter
		$sort = optional_param('sort', $cache->has('sort') ? $cache->get('sort') : 'lastname', PARAM_ALPHA);
		$cache->set('sort', $sort);

		// Get the enrolled users
		$users = get_enrolled_users(
			$context,
			'mod/elang:isinreport',
			$id_group,
			'u.id, u.firstname, u.lastname, u.email',
			$sort,
			$page * $perpage,
			$perpage
		);

		// Prepare data for the table
		$table->data = array();

		// Get answers from the users
		if (!empty($users))
		{
			if (empty($perpage))
			{
				$answers = $DB->get_records('elang_users', array('id_elang' => (int) $cm->instance));
			}
			else
			{
				$answers = $DB->get_records_sql(
					'SELECT * FROM {elang_users} WHERE id_elang =' . ((int) $cm->instance) .
					' AND id_user IN (' . implode(',', array_keys($users)) . ')'
				);
			}
		}
		else
		{
			$answers = array();
		}

		// Prepare the users
		foreach ($users as $user)
		{
			$user->success = 0;
			$user->error = 0;
			$user->help = 0;
		}

		// Process the answers
		foreach ($answers as $answer)
		{
			if (isset($users[$answer->id_user]))
			{
				$json = json_decode($answer->json, true);

				foreach ($json as $n => $data)
				{
					if ($data['help'])
					{
						$users[$answer->id_user]->help++;
					}
					elseif ($data['content'] == $solutions[$answer->id_cue]->json[$n])
					{
						$users[$answer->id_user]->success++;
					}
					elseif ($data['content'] != '')
					{
						$users[$answer->id_user]->error++;
					}
				}
			}
		}

		// Add the users to the table
		foreach ($users as $user)
		{
			// For each user add data
			$table->data[] = array(
				html_writer::link(
					new moodle_url(
						'/mod/elang/view.php',
						array('id' => $cm->id, 'id_user' => $user->id)
					),
					sprintf(get_string('studentformatname', 'elang'), $user->firstname, $user->lastname)
				),
				$user->email,
				$user->success,
				$user->help,
				$user->error,
				$count - $user->success - $user->help - $user->error
			);
		}

		// Define alignments
		$table->align = array(null, null, 'right', 'right', 'right', 'right');

		// Output the table
		echo html_writer::table($table);

		if ($perpage > 0)
		{
			// Output the pagination
			echo $OUTPUT->paging_bar(
				count_enrolled_users($context, 'mod/elang:isinreport', $id_group),
				$page,
				$perpage,
				new moodle_url('/mod/elang/view.php', array('id' => $cm->id))
			);
		}

		// Displays the form
		$mform->display();

		// Finish the page.
		echo $OUTPUT->footer();
	}

	die;
}
