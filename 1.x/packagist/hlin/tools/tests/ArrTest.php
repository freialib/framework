<?php namespace hlin\tools\tests;

$srcpath = realpath(__DIR__.'/../src');
require "$srcpath/Arr.php";

class ArrTest extends \PHPUnit_Framework_TestCase {

	/** @test */ function
	join() {

		$list = [
			'a' => 'aaa',
			'b' => 'bbb',
			'x' => 'nnn',
			'c' => 'ccc'
		];

		$actual = \hlin\tools\Arr::join('|', $list, function ($k, $v) {
			return $k == 'x' ? false : $v;
		});

		$this->assertEquals('aaa|bbb|ccc', $actual);
	}

} # test
