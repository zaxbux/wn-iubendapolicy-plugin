<?php

namespace Zaxbux\IubendaPolicy\Components;

use Cms\Classes\ComponentBase;
use Zaxbux\IubendaPolicy\Models\Settings;

class IubendaCookieSolution extends ComponentBase {
	public function componentDetails() {
		return [
			'name'        => 'zaxbux.iubendapolicy::lang.components.iubendaCookieSolution.name',
			'description' => 'zaxbux.iubendapolicy::lang.components.iubendaCookieSolution.description',
		];
	}

	public function solutionContent() {
		return Settings::get('cookie_embed', '');
	}
}