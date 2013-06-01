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
?>
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php echo format_string($elang->name); ?></title>
	
		<link rel="shortcut icon" href="assets/favicon.ico"/>
		<!-- -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf8"/>
		<meta name="apple-mobile-web-app-capable" content="yes"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
		
		<!-- enyo (debug) -->
		<script src="view/enyo/enyo.js"></script>

		<!-- Bootstrap -->
		<link href="view/lib/bootstrap/css/bootstrap-responsive.css" rel="stylesheet">
		<script src="view/lib/jquery/jquery.min.js"></script>
        <link rel="stylesheet" type="text/css" href="view/lib/bootstrap/css/bootstrap.css"/>
        <script src="view/lib/bootstrap/js/bootstrap-tooltip.js" type="text/javascript"></script>
        <script src="view/lib/bootstrap/js/bootstrap-modal.js" type="text/javascript"></script>
		
		<!-- Include Captionator -->
		
		<script type="text/javascript" src="view/lib/captionator/js/captionator.js"></script>
		<script type="text/javascript" src="view/lib/captionator/js/captionator-example-api.js"></script>
		<script type="text/javascript">
			
		</script>
		<!-- Video -->
		<!--<link href="assets/css/video-default.css" rel="stylesheet">
		<script type="text/javascript" src="assets/js/jquery-1.8.1.min.js"></script>
		<script type="text/javascript" src="assets/js/jquery.video-ui.js"></script>-->

		<!-- application (debug) -->
		<script src="view/source/package.js" type="text/javascript"></script>

	</head>
	<body onload="captionator.captionify();
		document.getElementById(app.$.video.getAttribute('id')).addEventListener('timeupdate',function(e){app.$.video.handleTimeUpdate();},false);
		document.getElementById(app.$.video.getAttribute('id')).addEventListener('play',function(e){app.$.video.play();},false);
		">
	<!-- TODO : set the envent listener in Enyo kinds -->
		<script>
			var app=new App().renderInto(document.body);
		</script>
	</body>
</html>
