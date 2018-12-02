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
	    ]
    ],
    'fields' => [
	    'policy_id'    => [
		    'label'      => 'Iubenda Policy ID',
		    'comment'    => 'The public ID for your privacy policy.',
		    'validation' => [
			    'required' => 'The Policy ID is required.',
			    'numeric'  => 'The Policy ID must be a number.',
		    ]
	    ],
	    'policy_style' => [
		    'label'   => 'Policy Style',
		    'comment' => 'How the privacy policy should be displayed.',
		    'options' => [
			    'default'    => 'Default',
			    'only-legal' => 'Only legal',
			    'no-markup'  => 'No markup',
		    ]
	    ]
    ]
];
