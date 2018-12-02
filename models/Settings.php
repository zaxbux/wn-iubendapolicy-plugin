<?php

namespace Zaxbux\IubendaPolicy\Models;

use October\Rain\Database\Model;

/**
 * Settings model class for Iubenda Policy
 * @author zaxbux
 */
class Settings extends Model {
	use \October\Rain\Database\Traits\Validation;

	// Setup
	public $implement = ['System.Behaviors.SettingsModel'];
	public $settingsCode = 'zaxbux_iubendapolicy_settings';
	public $settingsFields = 'fields.yaml';

	// Validation
	public $rules = [
		'policy_id' => 'required|numeric'
	];

	// Validation error messages
	public $customMessages = [
		'policy_id.required' => 'zaxbux.iubendaprivacypolicy::lang.fields.policy_id.validation.required',
		'policy_id.numeric'  => 'zaxbux.iubendaprivacypolicy::lang.fields.policy_id.validation.numeric'
	];

	/*
	 * @{inheritdoc}
	 */
	public function formBeforeSave($model) {
		$model->validate();
	}
}