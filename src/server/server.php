<?php
// This file is part of mod_elang for moodle.
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Server for ajax request of elang
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package     mod_elang
 *
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       0.0.1
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__) . '/locallib.php');

$task = optional_param('task', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

// Detect if there is no course module id.
if ($id == 0) {
    header('HTTP/1.1 400 Bad Request');
    die;
}

// Get the course module, the elang instance and the context.
$cm = get_coursemodule_from_id('elang', $id, 0, false);

// Detect if the course module exists.
if (!$cm) {
    header('HTTP/1.1 404 Not Found');
    die;
}

// Detect if the user is logged in.
if (!isloggedin()) {
    header('HTTP/1.1 401 Unauthorized');
    die;
}

// Get the context.
$context = context_module::instance($cm->id);

// Detect if the user has the capability to view this course module.
if (!has_capability('mod/elang:view', $context)) {
    header('HTTP/1.1 403 Forbidden');
    die;
}

// Get the elang instance and the course.
$course = $DB->get_record('course', array('id' => $cm->course), '*');
$elang = $DB->get_record('elang', array('id' => $cm->instance), '*');

// Detect an internal server error.
if (!$course || !$elang) {
    header('HTTP/1.1 500 Internal Server Error');
    die;
}

// Verify the login.
require_login($course, true, $cm);

// Get the moodle version.
$version = moodle_major_version(true);

$options = json_decode($elang->options, true);
$repeatedunderscore = isset($options['repeatedunderscore']) ? $options['repeatedunderscore'] : 10;

switch ($task) {
    // Get the data for preparing the exercise.
    case 'data':
        $fs = get_file_storage();

        // Get the video files.
        $files = $fs->get_area_files($context->id, 'mod_elang', 'videos', 0);
        $sources = array();

        foreach ($files as $file) {
            if ($file->get_source()) {
                $sources[] = array(
                    'src' => (string) moodle_url::make_pluginfile_url(
                        $file->get_contextid(),
                        $file->get_component(),
                        $file->get_filearea(),
                        null,
                        $file->get_filepath(),
                        $file->get_filename()
                    ),
                    'type' => $file->get_mimetype()
                );
            }
        }

        // Get the poster file.
        $files = $fs->get_area_files($context->id, 'mod_elang', 'poster', 0);
        $poster = '';

        foreach ($files as $file) {
            if ($file->get_source()) {
                $poster = (string) moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    null,
                    $file->get_filepath(),
                    $file->get_filename()
                );
                break;
            }
        }

        // Get the subtitle and the pdf file.
        $files = $fs->get_area_files($context->id, 'mod_elang', 'subtitle', 0);
        $subtitle = '';

        foreach ($files as $file) {
            if ($file->get_source()) {
                $pathparts = pathinfo($file->get_filename());
                $subtitle = (string) moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    $file->get_filearea(),
                    null,
                    $file->get_filepath(),
                    $pathparts['filename'] . '.vtt'
                );
                $pdf = (string) moodle_url::make_pluginfile_url(
                    $file->get_contextid(),
                    $file->get_component(),
                    'pdf',
                    null,
                    $file->get_filepath(),
                    $pathparts['filename'] . '.pdf'
                );
                break;
            }
        }

        $cues = array();
        $i = 0;
        $records = $DB->get_records('elang_cues', array('id_elang' => $elang->id), 'begin ASC');
        $users = $DB->get_records('elang_users', array('id_elang' => $elang->id, 'id_user' => $USER->id), '', 'id_cue,json');

        $total = 0;
        $error = 0;
        $success = 0;
        $help = 0;

        // Get the cues.
        foreach ($records as $id => $record) {
            $cuetotal = 0;
            $cueerror = 0;
            $cuesuccess = 0;
            $cuehelp = 0;

            $data = json_decode($record->json, true);

            if (isset($users[$id])) {
                $user = json_decode($users[$id]->json, true);
            } else {
                $user = array();
            }

            $elements = array();

            foreach ($data as $number => $element) {
                if ($element['type'] == 'input') {
                    $total++;
                    $cuetotal++;

                    if (isset($user[$number])) {
                        if ($user[$number]['help']) {
                            $help++;
                            $cuehelp++;
                            $elements[] = array(
                                'type' => 'help',
                                'content' => $element['content']
                            );
                        } else if (empty($user[$number]['content'])) {
                            $elements[] = array(
                                'type' => 'input',
                                'size' => ((int) (mb_strlen($element['content'], 'UTF-8') - 1) / 10 + 1) * 10,
                                'content' => '',
                                'help' => $element['help'],
                                'link' => isset($element['link']) ? $element['link'] : null
                            );
                        } else if ($user[$number]['content'] == $element['content']) {
                            $success++;
                            $cuesuccess++;
                            $elements[] = array(
                                'type' => 'success',
                                'content' => $element['content']
                            );
                        } else {
                            $error++;
                            $cueerror++;
                            $elements[] = array(
                                'type' => 'input',
                                'size' => ((int) (mb_strlen($element['content'], 'UTF-8') - 1) / 10 + 1) * 10,
                                'content' => $user[$number]['content'],
                                'help' => $element['help'],
                                'link' => isset($element['link']) ? $element['link'] : null
                            );
                        }
                    } else {
                        $elements[] = array(
                            'type' => 'input',
                            'size' => ((int) (mb_strlen($element['content'], 'UTF-8') - 1) / 10 + 1) * 10,
                            'content' => '',
                            'help' => $element['help'],
                            'link' => isset($element['link']) ? $element['link'] : null
                        );
                    }
                } else {
                    $elements[] = array(
                        'type' => 'text',
                        'content' => $element['content']
                    );
                }
            }

            $cues[] = array(
                'number' => $i++,
                'id' => $record->id,
                'title' => $record->title,
                'begin' => $record->begin / 1000,
                'end' => $record->end / 1000,
                'elements' => $elements,
                'remaining' => $cuetotal - $cuesuccess - $cuehelp - $cueerror,
                'error' => $cueerror,
                'help' => $cuehelp,
                'success' => $cuesuccess,
            );
        }

        $limit = isset($options['limit']) ? $options['limit'] : 10;

        // Send the data.
        Elang\send_response(
            array(
                'title' => $elang->name,
                'description' => $elang->intro,
                'total' => $total,
                'success' => $success,
                'error' => $error,
                'help' => $help,
                'cues' => $cues,
                'limit' => $limit,
                'sources' => $sources,
                'poster' => $poster,
                'track' => $subtitle,
                'pdf' => $pdf,
                'language' => $elang->language
            )
        );
        break;

    case 'check':
        // Get the cue id.
        $idcue = optional_param('id_cue', 0, PARAM_INT);

        // Get the cue record.
        $cue = $DB->get_record('elang_cues', array('id' => $idcue), '*');

        // Detect an error.
        if (!$cue) {
            header('HTTP/1.1 404 Not Found');
            die;
        }

        if ($cue->id_elang != $elang->id) {
            header('HTTP/1.1 400 Bad Request');
            die;
        }

        // Get the input number.
        $number = optional_param('number', 0, PARAM_INT);

        // Get the elements of the cue.
        $elements = json_decode($cue->json, true);

        // Detect an error.
        if (!isset($elements[$number])) {
            header('HTTP/1.1 500 Internal Server Error');
            die;
        }

        $user = $DB->get_record('elang_users', array('id_cue' => $idcue, 'id_user' => $USER->id));

        if ($user) {
            $data = json_decode($user->json, true);

            if (isset($data[$number]['help']) && $data[$number]['help']) {
                // Help has been already asked.
                header('HTTP/1.1 400 Bad Request');
                die;
            }
        }

        $text = preg_replace(array('/^\s*/', '/\s*$/', '/\s+/'), array('', '', ' '), optional_param('text', '', PARAM_TEXT));

        // Compare strings ignoring case.
        if (false == $options['usecasesensitive']) {
            $parsedtext = mb_strtolower($text, 'UTF-8');
            $parsedcontent = mb_strtolower($elements[$number]['content'], 'UTF-8');
        } else {
            $parsedtext = $text;
            $parsedcontent = $elements[$number]['content'];
        }

        $previouslocale = setlocale(LC_ALL, 0);

        // Use the locales associated to the language tag.
        if (false === setlocale(LC_ALL, Elang\get_locale($elang->language))) {
            setlocale(LC_ALL, $previouslocale);
        }

        if ($parsedtext == $parsedcontent
            || $options['usetransliteration']
            && @iconv('UTF-8', 'ASCII//TRANSLIT', $parsedtext) == @iconv('UTF-8', 'ASCII//TRANSLIT', $parsedcontent)
            || Elang\jaro($parsedtext, $parsedcontent) >= $options['jaroDistance']) {
            $text = $elements[$number]['content'];
        }

        setlocale(LC_ALL, $previouslocale);

        // Log action.
        if (!empty($text)) {
            $idcheck = $DB->insert_record(
                'elang_check',
                array(
                    'id_elang' => $elang->id,
                    'cue' => $cue->number,
                    'guess' => $elements[$number]['order'],
                    'info' => $elements[$number]['content'],
                    'user' => $text,
                )
            );

            if (version_compare($version, '2.7') < 0) {
                add_to_log($course->id, 'elang', 'add check', 'view.php?id=' . $cm->id, $idcheck, $cm->id);
            } else {
                $event = \mod_elang\event\check_added::create(
                    array(
                        'objectid' => $idcheck,
                        'context' => $context,
                        'courseid' => $course->id,
                        'other' => array(
                            'cue' => $cue->number,
                            'guess' => $elements[$number]['order'],
                            'info' => $elements[$number]['content'],
                            'user' => $text
                        )
                    )
                );
                $event->trigger();
            }
        }

        if ($user) {
            $data[$number] = array('help' => false, 'content' => $text);
            $user->json = json_encode($data);
            $DB->update_record('elang_users', $user);
        } else {
            $data = array($number => array('help' => false, 'content' => $text));
            $DB->insert_record(
                'elang_users',
                array(
                    'id_cue' => $idcue,
                    'id_elang' => $elang->id,
                    'id_user' => $USER->id,
                    'json' => json_encode($data),
                )
            );
        }

        // Check completion.
        $completion = new completion_info($course);

        if ($completion->is_enabled($cm)) {
            $completion->update_state($cm, COMPLETION_COMPLETE);
        }

        // Send the response.
        $cuetext = Elang\generate_cue_text($elements, $data, '-', $repeatedunderscore);

        if ($elements[$number]['content'] == $text) {
            Elang\send_response(array('status' => 'success', 'cue' => $cuetext, 'content' => $text));
        } else {
            Elang\send_response(array('status' => 'failure', 'cue' => $cuetext));
        }

        break;

    case 'help':
        // Get the cue id.
        $idcue = optional_param('id_cue', 0, PARAM_INT);

        // Get the cue record.
        $cue = $DB->get_record('elang_cues', array('id' => $idcue), '*');

        // Detect error.

        if (!$cue) {
            header('HTTP/1.1 404 Not Found');
            die;
        }

        if ($cue->id_elang != $elang->id) {
            header('HTTP/1.1 400 Bad Request');
            die;
        }

        // Get the input number.
        $number = optional_param('number', 0, PARAM_INT);

        // Get the elements of the cue.
        $elements = json_decode($cue->json, true);

        // Detect an error.
        if (!isset($elements[$number])) {
            header('HTTP/1.1 500 Internal Server Error');
            die;
        }

        // Detect a forbidden help.
        if (!$elements[$number]['help']) {
            header('HTTP/1.1 403 Forbidden');
            die;
        }

        $user = $DB->get_record('elang_users', array('id_cue' => $idcue, 'id_user' => $USER->id));

        if ($user) {
            $data = json_decode($user->json, true);

            if (isset($data[$number]['help']) && $data[$number]['help']) {
                // Help has been already asked.
                header('HTTP/1.1 400 Bad Request');
                die;
            }
        }

        // Log action.
        $idhelp = $DB->insert_record(
            'elang_help',
            array(
                'id_elang' => $elang->id,
                'cue' => $cue->number,
                'guess' => $elements[$number]['order'],
                'info' => $elements[$number]['content'],
            )
        );

        if (version_compare($version, '2.7') < 0) {
            add_to_log($course->id, 'elang', 'view help', 'view.php?id=' . $cm->id, $idhelp, $cm->id);
        } else {
            $event = \mod_elang\event\help_viewed::create(
                array(
                    'objectid' => $idhelp,
                    'context' => $context,
                    'courseid' => $course->id,
                    'other' => array(
                        'cue' => $cue->number,
                        'guess' => $elements[$number]['order'],
                        'info' => $elements[$number]['content'],
                    )
                )
            );
            $event->trigger();
        }

        if ($user) {
            $data[$number] = array('help' => true, 'content' => '');
            $user->json = json_encode($data);
            $DB->update_record('elang_users', $user);
        } else {
            $data = array($number => array('help' => true, 'content' => ''));
            $DB->insert_record(
                'elang_users',
                array(
                    'id_cue' => $idcue,
                    'id_elang' => $elang->id,
                    'id_user' => $USER->id,
                    'json' => json_encode($data),
                )
            );
        }

        // Check completion.
        $completion = new completion_info($course);

        if ($completion->is_enabled($cm)) {
            $completion->update_state($cm, COMPLETION_COMPLETE);
        }

        // Send the response.
        $cuetext = Elang\generate_cue_text($elements, $data, '-', $repeatedunderscore);
        Elang\send_response(array('cue' => $cuetext, 'content' => $elements[$number]['content']));

        break;

    default:
        header('HTTP/1.1 400 Bad Request');
        die;
        break;
}
