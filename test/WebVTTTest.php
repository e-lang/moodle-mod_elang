<?php
require __DIR__ . '/../src/parseWebVTT.php';
/**
 * Test parseWebVTT class
 *
 * @package    mod
 * @subpackage elang
 * @copyright  2013 University of La Rochelle, France
 * @license    http://www.cecill.info/licences/Licence_CeCILL-B_V1-en.html CeCILL-B license
 */

/**
 * Test class for parseWebVTT.php
 */
class WebVTTTest extends PHPUnit_Framework_TestCase
{
	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
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
	 * @since   1.0
	 */
	public function casesParseVtt()
	{
		return array(
			// title checking
			array(
				'titre',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City',
				'titre',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City'
			),
			// title checking for null title 
			array(
				null,
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City',
				'',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City'
			),
			// test to split the time with format 00:00:03.500
			array(
				null,
				'00:00:06.000 --> 00:00:09.000 align:start size:50%',
				'<v Roger Bingham>We are in New York City',
				'',
				'00:00:06.000 --> 00:00:09.000',
				'<v Roger Bingham>We are in New York City'
			),
			// test to split the time with format 00:03.500
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
	 * @return  void
	 *
	 * @dataProvider  casesParseVtt
	 *
	 * @since   1.0
	 */
	public function testParseVtt($title, $duration, $text, $expected_title, $expected_duration, $expected_text)
	{
		$str = "WEBVTT\n\n" . ($title ? $title . " \n" : '') . $duration . "\n" . $text . "\n";
		$wev = new parsewebvtt\WebVTT($str);
		//Test title equals
		$this->assertEquals (
			$wev->current()->getTitle(),
			$expected_title
		);
		//Test duration (string) equals
		$time = parsewebvtt\Cue::formatMSString($wev->current()->getbegin()).' --> '.parsewebvtt\Cue::formatMSString($wev->current()->getEnd());
		$this->assertEquals (
			$time,
			$expected_duration
		);
		//Test Text equals
		$this->assertEquals (
			$wev->current()->getText(),
			$expected_text
		);
				
	}
	
	/**
	 * Provider  for testToString
	 *
	 * @return  array
	 *
	 * @since   1.0
	 */
	public function casesToString()
	{
		return array(
			//test toString method
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
	 * @return  void
	 *
	 * @dataProvider  casesToString
	 *
	 * @since   1.0
	 */
	public function testToString($title, $duration, $text, $expected_title, $expected_duration, $expected_text)
	{
		//simulaiton de fichier vtt
		$str = "WEBVTT\n\n" . ($title ? $title . " \n" : '') . $duration . "\n" . $text . "\n";
		$webvtt = new parsewebvtt\WebVTT($str);
		$this->assertEquals (
			$webvtt->current()->__toString(),
			$expected_title."\n". $expected_duration."\n".$expected_text."\n"
		);
		$this->assertEquals (
			$webvtt->__toString(),
			"WEBVTT\n\n" . $expected_title."\n". $expected_duration."\n".$expected_text."\n\n"
		);
				
	}
	
	/**
	* Test the addCue method and the getCueList method
	*
	* @return void
	*
	* @since 1.0
	*/
	public function testAddCue()
	{
		$cue1 = new parsewebvtt\Cue();
		$cue1->setTitle("title1");
		$cue1->setBegin("3500");
		$cue1->setEnd("4000");
		$cue1->setText("This is the teext of Cue 1");
		
		$cue2 = new parsewebvtt\Cue();
		$cue2->setTitle("title2");
		$cue2->setBegin("4001");
		$cue2->setEnd("4500");
		$cue2->setText("This is the teext of Cue 2");
		
		$webVtt = new parsewebvtt\WebVTT;
		$webVtt->addCue($cue1);
		$webVtt->addCue($cue2);
		
		$cueList = $webVtt->getCueList();
		$cue1Copy = $cueList[0];
		$cue2Copy = $cueList[1];
		
		//check if getCueList method return the right object
		$this->assertEquals (
			$cue1,
			$cue1Copy
		);
		$this->assertEquals (
			$cue2,
			$cue2Copy
		);
	}
}

