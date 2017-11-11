<?php

namespace Wackywired135\IubendaPolicyCacher\Components;

use Cms\Classes\ComponentBase;
use Wackywired135\IubendaPolicyCacher\Classes\IubendaCache;

class IubendaPolicy extends ComponentBase {
	public function componentDetails() {
		return [
			'name' => 'wackywired135.iubendapolicycacher::lang.components.iubendaPolicy.name',
			'description' => 'wackywired135.iubendapolicycacher::lang.components.iubendaPolicy.description'
		];
	}

	public function policyContent() {
		$cache = new IubendaCache;

		return $cache->getPolicy();
	}
}