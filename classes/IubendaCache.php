<?php namespace Wackywired135\IubendaPolicyCacher\Classes;

use Cache;
use GuzzleHttp\Client as GuzzleClient;
use Wackywired135\IubendaPolicyCacher\Models\Settings;

/**
 * Caching operations for Iubenda Policy Cacher
 * @author wackywired135
 */
class IubendaCache {

	private $cacheKey;
	private $policyId;
	private $policyStyle;

	public function __construct() {
		$this->policyId = Settings::get('policy_id', null);
		
		$policyStyle = Settings::get('policy_style', 'no-markup');
		$this->policyStyle = str_replace('default', '', $policyStyle);

		$this->cacheKey = sprintf('iubenda_policy_%s_content', $this->policyId);
	}

	/**
	 * Retrive a fresh version of the privacy policy from Iubenda
	 */
	public function fetchPolicy() {
		
		// Cannot continue without a policy ID
		if (empty($this->policyId)) {
			return;
		}
		
		// Download policy from Iubenda
		$guzzle = new GuzzleClient();
		$response = $guzzle->get(sprintf('https://www.iubenda.com/api/privacy-policy/%s/%s', $this->policyId, $this->policyStyle), ['http_errors' => false]);
		$policyJson = json_decode($response->getBody(), true);

		if ($policyJson['success'] == false) {
			// An error beyond our control
			if ($response->getStatusCode() === 500) {
				return;
			}

			// Incorrect settings
			return 'ERROR: '.$policyJson['error'];
		}

		// Success!
		return $policyJson['content'];
	}

	/**
	 * Download a fresh version of the policy
	 */
	public function update() {
		Cache::forever($this->cacheKey, self::fetchPolicy());
	}

	/**
	 * Get the policy from the cache
	 */
	public function getPolicy() {
		// No policy ID, usually when plugin is activated for the first time
		if ($this->policyId == null) {
			return 'ERROR: No policy ID configured.';
		}

		// Get the policy from cache
		$policyContent = Cache::get($this->cacheKey, null);

		if ($policyContent) {
			return $policyContent;
		}

		// No policy in the cache, fetch a fresh version
		$policyContent = self::fetchPolicy();

		// Update the cache
		Cache::forever($this->cacheKey, $policyContent);

		return $policyContent;
	}

	/**
	 * Remove the policy from the cache
	 */
	public function forgetPolicy() {
		Cache::forget($this->cacheKey);
	}
}