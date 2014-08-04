<?php namespace hlin; return [

	'+anybody' => [
		Check::entities(['access:site'])
			->unrestricted(),
	],

]; # conf
