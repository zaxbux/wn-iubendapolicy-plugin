<?php

return [
    'plugin' => [
	    'name'        => 'Iubenda Policy',
	    'description' => 'Display your Iubenda privacy and cookie policies.',
    ],
    'permissions' => [
	    'tab'   => 'Iubenda Policy Cacher',
	    'label' => 'Manage Iubenda Policy Settings',
    ],
    'components' => [
	    'iubendaPrivacyPolicy' => [
		    'name'        => 'Privacy Policy',
		    'description' => 'Display your Iubenda privacy policy.',
	    ],
	    'iubendaCookiePolicy'  => [
		    'name'        => 'Cookie Policy',
		    'description' => 'Display your Iubenda cookie policy.',
	    ],
	    'iubendaCookieSolution' => [
		    'name'        => 'Cookie Solution',
		    'description' => 'Embed your Iubenda cookie solution.',
	    ],
    ],
    'fields' => [
	    'policy_id'    => [
		    'label'      => 'Iubenda Policy ID',
		    'comment'    => 'The public ID for your privacy policy.',
		    'validation' => [
			    'required' => 'The Policy ID is required.',
			    'numeric'  => 'The Policy ID must be a number.',
		    ],
	    ],
	    'cookie_embed' => [
		    'label'   => 'Iubenda Cookie Solution Embed',
		    'comment' => 'The embed code for your cookie solution from the Iubenda dashboard.',
	    ],
    ],
];
