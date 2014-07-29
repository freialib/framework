<?php namespace hlin; return array
	(
		Auth::Guest => [
			Check::entities(['access:admin-area'])
				->unrestricted(),
		],

	); # conf
