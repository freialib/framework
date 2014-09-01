<?php namespace hlin\tools\tests;

$srcpath = realpath(__DIR__.'/../src');
require "$srcpath/Time.php";

class TimeTest extends \PHPUnit_Framework_TestCase {

	/** @test */ function
	timezoneOffset() {
		$offset = \hlin\tools\Time::timezoneOffset('UTC');
		$this->assertEquals('+00:00', $offset);
		$offset = \hlin\tools\Time::timezoneOffset('Europe/London');
		$this->assertEquals('+01:00', $offset);
	}

} # test
