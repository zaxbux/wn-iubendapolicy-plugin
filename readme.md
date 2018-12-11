# Iubenda Policy Cacher

This October CMS plugin allows you to display your Iubenda privacy/cookie policy on your website. The policy is downloaded and cached every day, ensuring the latest version is served to your users, and caching minimizes wait times.

## Requirements

You must have a PRO subscription attached to your privacy policy in order to access the API.

## Plugin Settings

There are only two options to configure. Each time you save the settings, the policy is removed from the cache and downloaded.

### Policy ID

Your Iubenda policy ID, which can be obtained from your embedding code on the Iubenda dashboard.

## Components

These components are also available as snippets for the [Static Pages](https://octobercms.com/plugin/rainlab-pages) plugin.

### Privacy Policy Component

The `iubendaPrivacyPolicy` component allows your privacy policy to be displayed on any page.

```
title = "Privacy Policy"
url = "/privacy"

[iubendaPolicy]
==
{% component 'iubendaPrivacyPolicy' %}
```

### Cookie Policy Component

The `iubendaCookiePolicy` component allows your cookie policy to be displayed on any page.

```
title = "Cookie Policy"
url = "/cookies"

[iubendaCookiePolicy]
==
{% component 'iubendaCookiePolicy' %}
```

### Cookie Solution Component

The `iubendaCookieSolution` component allows your cookie solution embed code to be displayed on any page.

If you place this component in a layout, your cookie solution will display on every page that uses the layout.

```
{% component 'iubendaCookieSolution' %}
```

## Errors

If there is an error when retrieving the policy, generic error messages will displayed in place of a policy. Extra information is logged in the backend.

## Console Commands

These can be used for finding issues with policy retrieval, or for automating the download if you don't use the built-in scheduler.

`iubenda:update` - Downloads a fresh version of the policy.
`iubenda:forget` - Remove the cached policies.

## Change Log

* 1.1.3 - Added support for static pages snippets
* 1.1.2 - Added basic support for cookie solution
* 1.1.1 - Removed incompatible policy style setting
* 1.1.0 - Renamed plugin & cleaned up code
* 1.0.4 - Added support for cookie policy
* 1.0.3 - Localization improvements
* 1.0.2 - Refactored code
* 1.0.1 - First version

## TODO

* Add support for additional Iubenda services.