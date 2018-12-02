<?php

namespace Zaxbux\IubendaPolicy;

use System\Classes\PluginBase;
use Zaxbux\IubendaPolicy\Models\Settings;
use Zaxbux\IubendaPolicy\Classes\PolicyCache;

/**
 * Plugin class for Iubenda Policy
 * @author zaxbux
 */
class Plugin extends PluginBase {

	/**
	 * @{inheritDoc}
	 */
	public function pluginDetails() {
		return [
			'name'        => 'zaxbux.iubendaprivacypolicy::lang.plugin.name',
			'description' => 'zaxbux.iubendapolicycacher::lang.plugin.description',
			'author'      => 'zaxbux',
			'icon'        => 'oc-icon-shield',
			'homepage'    => 'https://www.zacharyschneider.ca/'
		];
	}

	/**
	 * @{inheritDoc}
	 */
	public function registerComponents() {
		return [
			'Zaxbux\IubendaPolicy\Components\IubendaPrivacyPolicy' => 'iubendaPrivacyPolicy',
			'Zaxbux\IubendaPolicy\Components\IubendaCookiePolicy'  => 'iubendaCookiePolicy',
		];
	}

	/**
	 * @{inheritDoc}
	 */
	public function registerPermissions() {
		return [
			'zaxbux.iubendaprivacypolicy.access_settings' => [
				'tab'   => 'zaxbux.iubendaprivacypolicy::lang.permissions.tab',
				'label' => 'zaxbux.iubendaprivacypolicy::lang.permissions.label'
			]
		];
	}

	/**
	 * @{inheritDoc}
	 */
  public function registerSettings() {
		return [
			'config' => [
				'label'       => 'Iubenda Policy',
				'icon'        => 'icon-shield',
				'description' => 'Manage your Iubenda policy configuration.',
				'class'       => 'Zaxbux\IubendaPolicy\Models\Settings',
				'order'       => '600',
				'permissions' => ['zaxbux.iubendaprivacypolicy.access_settings'],
			]
		];
	}
	
	/**
	 * @{inheritDoc}
	 */
	public function registerSchedule($schedule) {
		// Download and cache the policy on a daily basis
		$schedule->call(function() {
			$cache = new PolicyCache();
			$cache->update();
		})->daily();
	}

	/**
	 * @{inheritDoc}
	 */
	public function boot() {
		// Include Guzzle
		set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/vendor/guzzlehttp/guzzle/src');

		// Clear the policy cache when settings are saved
		// This takes care of policy ID changes, and allows the policy cache to be cleared on demand
		Settings::extend(function($model) {
			$model->bindEvent('model.beforeSave', function () {
				$cache = new PolicyCache();
				$cache->forget();
			});

			$model->bindEvent('model.afterSave', function () {
				$cache = new PolicyCache();
				$cache->update();
			});
		});
	}
}

