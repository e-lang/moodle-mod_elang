<?php

/**
 * Test the version file
 *
 * @package    mod
 * @subpackage elang
 * @copyright  2013 University of La Rochelle, France
 * @license    http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

/**
 * Test class for version.php
 */
class VersionTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$module = new stdClass;
		$this->object = $module;
		require __DIR__ . '/../src/version.php';
	}

	/**
	 * Tests the module variable
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testModule()
	{
		$this->assertEquals(
			$this->object->component,
			'mod_elang',
			'Module name must be equal to "mod_elang".'
		);
	}
}

