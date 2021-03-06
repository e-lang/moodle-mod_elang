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
 * Play the exercise
 *
 * @package     mod_elang
 *
 * @copyright   2013-2018 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       1.1.0
 */

// This file is included from view.php.
defined('MOODLE_INTERNAL') || die();

// Verify access right.
require_capability('mod/elang:view', $context);

// Add a view log.
if (version_compare($version, '2.7') < 0) {
    add_to_log($course->id, 'elang', 'view', 'view.php?id=' . $cm->id, $elang->id, $cm->id);
} else {
    // Log this request.
    $params = array(
        'objectid' => $cm->id,
        'context' => $context
    );
    $event = \mod_elang\event\course_module_viewed::create($params);
    $event->add_record_snapshot('elang', $elang);
    $event->trigger();
}

// Get the options for the exercise.
$options = json_decode($elang->options, true);

// Get the page title.
$title = Elang\generate_title($elang, $options);

// Get the module general settings.
$config = get_config('elang');
?>
<!DOCTYPE html>
<html<?php echo get_html_lang(); ?>>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>

        <title><?php echo format_string($title); ?></title>

        <link rel="shortcut icon" href="pix/icon.ico"/>
        <script>
            var Elang = Elang || {};
            Elang.strings =
<?php
    require(dirname(__FILE__) . '/lang/en/elang.php');
    echo json_encode(get_strings(array_keys($string), 'elang'));
?>;
        </script>
<?php if (file_exists(dirname(__FILE__) . '/build')): ?>
        <link href="build/enyo.css" rel="stylesheet" />
        <link href="build/app.css" rel="stylesheet" />
        <script src="build/enyo.js"></script>
        <script src="build/app.js"></script>
<?php else: ?>
        <script src="enyo/enyo.js" type="text/javascript"></script>
        <script src="source/package.js" type="text/javascript"></script>
<?php endif; ?>

    </head>
    <body>
        <script>
            if (!!document.createElement('video').textTracks)
            {    
                new Elang.App(
<?php
    echo json_encode(
        array(
            'url' => (string) new moodle_url(
                '/mod/elang/server.php',
                array('id' => $cm->id)
            ),
            'timeout' => $config->timeout)
        );
?>
                ).renderInto(document.body).requestData();
            }
            else
            {
                alert($L('notcompatible_with_HTML5'));
            }
        </script>
    </body>
</html>
