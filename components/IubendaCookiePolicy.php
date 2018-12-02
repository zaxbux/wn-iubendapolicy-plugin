<?php

namespace Zaxbux\IubendaPolicy\Components;

use Cms\Classes\ComponentBase;
use Zaxbux\IubendaPolicy\Classes\PolicyCache;

class IubendaCookiePolicy extends ComponentBase {
	public function componentDetails() {
		return [
			'name'        => 'zaxbux.iubendapolicy::lang.components.iubendaCookiePolicy.name',
			'description' => 'zaxbux.iubendapolicy::lang.components.iubendaCookiePolicy.description'
		];
	}

	public function policyContent() {
		$cache = new PolicyCache();

		return $cache->getCookiePolicy();
	}
}