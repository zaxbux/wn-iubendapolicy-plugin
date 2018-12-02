<?php

namespace Zaxbux\IubendaPolicy\Components;

use Cms\Classes\ComponentBase;
use Zaxbux\IubendaPolicy\Classes\PolicyCache;

class IubendaPrivacyPolicy extends ComponentBase {
	public function componentDetails() {
		return [
			'name'        => 'zaxbux.iubendapolicy::lang.components.iubendaPrivacyPolicy.name',
			'description' => 'zaxbux.iubendapolicy::lang.components.iubendaPrivacyPolicy.description'
		];
	}

	public function policyContent() {
		$cache = new PolicyCache();

		return $cache->getPrivacyPolicy();
	}
}