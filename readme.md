# Iubenda Policy Cacher
This October plugin allows you to display your Iubenda privacy policy on your website. Your policy is cached every day, ensuring the latest version is served to your users, and caching minimizes wait times.

## Requirements
You must have a PRO  attached to your privacy policy in order to access the API.

## Plugin Settings
There are only two options to configure. Each time you save the settings, the policy is removed from the cache.

### Policy ID
This is your six digit Iubenda policy ID, whoch can be obtained from your embedding code on the Iubenda dashbard.

### Policy Style
How you want your policy to be displayed.
* **Default**: Styled privacy policy
* **Only legal**: Removes icons and most of the CSS
* **No markup**: Similar to 'Only legal', no extra markup or CSS

## Policy Component
The `IubendaPolicy` component allows your privacy policy to be displayed on any page.
```
title = "Privacy Policy"
url = "/privacy"

[IubendaPolicy]
==
{% component 'IubendaPolicy' %}
```
Any errors will be displayed in place of your policy.

## CookiePolicy Component
The `IubendaCookiePolicy` component allows your cookie policy to be displayed on any page.
```
title = "Cookie Policy"
url = "/cookies"

[IubendaCookiePolicy]
==
{% component 'IubendaCookiePolicy' %}
```
Any errors will be displayed in place of your policy.

## Change Log
* 1.0.4 - Added support for cookie policy
* 1.0.3 - Localization improvements
* 1.0.2 - Refactored code
* 1.0.1 - First version

## TODO
* Display older cached policy and log errors instead, and display logs in backend