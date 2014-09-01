<?php namespace hlin\tools\tests;

$srcpath = realpath(__DIR__.'/../src');
require "$srcpath/Text.php";

class TextTest extends \PHPUnit_Framework_TestCase {

	/** @test */ function
	reindent() {
		$actual = \hlin\tools\Text::reindent
			(
				'SELECT *
					 FROM [table]
					WHERE field = 1
					LIMIT 1
				',
				' -- ',
				4,
				true
			);

		$this->assertEquals
			(
				" -- SELECT *\n --  FROM [table]\n -- WHERE field = 1\n -- LIMIT 1\n -- ",
				$actual
			);
	}

} # test
