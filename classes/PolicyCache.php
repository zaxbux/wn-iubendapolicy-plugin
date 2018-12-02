<?php

namespace Zaxbux\IubendaPolicy\Classes;

use Log;
use Cache;
use GuzzleHttp\Client as GuzzleClient;
use Zaxbux\IubendaPolicy\Models\Settings;

/**
 * Caching operations for Iubenda Policy
 * @author zaxbux
 */
class PolicyCache {

	const IUBENDA_BASE_URL = 'https://www.iubenda.com/api/privacy-policy';

	private $cachePrivacyKey;
	private $cacheCookieKey;
	private $policyID;
	private $policyStyle;

	public function __construct() {
		$this->policyID        = Settings::get('policy_id', null);
		$this->policyStyle     = str_replace('default', '',
			Settings::get('policy_style', 'no-markup')
		);
		$this->cachePrivacyKey = sprintf('iubenda_policy_%s_content', $this->policyID);
		$this->cacheCookieKey  = sprintf('iubenda_cookie_policy_%s_content', $this->policyID);
	}

	/**
	 * Fetch a policy from Iubenda
	 * @param $url
	 * @return string
	 */
	private function fetchPolicy($url) {
		// Cannot continue without a policy ID
		if (empty($this->policyID)) {
			return 'ERROR: Please set a policy ID.';
		}

		// Get privacy policy
		$guzzle     = new GuzzleClient();
		$response   = $guzzle->get($url, ['http_errors' => false]);
		$policyJson = json_decode($response->getBody(), true);

		if ($policyJson['success'] == false) {
			// An error beyond our control, log it
			Log::error('Error fetching Iubenda policy.', [
				'response_code' => $response->getStatusCode(),
				'error'         => $policyJson['error'],
				'policy_id'     => $this->policyID
			]);

			return 'ERROR: Please check logs.';
		}

		// Success!
		return iconv('UTF-8', 'ASCII//TRANSLIT', $policyJson['content']); // Convert irregular quotes
	}

	/**
	 * Get a cached policy from the cache
	 * @param string $key
	 * @return string
	 */
	private function getPolicy($key) {
		// No policy ID, usually when plugin is activated for the first time
		if ($this->policyID == null) {
			return 'ERROR: No policy ID set.';
		}

		// Get the policy from cache
		$policyContent = Cache::get($key, null);

		if ($policyContent) {
			return $policyContent;
		}

		// No policy in the cache, fetch a fresh version
		if ($key == $this->cachePrivacyKey) {
			$policyContent = self::fetchPrivacyPolicy();
		} elseif ($key == $this->cacheCookieKey) {
			$policyContent = self::fetchCookiePolicy();
		}

		// Update the cache
		Cache::forever($key, $policyContent);

		return $policyContent;
	}

	/**
	 * Retrieve a fresh version of the privacy policy from Iubenda
	 * @return string
	 */
	public function fetchPrivacyPolicy() {
		$url = sprintf(
			self::IUBENDA_BASE_URL . '/%s/%s',
			$this->policyID,
			$this->policyStyle
		);

		return $this->fetchPolicy($url);
	}

	/**
	 * Retrieve a fresh version of the privacy policy from Iubenda
	 * @return string
	 */
	public function fetchCookiePolicy() {
		$url = sprintf(
			self::IUBENDA_BASE_URL . '/%s/cookie-policy/%s',
			$this->policyID,
			$this->policyStyle
		);

		$html           = $this->fetchPolicy($url);
		$document       = new \DOMDocument();
		$internalErrors = libxml_use_internal_errors(true);
		$document->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD); // load HTML
		libxml_use_internal_errors($internalErrors); // Restore error level

		// remove "view complete policy" link
		$xpath = new \DOMXPath($document);
		foreach ($xpath->query(
			'//div[contains(attribute::class, "iub_footer")]/a[contains(attribute::class, "show_comp_link")]'
		) as $element) {
			$element->parentNode->removeChild($element);
		}

		return $document->saveHTML($document->documentElement);
	}

	/**
	 * Get the privacy policy
	 * @return string
	 */
	public function getPrivacyPolicy() {
		return $this->getPolicy($this->cachePrivacyKey);
	}

	/**
	 * Get the cookie policy
	 * @return string
	 */
	public function getCookiePolicy() {
		return $this->getPolicy($this->cacheCookieKey);
	}

	/**
	 * Download a fresh version of the policy
	 */
	public function update() {
		Cache::forever($this->cachePrivacyKey, $this->fetchPrivacyPolicy());
		Cache::forever($this->cacheCookieKey, $this->fetchCookiePolicy());
	}

	/**
	 * Remove the policies from the cache
	 */
	public function forget() {
		Cache::forget($this->cachePrivacyKey);
		Cache::forget($this->cacheCookieKey);
	}
}
