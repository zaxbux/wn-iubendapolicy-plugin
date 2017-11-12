<?php return [
    'plugin' => [
        'name' => 'Iubenda Policy Cacher',
        'description' => 'Cache your Iubenda privacy policy, and display on your site.'
		],
		'permissions' => [
			'tab' => 'Iubenda Policy Cacher',
			'label' => 'Manage Iubenda Policy Settings'
		],
		'components' => [
			'iubendaPolicy' => [
				'name' => 'Iubenda Policy',
				'description' => 'Display your Iubenda privacy policy.'
			],
			'iubendaCookiePolicy' => [
				'name' => 'Iubenda Cookie Policy',
				'description' => 'Display your Iubenda Cookie policy.'
			]
		],
		'fields' => [
			'policy_id' => [
				'label' => 'Iubenda Policy ID',
				'comment' => 'A six digit ID for your privacy policy, obtainable from the embedding code of your policy.',
				'validation' => [
					'required' => 'The Policy ID is required.',
					'numeric' => 'The Policy ID must be a number.',
					'digits' => 'The Policy ID must be 6 digits long.'
				]
			],
			'policy_style' => [
				'label' => 'Policy Style',
				'comment' => 'How the privacy policy should be displayed.',
				'options' => [
					'default' => 'Default',
					'only-legal' => 'Only legal',
					'no-markup' => 'No markup'
				]
			]
		]
];