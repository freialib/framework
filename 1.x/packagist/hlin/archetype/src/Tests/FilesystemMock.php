<?php namespace hlin\archetype\mocks\tests;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class FilesystemMockTests {

//	use \hlin\archetype\FilesystemTests;

	/**
	 * @before
	 */
	function setup() {

		$fs = \hlin\Mock_Filesystem::instance('appuser', 'appgroup');

		// create a template
		$file1 = $fs->mock_file('file1.txt', "hello, file1", 0770, 'file', 0);
		$file2 = $fs->mock_file('file2.md', "hello, file2");
		$file3 = $fs->mock_file('file3.php');
		$file4 = $fs->mock_file('file4.png');
		$file5 = $fs->mock_file('file5.png');

		$fs->mock_mount
			(
				[ 'api/v1' => [ $fs->mock_file('test.json', '{ "hello": "world" }') ] ],
				'test.site.com:/'
			);

		$fs->mock_mount
			(
				[
					// directories are just string keys, if you specify a path it will
					// get expanded and merged correctly; error in case the definition
					// require overwrites
					'example/path/to' => [
						// files are just array entries with an integer key
						$file1, $file2, $file3,
						'another_dir' => [
							$file1, $file2, $file3, $file4
						]
					],
					'example/path' => [
						'to' => [ $file4, $file5 ],
						'secured' => [
							// dir's can have mode, user, timestamp and group
							'%mode' => 0700,
							'%user' => 'root',
							'%group' => 'www-data',
							'%time' => 0,
							$file1, $file2
						]
					]
				],
				'/home/appuser/appfiles' # where above structure is attached
			);

		$this->fs = $fs;
	}

	/**
	 * @after
	 */
	function teardown() {
		$this->fs = null;
	}

} # tests
