<?php namespace hlin; return [

	Auth::Guest => [
		Check::entities(['access:admin-area'])
			->unrestricted(),
	],

]; # conf
