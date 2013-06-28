<?php
/**
 * Test parseWebVTT class
 *
 * @package     mod
 * @subpackage  elang
 * @copyright   2013 University of La Rochelle, France
 * @license     http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

require __DIR__ . '/../src/locallib.php';

/**
 * Test class for parseWebVTT.php
 *
 * @since  0.0.1
 */
class WebVTTTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 *
	 * @since   0.0.1
	 */
	protected function setUp()
	{
		$module = new stdClass;
		$this->object = $module;
	}

	/**
	 * Provider  fot testParseVtt
	 *
	 * @return  array
	 *
	 * @since   0.0.1
	 */
	public function casesParseVtt()
	{
		return array(
			// Title checking
			array(
				'titre',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City',
				'titre',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City'
			),

			// Title checking for null title
			array(
				null,
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City',
				'',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City'
			),

			// Test to split the time with format 00:00:03.500
			array(
				null,
				'00:00:06.000 --> 00:00:09.000 align:start size:50%',
				'<v Roger Bingham>We are in New York City',
				'',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City'
			),

			// Test to split the time with format 00:03.500
			array(
				null,
				'00:06.000 --> 00:09.000 align:start size:50%',
				'<v Roger Bingham>We are in New York City',
				'',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City'
			)
		);
	}

	/**
	 * Test the parse method
	 *
	 * @param   string  $title              Input title
	 * @param   string  $duration           Input duration
	 * @param   string  $text               Input text
	 * @param   string  $expected_title     Expected title
	 * @param   string  $expected_duration  Expected duration
	 * @param   string  $expected_text      Expected text
	 *
	 * @return  void
	 *
	 * @dataProvider  casesParseVtt
	 *
	 * @since   0.0.1
	 */
	public function testParseVtt($title, $duration, $text, $expected_title, $expected_duration, $expected_text)
	{
		$str = "WEBVTT\n\n" . ($title ? $title . " \n" : '') . $duration . "\n" . $text . "\n";
		$wev = new Elang\WebVTT($str);

		// Test title equals
		$this->assertEquals(
			$wev->current()->getTitle(),
			$expected_title
		);

		// Test duration (string) equals
		$time = Elang\Cue::millisecondsToString($wev->current()->getbegin()) . ' --> ' . Elang\Cue::millisecondsToString($wev->current()->getEnd());
		$this->assertEquals(
			$time,
			$expected_duration
		);

		// Test Text equals
		$this->assertEquals(
			$wev->current()->getText(),
			$expected_text
		);
	}

	/**
	 * Provider  for testToString
	 *
	 * @return  array
	 *
	 * @since   0.0.1
	 */
	public function casesToString()
	{
		return array(
			// Test toString method
			array(
				'titre',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City',
				'titre',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City'
			)
		);
	}

	/**
	 * Test the toString methods
	 *
	 * @param   string  $title              Input title
	 * @param   string  $duration           Input duration
	 * @param   string  $text               Input text
	 * @param   string  $expected_title     Expected title
	 * @param   string  $expected_duration  Expected duration
	 * @param   string  $expected_text      Expected text
	 *
	 * @return  void
	 *
	 * @dataProvider  casesToString
	 *
	 * @since   0.0.1
	 */
	public function testToString($title, $duration, $text, $expected_title, $expected_duration, $expected_text)
	{
		// Simulate a vtt file
		$str = "WEBVTT\n\n" . ($title ? $title . " \n" : '') . $duration . "\n" . $text . "\n";
		$webvtt = new Elang\WebVTT($str);
		$this->assertEquals(
			(string) $webvtt->current(),
			$expected_title . "\n" . $expected_duration . "\n" . $expected_text . "\n"
		);
		$this->assertEquals(
			(string) $webvtt,
			"WEBVTT\n\n" . $expected_title . "\n" . $expected_duration . "\n" . $expected_text . "\n\n"
		);
	}

	/**
	* Test the addCue method and the getCueList method
	*
	* @return void
	*
	* @since   0.0.1
	*/
	public function testAddCue()
	{
		$cue1 = new Elang\Cue;
		$cue1->setTitle("title1");
		$cue1->setBegin("3500");
		$cue1->setEnd("4000");
		$cue1->setText("This is the teext of Cue 1");

		$cue2 = new Elang\Cue;
		$cue2->setTitle("title2");
		$cue2->setBegin("4001");
		$cue2->setEnd("4500");
		$cue2->setText("This is the teext of Cue 2");

		$webVtt = new Elang\WebVTT;
		$webVtt->addCue($cue1);
		$webVtt->addCue($cue2);

		$cueList = $webVtt->getCueList();
		$cue1Copy = $cueList[0];
		$cue2Copy = $cueList[1];

		// Check if getCueList method return the right object
		$this->assertEquals(
			$cue1,
			$cue1Copy
		);
		$this->assertEquals(
			$cue2,
			$cue2Copy
		);
	}
}
