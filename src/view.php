<?php

/**
 * Prints a particular instance of elang
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

 // course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

if ($id)
{
	$cm = get_coursemodule_from_id('elang', $id, 0, false, MUST_EXIST);
	$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
	$elang = $DB->get_record('elang', array('id' => $cm->instance), '*', MUST_EXIST);
}
else
{
    error('You must specify a course_module ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/elang:view', $context);

add_to_log($course->id, 'elang', 'view', 'view.php?id=' . $cm->id, $elang->id, $cm->id);

$languages = elang_get_languages();
$options = json_decode($elang->options, true);
$config = get_config('elang');
if ($options['showlanguage'])
{
	$name = sprintf(get_string('formatname', 'elang'), $elang->name, $languages[$elang->language]);
}
else
{
	$name = $elang->name;
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php echo format_string($name); ?></title>
	
		<link rel="shortcut icon" href="assets/favicon.ico"/>
		<!-- -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf8"/>
		<meta name="apple-mobile-web-app-capable" content="yes"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
		
		<!-- enyo (debug) -->
		<script src="view/enyo/enyo.js" type="text/javascript"></script>

		<!-- Bootstrap -->
		<link href="view/lib/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
		<script src="view/lib/jquery/jquery.min.js" type="text/javascript"></script>
        <link rel="stylesheet" type="text/css" href="view/lib/bootstrap/css/bootstrap.css"/>
        <script src="view/lib/bootstrap/js/bootstrap-tooltip.js" type="text/javascript"></script>
        <script src="view/lib/bootstrap/js/bootstrap-modal.js" type="text/javascript"></script>
		
		<!-- Include Captionator -->
		
		<script type="text/javascript" src="view/lib/captionator/js/captionator.js"></script>
		<script type="text/javascript" src="view/lib/captionator/js/captionator-example-api.js"></script>

		<!-- Video -->
		<!--<link href="assets/css/video-default.css" rel="stylesheet">
		<script type="text/javascript" src="assets/js/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="assets/js/jquery.video-ui.js"></script>-->

		<!-- application (debug) -->
		<script src="view/source/package.js" type="text/javascript"></script>

	</head>
	<body>
		<script>
			var app=new Elang.App(
				<?php echo json_encode(array('url' => (string) new moodle_url('/mod/elang/server.php', array('id' => $cm->id)), 'timeout' => $config->timeout));?>
			).renderInto(document.body).requestData();
		</script>
	</body>
</html>
