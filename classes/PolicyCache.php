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
	const IUBENDA_POLICY_STYLE = 'no-markup';

	private $policyID;
	private $cachePrivacyKey;
	private $cacheCookieKey;

	public function __construct() {
		$this->policyID        = Settings::get('policy_id', null);
		$this->cachePrivacyKey = sprintf('iubenda_policy_%s_content', $this->policyID);
		$this->cacheCookieKey  = sprintf('iubenda_cookie_policy_%s_content', $this->policyID);
	}

	/**
	 * Convert smart/curly quotes to regular quotes
	 * @param string $str
	 * @return string
	 */
	private static function convertSmartQuotes($str) {
		// UTF-8
		$search = [
			"\xe2\x80\x98", // Left single quote
			"\xe2\x80\x99", // Right single quote
			"\xe2\x80\x9c", // Left double quote
			"\xe2\x80\x9d", // Right double quote
			"\xe2\x80\x93", // EN Dash
			"\xe2\x80\x94", // EM Dash
		];

		$replace = [
			"'",
			"'",
			'"',
			'"',
			'&ndash;',
			'&mdash;'
		];

		return \str_replace($search, $replace, $str);
	}

	/**
	 * Remove inline javascript
	 * @param string $html
	 * @return string
	 */
	private static function removeInlineJS($html) {
		$document       = new \DOMDocument();
		$internalErrors = libxml_use_internal_errors(true);
		$document->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD); // load HTML
		libxml_use_internal_errors($internalErrors); // Restore error level

		$xpath = new \DOMXPath($document);

		// Remove <script> elements
		foreach ($xpath->query('//script') as $element) {
			$element->parentNode->removeChild($element);
		}

		// remove onClick attributes on a elements
		foreach ($xpath->query('//a[@onclick]') as $element) {
			$element->removeAttribute('onclick');
		}

		return $document->saveHTML($document->documentElement);
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
			Log::error(
				sprintf(
					'Error fetching Iubenda policy: %s. Response code: %d. Error: "%s"',
					$this->policyID,
					$response->getStatusCode(),
					$policyJson['error']
				)
			);

			return 'ERROR: Please check logs.';
		}

		$policyContent = $policyJson['content'];

		if (Settings::get('remove_js')) {
			$policyContent = self::removeInlineJS($policyContent);
		}

		// Convert smart quotes to regular quotes
		// THIS MUST BE CALLED AFTER REMOVING INLINE JS, otherwise encoding madness
		return self::convertSmartQuotes($policyContent);
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
			self::IUBENDA_POLICY_STYLE
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
			self::IUBENDA_POLICY_STYLE
		);

		$html           = $this->fetchPolicy($url);
		$document       = new \DOMDocument();
		$internalErrors = libxml_use_internal_errors(true);
		$document->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD); // load HTML
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
