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
	private $cacheCookieKey;
	private $policyId;
	private $policyStyle;

	public function __construct() {
		$this->policyId = Settings::get('policy_id', null);
		
		$policyStyle = Settings::get('policy_style', 'no-markup');
		$this->policyStyle = str_replace('default', '', $policyStyle);

		$this->cacheKey = sprintf('iubenda_policy_%s_content', $this->policyId);
		$this->cacheCookieKey = sprintf('iubenda_cookie_policy_%s_content', $this->policyId);
	}

	/**
	 * Retrive a fresh version of the privacy policy from Iubenda
	 */
	public function fetchPolicy() {
		
		// Cannot continue without a policy ID
		if (empty($this->policyId)) {
			return;
		}
		
		// Download privacy policy from Iubenda
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
		$html = iconv('UTF-8', 'ASCII//TRANSLIT', $policyJson['content']); // Convert irregular quotes
		return $html;
	}

	/**
	 * Retrive a fresh version of the privacy policy from Iubenda
	 */
	 public function fetchCookiePolicy() {
		
		// Cannot continue without a policy ID
		if (empty($this->policyId)) {
			return;
		}

		// Only two options are available for the cookie policy
		$policyStyle = str_replace('only-legal', 'no-markup', $this->policyStyle);
		
		// Download cookie policy from Iubenda
		$guzzle = new GuzzleClient();
		$response = $guzzle->get(sprintf('https://www.iubenda.com/api/privacy-policy/%s/cookie-policy/%s', $this->policyId, $policyStyle), ['http_errors' => false]);
		$policyJson = json_decode($response->getBody(), true);

		if ($policyJson['success'] == false) {
			// An error beyond our control
			if ($response->getStatusCode() === 500) {
				return;
			}

			// Incorrect settings
			return 'ERROR: '.$policyJson['error'];
		}

		// Success! Parse HTML without doctype
		$html = iconv('UTF-8', 'ASCII//TRANSLIT', $policyJson['content']); // Convert irregular quotes

		$document = new \DOMDocument();
		$internalErrors = libxml_use_internal_errors(true);
		$document->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD); // load HTML
		libxml_use_internal_errors($internalErrors); // Restore error level

		// remove "view complete policy" link
		$xpath = new \DOMXPath($document);
		foreach ($xpath->query('//div[contains(attribute::class, "iub_footer")]/a[contains(attribute::class, "show_comp_link")]') as $element) {
			$element->parentNode->removeChild($element);
		}

		return $document->saveHTML($document->documentElement);
	}

	/**
	 * Download a fresh version of the policy
	 */
	public function update() {
		Cache::forever($this->cacheKey, self::fetchPolicy());
		Cache::forever($this->cacheCookieKey, self::fetchCookiePolicy());
	}

	/**
	 * Get the policy from the cache
	 */
	public function getPolicy() {
		// No policy ID, usually when plugin is activated for the first time
		if ($this->policyId == null) {
			return 'ERROR: No policy ID set.';
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
	 * Get the policy from the cache
	 */
	 public function getCookiePolicy() {
		// No policy ID, usually when plugin is activated for the first time
		if ($this->policyId == null) {
			return 'ERROR: No policy ID set.';
		}

		// Get the policy from cache
		$policyContent = Cache::get($this->cacheCookieKey, null);

		if ($policyContent) {
			return $policyContent;
		}

		// No policy in the cache, fetch a fresh version
		$policyContent = self::fetchCookiePolicy();

		// Update the cache
		Cache::forever($this->cacheCookieKey, $policyContent);

		return $policyContent;
	}

	/**
	 * Remove the policy from the cache
	 */
	public function forgetPolicy() {
		Cache::forget($this->cacheKey);
		Cache::forget($this->cacheCookieKey);
	}
}