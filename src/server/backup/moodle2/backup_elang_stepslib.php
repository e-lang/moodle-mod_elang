<?php

/**
 * Steps for elang backup
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2016 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 *
 * @since       1.1.0
 */

/**
 * Steps to restore an elang activity
 *
 * @since  1.1.0
 */
class backup_elang_activity_structure_step extends backup_activity_structure_step
{
	/**
	 * Used in activity_task
	 *
	 * @return prepared structure of elang for activity task
	 */
	protected function define_structure()
	{
		// To know if we are including userinfo
		$userinfo = $this->get_setting_value('userinfo');

		// Define each element separated
		$elang = new backup_nested_element(
			'elang',
			array('id'),
			array(
				'course',
				'name',
				'intro',
				'introformat',
				'timecreated',
				'timemodified',
				'language',
				'options'
			)
		);
		$cues = new backup_nested_element('cues');
		$cue = new backup_nested_element('cue', array('id'), array('id_elang', 'number', 'begin', 'end', 'title', 'json'));
		$users = new backup_nested_element('users');
		$user = new backup_nested_element('user', array('id'), array('id_user', 'json'));

		// Build the tree
		$elang->add_child($cues);
		$cues->add_child($cue);
		$cue->add_child($users);
		$users->add_child($user);

		// Define sources
		$elang->set_source_table('elang', array('id' => backup::VAR_ACTIVITYID));
		$cue->set_source_table('elang_cues', array('id_elang' => backup::VAR_PARENTID), 'id ASC');

		// Define source for users if userinfo is set
		if ($userinfo)
		{
			$user->set_source_table('elang_users', array('id_cue' => backup::VAR_PARENTID), 'id ASC');
		}

		// Define id annotations
		$user->annotate_ids('user', 'id_user');

		// Define file annotations-These files areas haven't itemid
		$elang->annotate_files('mod_elang', 'videos', null);
		$elang->annotate_files('mod_elang', 'poster', null);
		$elang->annotate_files('mod_elang', 'subtitle', null);

		// Return the root element (elang), wrapped into standard activity structure
		return $this->prepare_activity_structure($elang);
	}
}
