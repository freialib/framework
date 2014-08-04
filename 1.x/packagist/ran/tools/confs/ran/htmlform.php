<?php

	$basictype = function ($type) {
		return function ($form, $confs) use ($type) {
			return \ran\Field::instance($confs)
				->set('tabindex', $form->is_unsigned() ? null : \ran\HTML::tabindex())
				->set('type', $type);
		};
	};

	$numericbasictype = function ($type) {
		return function ($form, $confs) use ($type) {
				return \ran\Field::instance($confs)
					->set('tabindex', $form->is_unsigned() ? null : \ran\HTML::tabindex())
					->set('type', $type)
					->set('placeholder', '#');
			};
	};

return array
	(
		'form.standards' => array
			(
				// no styling, just the field
				'ran:barebone' => function ($form) {
					return $form->addfieldtemplate(':field');
				},

				// similar to barebone only the element will be inlined
				'ran:inline' => function ($form) {
					return $form->apply('ran:barebone')
						->set('style', 'display: inline');
				},

				// legacy twitter 2.x reference name
				'ran:twitter' => function ($form) {
					return $form->apply('ran:twbs2');
				},

				// twitter bootstrap 2.x series style
				'ran:twbs2' => function ($form) {
					return $form
						->adderrorrenderer
							(
								function (array $errors = null) {
									if ($errors) {
										$out = '';
										foreach ($errors as $error) {
											$out .= "<span class=\"help-block\"><span class=\"text-error\">$error</span></span>";
										}

										return $out.'';
									}
									else { # no errors
										return '';
									}
								}
							)
						->addhintrenderer
							(
								function (array $hints = null) {
									if ($hints) {
										$out = '';
										foreach ($hints as $hint) {
											$out .= "<span class=\"help-block\">$hint</span>";
										}

										return $out;
									}
									else { # no hints
										return '';
									}
								}
							)
						->addfieldtemplate
							(
								'<div class="control-group">
									<label class="control-label" for=":id">:label</label>
									<div class="controls">:field :hints :errors</div>
								</div>'
							)
						->addfieldtemplate
							(
								'<div class="control-group">
									<label class="control-label" for=":id">:label</label>
									<div class="controls"><div class="checkbox inline">:field</div> :hints :errors</div>
								</div>',
								'checkbox'
							);
				},

				// twitter 3.x series style
				'ran:twbs3' => function ($form) {
					return $form
						->adderrorrenderer
							(
								function (array $errors = null) {
									if ($errors) {
										$out = '';
										foreach ($errors as $error) {
											$out .= "<span class=\"help-block\"><span class=\"text-error\">$error</span></span>";
										}

										return $out.'';
									}
									else { # no errors
										return '';
									}
								}
							)
						->addhintrenderer
							(
								function (array $hints = null) {
									if ($hints) {
										$out = '';
										foreach ($hints as $hint) {
											$out .= "<span class=\"help-block\">$hint</span>";
										}

										return $out;
									}
									else { # no hints
										return '';
									}
								}
							)
						->addfieldtemplate
							(
								'
									<div class="form-group">
										<label class="col-lg-2 control-label" for=":id">:label</label>
										<div class="col-lg-10">
											:field :hints :errors
										</div>
									</div>
								'
							)
						->addfieldtemplate
							(
								'
									<div class="form-group">
										<label class="col-lg-2 control-label" for=":id">:label</label>
										<div class="col-lg-2">
											:field :hints :errors
										</div>
									</div>
								',
								[
									'date', 'datetime', 'localdatetime'
								]
							)
						->addfieldtemplate
							(
								'
									<div class="form-group">
										<div class="col-lg-offset-2 col-lg-10">
											<div class="checkbox">
												<label>:field :label</label>
											</div>
											:hints :errors
										</div>
									</div>
								',
								'checkbox'
							)
						->addfieldconfigurer
							(
								function ($field) {
									$field->add('class', 'form-control');
								},
								[
									'select',
									'date',
									'text',
									'password',
									'image',
									'search',
									'number',
									'identifier',
									'currency',
									'phonenumber',
									'url',
									'email',
									'month',
									'week',
									'color',
									'range',
									'datetime',
									'localdatetime',
									'textarea',
								]
							);
				},

			),

		'field.standards' => array
			(
				// empty
			),

		'fieldtypes' => array
			(
				'select' => function ($form, $confs) {
						return \ran\SelectField::instance($confs)
							->set('tabindex', $form->is_unsigned() ? null : \ran\HTML::tabindex());
					},

				'checkbox' => function ($form, $confs) {
						return \ran\CheckboxField::instance($confs)
							->set('tabindex', $form->is_unsigned() ? null : \ran\HTML::tabindex());
					},

				'radio' => function ($form, $confs) {
						return \ran\RadioField::instance($confs)
							->set('tabindex', $form->is_unsigned() ? null : \ran\HTML::tabindex());
					},

				'hidden' => function ($form, $confs) {
						return \ran\HiddenField::instance($confs);
					},

				'textarea' => function ($form, $confs) {
						return \ran\TextareaField::instance($confs)
							->set('tabindex', $form->is_unsigned() ? null : \ran\HTML::tabindex());
					},

				'date' => function ($form, $confs) {
						return \ran\DateField::instance($confs)
							->set('tabindex', $form->is_unsigned() ? null : \ran\HTML::tabindex());
					},

				'button'        => $basictype('submit'),
				'submit'        => $basictype('submit'),
				'reset'         => $basictype('reset'),
				'text'          => $basictype('text'),
				'password'      => $basictype('password'),
				'file'          => $basictype('file'),
				'image'         => $basictype('image'),
				'search'        => $basictype('search'),
				'number'        => $numericbasictype('number'),
				'identifier'    => $numericbasictype('text'),
				'currency'      => $basictype('text'),
				'phonenumber'   => $basictype('tel'),
				'url'           => $basictype('url'),
				'email'         => $basictype('email'),
				'month'         => $basictype('month'),
				'week'          => $basictype('week'),
				'color'         => $basictype('color'),
				'range'         => $basictype('range'),
				'datetime'      => $basictype('datetime'),
				'localdatetime' => $basictype('datetime-local'),
			),

	); # conf
