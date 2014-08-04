<?php return array
	(
		'metarenderers' => array
			(
				'class' => function ($htmltag) {
					$classes = $htmltag->get('class');

					if (is_array($classes)) {
						return implode(' ', array_unique($classes));
					}
					else { # assume string
						return $classes;
					}
				},

				'style' => function ($htmltag) {
					$styles = $htmltag->get('style');

					if (is_array($styles)) {
						$result = '';
						foreach ($styles as $key => $style) {
							$result .= rtrim($style, ';').'; ';
						}

						return $result;
					}
					else { # non array
						return $styles;
					}
				},
			),
	);
