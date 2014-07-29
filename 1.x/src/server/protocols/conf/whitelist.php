<?php namespace hlin; return array
	(
		'+anybody' => [
			Check::entities(['access:site'])
				->unrestricted(),
		],

	); # conf
