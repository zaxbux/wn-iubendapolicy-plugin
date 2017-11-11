<?php

namespace Wackywired135\IubendaPolicyCacher\Models;

use October\Rain\Database\Model;

/**
 * Settings model class for Iubenda Policy Cacher
 * @author wackywired135
 */
class Settings extends Model {
	use \October\Rain\Database\Traits\Validation;

	// Setup
	public $implement = ['System.Behaviors.SettingsModel'];
	public $settingsCode = 'wackywired135_iubendapolicycacher_settings';
	public $settingsFields = 'fields.yaml';

	// Validation
	public $rules = [
		'policy_id' => 'required|numeric|digits:6'
	];
	public $customMessages = [
		'policy_id.required' => 'wackywired135.iubendapolicycacher::lang.fields.policy_id.validation.required',
		'policy_id.numeric' => 'wackywired135.iubendapolicycacher::lang.fields.policy_id.validation.numeric',
		'policy_id.digits' => 'wackywired135.iubendapolicycacher::lang.fields.policy_id.validation.digits'
	];

	/*
	 * @{inheritdoc}
	 */
	public function formBeforeSave($model) {
		$model->validate();
	}
}