<?php namespace ran\tools;

/**
 * @copyright (c) 2014, freia Team
 * @license BSD-2 <http://freialib.github.io/license.txt>
 * @package freia Library
 */
class Temp {

	/**
	 * Overwrite if you need to replace or enhance the configuration.
	 *
	 * @return array
	 */
	protected static function conf() {
		return [
			'given_names' => array
				(
					//// male

					// UK
					'Oliver', 'Jack', 'Harry', 'Alfie', 'Charlie', 'Thomas', 'William', 'Joshua', 'George', 'James',
					// France
					'Nathan', 'Lucas', 'Jules', 'Enzo', 'Gabriel', 'Louis', 'Arthur', 'Raphaël', 'Mathis', 'Ethan',
					// Germany
					'Ben', 'Leon', 'Lukas', 'Fynn', 'Jonas', 'Maximilian', 'Paul', 'Felix', 'Luka',
					// Italy
					'Francesco', 'Alessandro', 'Andrea', 'Lorenzo', 'Matteo', 'Mattia', 'Gabriele', 'Riccardo', 'Davide', 'Leonardo',

					//// female

					// UK
					'Olivia', 'Sophie', 'Emily', 'Lily', 'Amelia', 'Jessica', 'Ruby', 'Chloe', 'Grace', 'Evie',
					// France
					'Olivia', 'Sophie', 'Emily', 'Amelia', 'Jessica', 'Ruby', 'Chloe',
					// Germany
					'Mia', 'Emma', 'Hannah', 'Anna', 'Leah', 'Lina', 'Marie', 'Sophia', 'Lena',
					// Italy
					'Giulia', 'Sara', 'Martina', 'Giorgia', 'Chiara', 'Aurora', 'Alice', 'Emma', 'Alessia',
				),

			'family_names' => array
				(
					// Irish
					'Murphy', 'Kelly', 'O\'Sullivan', 'Breathnach', 'Mac Gabhann', 'Ó Briain', 'Ó Néill', 'Mac Cárthaigh', 'Lynch',
					// UK
					'Brown', 'Smith', 'Patel', 'Roberts', 'Khan', 'Cox', 'Davis', 'Clarke', 'Hall', 'Thompson', 'Jenkins', 'Griffiths', 'Morgan', 'Evans', 'Hughes',
					// France
					'Dubois', 'Durand', 'Bernard', 'Martin', 'Lefebvre', 'Leroy', 'Laurent', 'Lefèvre', 'André', 'Roux', 'Vincent', 'Fournier', 'Moreau',
					// Germany
					'Müller', 'Schmidt', 'Schneider', 'Fischer', 'Weber', 'Meyer', 'Schulz', 'Wagner', 'Becker', 'Hoffmann',
					// Italy
					'Rossi', 'Russo', 'Ferrari', 'Bianchi', 'Ricci', 'Bruno', 'Bruno', 'Rizzo', 'Moretti', 'Santoro',
				),

			'punctuation' => array
				(
					'.', '?', '.', '!', '.', ';'
				),

			'words' => array
				(
					'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit', 'quisque', 'vitae', 'metus',
					'non', 'leo', 'rutrum', 'interdum', 'eget', 'tincidunt', 'erat', 'morbi', 'id', 'lorem', 'id', 'turpis',
					'blandit', 'gravida', 'et', 'vitae', 'neque', 'nam', 'pretium', 'tempus', 'mauris', 'in', 'porta', 'orci',
					'dictum', 'vitae', 'mauris', 'orci', 'tortor', 'aliquam', 'sit', 'amet', 'iaculis', 'ut', 'mattis', 'in',
					'augue', 'quisque', 'purus', 'nunc', 'egestas', 'quis', 'fringilla', 'ac', 'ornare', 'eget', 'est', 'vestibulum',
					'ante', 'ipsum', 'primis', 'in', 'faucibus', 'orci', 'luctus', 'et', 'ultrices', 'posuere', 'cubilia', 'curae',
					'mauris', 'leo', 'orci', 'pulvinar', 'sit', 'amet', 'imperdiet', 'vitae', 'laoreet', 'vel', 'augue', 'phasellus',
					'id', 'varius', 'nulla', 'nulla', 'vitae', 'magna', 'elit', 'vestibulum', 'vitae', 'ullamcorper', 'elit', 'sed',
					'eu', 'enim', 'sem', 'at', 'mattis'
				),

			'cities' => array
				(
					'New York', 'Los Angeles', 'Chicago', 'Houston', 'Philadelphia',
					'Phoenix', 'San Antonio', 'San Diego', 'Dallas', 'San Jose',
					'Jacksoville', 'Indianapolis', 'Austin', 'San Francisco',
					'Columbus', 'Fort Worth', 'Charlotte', 'Detroit', 'El Paso',
					'Memphis', 'Boston', 'Seattle', 'Denver', 'Baltimore',
					'Washington', 'Nashville', 'Louisville', 'Milwaukee',
					'Portland', 'Oklahoma City', 'Las Vegas', 'Albuquerque',
					'Tucson', 'Fresno', 'Sacramento', 'Long Beach', 'Kansas City',
					'Mesa', 'Virginia Beach', 'Atlanta', 'Colorado Springs',
					'Raleigh', 'Omaha', 'Miami', 'Tulsa', 'Oakland', 'Cleveland',
					'Minneapolis', 'Wichita', 'Arlington', 'New Orleans',
					'Bakersfield', 'Tampa', 'Anaheim', 'Honolulu', 'Aurora',
					'Santa Ana', 'St. Louis', 'Riverside',
				),
		];
	}

	/**
	 * Random time from begining of time to 5 years in the future.
	 *
	 * @return integer
	 */
	static function time() {
		return rand(0, time() + 31557600 * 5);
	}

	/**
	 * @return static
	 */
	static function name() {
		return static::instance('name');
	}

	/**
	 * @return string
	 */
	static function img($width, $height) {
		return "http://placehold.it/{$width}x{$height}";
	}

	/**
	 * @return string kitten!
	 */
	static function placekitten($width, $height, $grayscale = false) {
		return static::instance('placekitten', [
			'width' => $width,
			'height' => $height,
			'grayscale' => $grayscale,
		]);
	}

	/**
	 * @return string zombie~grrr~brainsss
	 */
	static function placezombie($width, $height, $grayscale = false) {
		return static::instance('placezombie', [
			'width' => $width,
			'height' => $height,
			'grayscale' => $grayscale,
		]);
	}

	/**
	 * Similar to Temp::img, only produces real life images.
	 *
	 * Categories: abstract, animals, city, food, nightlife, fashion, people,
	 * nature, sports, technics, transport
	 *
	 * Set category to random to get random categories.
	 *
	 * @return static
	 */
	static function lorempixel($width, $height, $category = null, $grayscale = null) {

		$defaults = [
			'category' => 'technics',
			'grayscale' => true,
		];

		if ($category == null) {
			$category = $defaults['category'];
		}

		if ($grayscale == null) {
			$grayscale = $defaults['grayscale'];
		}

		return static::instance('lorempixel', [
			'width' => $width,
			'height' => $height,
			'category' => $category,
			'grayscale' => $grayscale,
		]);
	}

	/**
	 * @return static
	 */
	static function given_name() {
		return static::instance('given_name');
	}

	/**
	 * @return static
	 */
	static function family_name() {
		return static::instance('family_name');
	}

	/**
	 * @return static
	 */
	static function telephone() {
		return static::instance('telephone');
	}

	/**
	 * @return static
	 */
	static function email() {
		return static::instance('email');
	}

	/**
	 * @return static
	 */
	static function ssn() {
		return static::instance('ssn');
	}

	/**
	 * @return static
	 */
	static function address() {
		return static::instance('address');
	}

	/**
	 * @return static
	 */
	static function city() {
		return static::instance('city');
	}

	/**
	 * @return static
	 */
	static function paragraph() {
		return static::instance('paragraph');
	}

	/**
	 * @return string
	 */
	static function fullurl() {
		$url = (rand(1, 4) === 1 ? '' : 'www.');
		$url .= static::word();
		if (rand(1,2) === 1) {
			$url .= (rand(1, 2) == 1 ? '-' : '').static::word();

			if (rand(1,4) === 1) {
				$url .= (rand(1, 2) == 1 ? '-' : '').static::word();
			}
		}

		$domain = static::rand(['com', 'co.uk', 'co', 'de', 'fr', 'jp', 'kr', 'ro', 'ru', 'eu', 'org']);
		return "$url.$domain/";
	}

	/**
	 * @return static
	 */
	static function counter($id) {
		return static::instance('counter', func_get_args());
	}

	/**
	 * @return static
	 */
	static function title() {
		return static::instance('title');
	}

	/**
	 * @return static
	 */
	static function word() {
		return static::instance('word');
	}

	/**
	 * @return static
	 */
	static function words($count = 10) {
		return static::instance('words', func_get_args());
	}

	/**
	 * @return static
	 */
	static function rand(array $values) {
		$instance = static::instance('rand');
		$instance->args = $values;

		return $instance;
	}

	/**
	 * @return array
	 */
	static function copies($source, $count = null, array $counters = null) {

		$count !== null or $count = rand(-10, 25);

		if ($count < 0) {
			$count = 0;
		}

		$copies = [];
		while ($count-- > 0) {
			// resolve various counter fields (id, views, time, etc)
			if ($counters) {
				foreach ($counters as $counter => &$count_type) {
					if (is_array($count_type)) { # random (viewcount, etc)
						$source[$counter] = rand($count_type[0], $count_type[1]);
					}
					else { # incremental counter (index, etc)
						$source[$counter] = $count_type++;
					}
				}
			}

			$copies[] = $source;
		}

		return $copies;
	}

// ---- Internal --------------------------------------------------------------

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var array
	 */
	protected $args;

	/**
	 * @var array
	 */
	protected static $counters = [];

	/**
	 * @return static
	 */
	static function instance($type = 'paragraph', array $args = null) {
		$i = new static;
		$i->type = $type;
		$i->args = $args;
		return $i;
	}

	/**
	 * @return string
	 */
	function render() {
		$mockup = static::conf();
		switch ($this->type) {
			case 'lorempixel':
				$width = $this->args['width'];
				$height = $this->args['height'];
				$category = $this->args['category'];
				$grayscale = $this->args['grayscale'];

				$url = 'http://lorempixel.com/';

				if ($grayscale) {
					$url .= 'g/';
				}

				$url .= "$width/$height/";

				if ($category != 'random') {
					$rand = rand(1, 10);
					$url .= "$category/$rand/";
				}

				return $url;

			case 'placekitten':
				$width = $this->args['width'];
				$height = $this->args['height'];
				$grayscale = $this->args['grayscale'];

				$url = 'http://placekitten.com/';

				if ($grayscale) {
					$url .= 'g/';
				}

				$rand = rand(1, 16);
				$url .= "$width/$height?image=$rand";

				return $url;

			case 'placezombie':
				$width = $this->args['width'];
				$height = $this->args['height'];
				$grayscale = $this->args['grayscale'];

				$url = 'http://placezombies.com/';

				if ($grayscale) {
					$url .= 'g/';
				}

				$rand = rand(1, 10);
				$url .= "{$width}x{$height}?image=$rand";

				return $url;

			case 'given_name':
				return static::random($mockup['given_names']);

			case 'family_name':
				return static::random($mockup['family_names']);

			case 'name':
				$family_name = static::random($mockup['family_names']);
				$given_name = static::random($mockup['given_names']);
				return "$given_name $family_name";

			case 'title':
				$words = rand(4, 20);
				$title = ucfirst($mockup['words'][rand(1, count($mockup['words']) - 1)]);

				while ($words-- > 0) {
					$title .= ' '.$mockup['words'][rand(1, count($mockup['words']) - 1)];
				}

				return $title;

			case 'word':
				return $mockup['words'][rand(1, count($mockup['words']) - 1)];

			case 'counter':
				$id = $this->args[0];
				// gurantee counter is initialized
				isset(static::$counters[$id]) or static::$counters[$id] = 1;
				return ''.(static::$counters[$id]++);

			case 'words':
				$count = $this->args[0];
				$words = '';
				while ($count-- > 0) {
					$words .= static::random($mockup['words']).' ';
				}

				return rtrim($words, ' ');

			case 'rand':
				$idx = rand(0, count($this->args) - 1);
				return $this->args[$idx];

			case 'telephone':
				return '('.rand(111, 999).') '.rand(111, 999).'-'.rand(1111, 9999);

			case 'city':
				return self::random($mockup['cities']);

			case 'email':
				return strtolower(static::random($mockup['given_names'])).'@'.static::random(array('gmail', 'yahoo', 'hotmail')).'.com';

			case 'ssn':
				$month = rand(1, 12);
				$day = rand(1, 30);
				$sector = rand(1, 52);
				return ''.rand(1, 9).rand(0, 99)
					. ($month < 10 ? '0'.$month : $month)
					. ($day < 10 ? '0'.$day : $day)
					. ($sector < 10 ? '0'.$sector : $sector);

			case 'address':
				return 'Str. '.static::random($mockup['family_names'])
					. ', Nr. '.rand(11, 35)
					. ', Bl. '.rand(51, 956)
					. ', Ap. '.rand(1, 25)
					. ', '.static::random($mockup['cities'])
					;

			case 'paragraph':
			default:
				$sentences = rand(5, 15);
				$paragraph = '';
				while ($sentences-- > 0) {
					$words = rand(4, 20);
					$sentence = ucfirst($mockup['words'][rand(1, count($mockup['words']) - 1)]);

					while ($words-- > 0) {
						$sentence .= ' '.$mockup['words'][rand(1, count($mockup['words']) - 1)];
					}

					$sentence .= $mockup['punctuation'][rand(1, count($mockup['punctuation']) - 1)];
					$sentence .= '  ';
					$paragraph .= $sentence;
				}

				return $paragraph;
		}
	}

	/**
	 * @return string
	 */
	function __toString() {
		try {
			return $this->render();
		}
		catch (\Exception $e) {
			// [!!] __toString can not throw exception in PHP!
			return '[ERROR: '.$e->getMessage().']';
		}
	}

// ---- Private ---------------------------------------------------------------

	/**
	 * @return mixed
	 */
	protected static function random(array $collection) {
		return $collection[rand(0, count($collection) - 1)];
	}

} # class
