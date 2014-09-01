<?php namespace hlin\tools\tests;

$srcpath = realpath(__DIR__.'/../src');
require "$srcpath/PHP.php";

class PHPTest extends \PHPUnit_Framework_TestCase {

	/** @test */ function
	unn() {
		$actual = \hlin\tools\PHP::unn('\\ex\\test\\Example1');
		$this->assertEquals('ex.test.Example1', $actual);
	}

	/** @test */ function
	pnn() {
		$actual = \hlin\tools\PHP::pnn('ex.test.Example1');
		$this->assertEquals('\\ex\\test\\Example1', $actual);
	}

} # test
