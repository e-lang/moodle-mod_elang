<?php
/**
 * Test locallib functions
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013-2015 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

require __DIR__ . '/../src/server/locallib.php';

/**
 * Test class for parseWebVTT.php
 *
 * @since  1.3.0
 */
class LocalLibTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   1.3.0
	 */
	protected function setUp()
	{
		$module = new stdClass;
		$this->object = $module;
	}

	/**
	 * Provider  fot testJaro
	 *
	 * @return  array
	 *
	 * @since   1.3.0
	 */
	public function casesJaro()
	{
		return array(
			array(
				'MARTHA',
				'MARHTA',
				2,
				0.94
			),
			array(
				'DWAYNE',
				'DUANE',
				2,
				0.82
			),
		);
	}

	/**
	 * Test the jaro method
	 *
	 * @param   string   $str1       String 1
	 * @param   string   $str2       String 2
	 * @param   integer  $precision  Precision
	 * @param   float    $jaro       Jaro's distance
	 *
	 * @return  void
	 *
	 * @dataProvider  casesJaro
	 *
	 * @since   1.3.0
	 */
	public function testJaro($str1, $str2, $precision, $jaro)
	{
		// Test Jaro's distance
		$this->assertEquals(round(Elang\jaro($str1, $str2), $precision), $jaro);
	}
}
