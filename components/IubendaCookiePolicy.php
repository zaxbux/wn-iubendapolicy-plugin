<?php

namespace Wackywired135\IubendaPolicyCacher\Components;

use Cms\Classes\ComponentBase;
use Wackywired135\IubendaPolicyCacher\Classes\IubendaCache;

class IubendaCookiePolicy extends ComponentBase {
	public function componentDetails() {
		return [
			'name' => 'wackywired135.iubendapolicycacher::lang.components.iubendaCookiePolicy.name',
			'description' => 'wackywired135.iubendapolicycacher::lang.components.iubendaCookiePolicy.description'
		];
	}

	public function policyContent() {
		$cache = new IubendaCache;

		return $cache->getCookiePolicy();
	}
}