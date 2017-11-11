<?php namespace Wackywired135\IubendaPolicyCacher;

use System\Classes\PluginBase;
use Wackywired135\IubendaPolicyCacher\Models\Settings;
use Wackywired135\IubendaPolicyCacher\Classes\IubendaCache;

/**
 * Plugin class for Iubenda Policy Cacher
 * @author wackywired135
 */
class Plugin extends PluginBase {

	/**
	 * @{inheritDoc}
	 */
	public function pluginDetails() {
		return [
			'name' => 'wackywired135.iubendapolicycacher::lang.plugin.name',
			'description' => 'wackywired135.iubendapolicycacher::lang.plugin.description',
			'author' => 'wackywired135',
			'icon' => 'oc-icon-shield',
			'homepage' => 'https://www.zacharyschneider.ca/projects'
		];
	}

	/**
	 * @{inheritDoc}
	 */
	public function registerComponents() {
		return [
			'Wackywired135\IubendaPolicyCacher\Components\IubendaPolicy' => 'IubendaPolicy',
		];
	}

	/**
	 * @{inheritDoc}
	 */
	public function registerPermissions() {
		return [
			'wackywired135.iubendapolicycacher.access_settings' => [
				'tab'   => 'wackywired135.iubendapolicycacher::lang.permissions.tab',
				'label' => 'wackywired135.iubendapolicycacher::lang.permissions.label'
			]
		];
	}

	/**
	 * @{inheritDoc}
	 */
  public function registerSettings() {
		return [
			'config' => [
				'label' => 'Iubenda Policy Cacher',
				'icon' => 'icon-shield',
				'description' => 'Manage your Iubenda privacy policy configuration.',
				'class' => 'Wackywired135\IubendaPolicyCacher\Models\Settings',
				'order' => '600',
				'permissions' => ['wackywired135.iubendapolicycacher.access_settings'],
			]
		];
	}
	
	/**
	 * @{inheritDoc}
	 */
	public function registerSchedule($schedule) {

		// Download and cache the policy on a daily basis
		$schedule->call(function() {
			$cache = new IubendaCache;
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
		Settings::extend(function($model) {
			$model->bindEvent('model.beforeSave', function() use ($model) {
				$cache = new IubendaCache;
				$cache->forgetPolicy();
			});
		});
	}
}
