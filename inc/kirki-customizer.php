<?php

/**
 * Add postMessage support for site title and description for the Theme Customizer
 *
 * @param WP_Customize_Manager $wp_customize
 */
function devcon_msummit2024_customize_register($wp_customize) {
	devcon_msummit2024_setup_system_section($wp_customize);
	devcon_msummit2024_setup_landing_page_customize_section($wp_customize);
}

const DEVCON_MSUMMIT_THEME_MOD_PREFIX = 'devcon-msummit2024_';

function devcon_msummit2024_get_mod_id($id) {
	$mod_id = strtolower(str_replace(' ', '_', $id));
	if ( !str_starts_with( $mod_id, DEVCON_MSUMMIT_THEME_MOD_PREFIX ) ) {
		return DEVCON_MSUMMIT_THEME_MOD_PREFIX . $mod_id;
	}
	return $mod_id;
}

function devcon_msummit2024_get_theme_mod($name, $default = null) {
	$id = devcon_msummit2024_get_mod_id($name);
	return get_theme_mod($id, $default);
}

$customTemplateTags = [
	'[alt_text]' => function () {
		return '<span class="type-effect" id="alternating-text"></span>';
	},
	'[devcon_logo]' => function() {
		return '<img class="inline mr-0 -mt-2" src="'. devcon_msummit2024_get_asset_url('devcon_logo_2.png', true) .'" alt="DEVCON">';
	},
];

function devcon_msummit2024_add_setting(WP_Customize_Manager $wp_customize, string $id, array $args): WP_Customize_Setting {
	$mod_id = devcon_msummit2024_get_mod_id($id);
	if ($args && array_key_exists('default', $args)) {
		set_theme_mod($mod_id, $args['default']);
	}
	return $wp_customize->add_setting($mod_id, $args);
}

function devcon_msummit2024_render_text($setting_name, $default = null, $return = false) {
	$text = devcon_msummit2024_get_theme_mod($setting_name, $default ?? $setting_name);

	// replace newline with <br />
	$text = str_replace("\\n", "<br />", $text);

	// replace custom template tags (e.g. [alt_text]) with their respective values
	global $customTemplateTags;
	foreach ($customTemplateTags as $tag => $callback) {
		$text = str_replace($tag, $callback(), $text);
	}

	if ($return) {
		return $text;
	}

	echo $text;
	return "";
}

function devcon_msummit2024_render_section($section_name) {
	$sectionTitle = devcon_msummit2024_render_text('section_' . $section_name . '_title', default: '', return: true );
	$sectionDescription = devcon_msummit2024_render_text('section_' . $section_name . '_description', default: '', return: true );

	return [
		$sectionTitle,
		$sectionDescription
	];
}

function devcon_msummit2024_setup_system_section(WP_Customize_Manager $wp_customize) {
	// === General ===
	$general_section = $wp_customize->add_section( 'devcon-msummit2024_general_section', [
		'title'    => __( 'Theme-wide Settings', 'devcon-msummit2024' ),
		'priority' => 103,
	] );

	// Load default settings button
	$load_default_settings_setting = devcon_msummit2024_add_setting($wp_customize, 'load_default_settings', [
		'default' => false,
//		'transport' => 'refresh',
	]);

	$wp_customize->add_control($load_default_settings_setting->id, [
		'label' => __('Settings Configuration', 'devcon-msummit2024'),
		'section' => $general_section->id,
		'settings' => [],
		'type' => 'button',
		'input_attrs' => [
			'value' => 'Load Default Settings',
			'class' => 'button button-primary',
		],
	]);
}

function devcon_msummit2024_setup_landing_page_customize_section(WP_Customize_Manager $wp_customize) {
	$custom_landing_page_panel = $wp_customize->add_panel('devcon-msummit2024_landing_page_panel', [
		'title' => __('Landing Page', 'devcon-msummit2024'),
		'description' => __('Options for modifying the details to be displayed to the homepage', 'devcon-msummit2024'),
		'priority' => 105,
	]);

	// === Hero ===
	$hero_section = $wp_customize->add_section('devcon-msummit2024_hero_section', [
		'title' => __('Hero', 'devcon-msummit2024'),
		'panel' => $custom_landing_page_panel->id,
		'priority' => 10,
	]);

	// Hero Title
	$hero_title_setting = devcon_msummit2024_add_setting($wp_customize, 'hero_title', [
		'default' => 'Weaving tech\nfor [alt_text]',
//		'transport' => 'refresh',
	]);

	(new Kirki\Field\Textarea([
		'section' => $hero_section->id,
		'settings' => $hero_title_setting->id,
		'label' => __('Hero Title', 'devcon-msummit2024'),
		'description' => __('To add the alternating text, simply add [alt_text] tag.', 'devcon-msummit2024'),
		'default' => $hero_title_setting->default,
	]))->add_control($wp_customize);

	// Hero alt texts - List of alternating texts for hero text (eg. Students, Professionals, All)
	$default_alternating_texts = [
		'Students',
		'Developers',
		'Professionals',
		'Designers',
		'Teachers',
		'All'
	];

	$hero_alternating_texts_setting = devcon_msummit2024_add_setting($wp_customize, 'hero_alternating_texts',  [
		'default' => $default_alternating_texts,
//		'transport' => 'refresh',
		'sanitize_callback' => 'sanitize_text_field',
	]);

	(new Kirki\Field\Textarea([
		'section' => $hero_section->id,
		'settings' => $hero_alternating_texts_setting->id,
		'label' => __('Alternating Texts', 'devcon-msummit2024'),
		'description' => __('List of alternating texts for hero text (eg. Students, Professionals, All)', 'devcon-msummit2024'),
		'default' => implode(", ", $default_alternating_texts),
	]))->add_control($wp_customize);

	// Hero content description
	$hero_content_setting = devcon_msummit2024_add_setting($wp_customize, 'hero_content',  [
		'default' => 'Lorem ipsum dolor sit amet consectetur. Erat felis cras praesent in proin vitae. Nisl turpis sagittis tortor feugiat diam maecenas fermentum vitae pellentesque. Ultrices in neque nunc arcu cras. Hac morbi fusce amet quisque erat. Ac feugiat.',
//		'transport' => 'refresh',
	]);

	(new Kirki\Field\Textarea([
		'section' => $hero_section->id,
		'settings' => $hero_content_setting->id,
		'label' => __('Hero Content', 'devcon-msummit2024'),
		'default' => $hero_content_setting->default,
	]))->add_control($wp_customize);

	// Event Location
	$event_location_setting = devcon_msummit2024_add_setting($wp_customize, 'event_location',  [
		'default' => '123 Pizzaplex, Freddy Fazbear St.',
//		'transport' => 'refresh',
	]);

	(new Kirki\Field\Text([
		'section' => $hero_section->id,
		'settings' => $event_location_setting->id,
		'label' => __('Event Location', 'devcon-msummit2024'),
		'default' => $event_location_setting->default,
	]))->add_control($wp_customize);

	// Event Date
	$event_date_setting = devcon_msummit2024_add_setting($wp_customize, 'event_date',  [
		'default' => 'June 29-30, 2024',
//		'transport' => 'refresh',
	]);

	(new Kirki\Field\Text([
		'section' => $hero_section->id,
		'settings' => $event_date_setting->id,
		'label' => __('Event Date', 'devcon-msummit2024'),
		'default' => $event_date_setting->default,
	]))->add_control($wp_customize);

	// Event Time
	$event_time_setting = devcon_msummit2024_add_setting($wp_customize, 'event_time',  [
		'default' => '8:00 AM - 5:00 PM',
//		'transport' => 'refresh',
	]);

	(new Kirki\Field\Text([
		'section' => $hero_section->id,
		'settings' => $event_time_setting->id,
		'label' => __('Event Time', 'devcon-msummit2024'),
		'default' => $event_time_setting->default,
	]))->add_control($wp_customize);


	// Call-to-action button text
	$cta_button_text_setting = devcon_msummit2024_add_setting($wp_customize, 'cta_button_text',  [
		'default' => 'View Tickets',
//		'transport' => 'refresh',
	]);

	(new Kirki\Field\Text([
		'section' => $hero_section->id,
		'settings' => $cta_button_text_setting->id,
		'label' => __('Call-to-action Button Text', 'devcon-msummit2024'),
		'default' => $cta_button_text_setting->default,
	]))->add_control($wp_customize);

	// Call-to-action button URL
	$cta_button_url_setting = devcon_msummit2024_add_setting($wp_customize, 'cta_button_url',  [
		'default' => '#',
//		'transport' => 'refresh',
		'sanitize_callback' => 'esc_url_raw',
	]);

	(new Kirki\Field\URL([
		'section' => $hero_section->id,
		'settings' => $cta_button_url_setting->id,
		'label' => __('Call-to-action Button URL', 'devcon-msummit2024'),
		'default' => $cta_button_url_setting->default,
	]))->add_control($wp_customize);

	// === Sections ===
	$sections_section = $wp_customize->add_section('devcon-msummit2024_sections_section', [
		'title' => __('Sections', 'devcon-msummit2024'),
		'panel' => $custom_landing_page_panel->id,
		'priority' => 20,
	]);

	$sections = [
		[
			'name' => 'Overview',
			'title' => 'Welcome to the\n Mindanao [devcon_logo] Summit',
			'description' => "We're excited to have you join us for the Mindanao DEVCON Summit! Get ready for an immersive experience filled with insightful sessions, engaging discussions, and networking opportunities."
		],
		[
			'name' => 'Program Agenda',
			'title' => 'Program Agenda',
			'description' => 'Get to know our lineup of prominent speakers who will be sharing their expertise at the Mindanao DEVCON Summit.'
		],
		[
			'name' => 'Featured Speakers',
			'title' => 'Featured Speakers',
			'description' => 'Get to know our lineup of prominent speakers who will be sharing their expertise at the Mindanao DEVCON Summit.'
		],
		[
			'name' => 'Tickets',
			'title' => 'Tickets',
			'description' => 'Register for the Mindanao DEVCON Summit and secure your spot today!'
		],
		[
			'name' => 'Testimonials',
			'title' => 'Testimonials',
			'description' => 'Get to know our lineup of prominent speakers who will be sharing their expertise at the Mindanao DEVCON Summit.'
		],
		[
			'name' => 'Sponsorship Packages',
			'title' => 'Sponsor this Event',
			'description' => 'Register for the Mindanao DEVCON Summit and secure your spot today!'
		],
		[
			'name' => 'FAQs',
			'title' => 'Frequently Asked Questions',
		],
		[
			'name' => 'Countdown',
			'title' => 'Save the Date!',
			'description' => 'Tick-tock tech enthusiasts! Get ready for two days packed with innovation, knowledge, and networking.'
		],
		[
			'name' => 'Social Media Feed',
			'title' => "See what's going on",
			'description' => 'Join the conversation on social media using the hashtag #MindanaoDEVCONSummit. Share your thoughts, insights, and photos from the event.'
		],
		[
			'name' => 'Sponsors List',
			'title' => 'Discover the driving force behind the summit!',
			'description' => ''
		]
	];

	foreach ($sections as $section) {
		$sectionId = devcon_msummit2024_get_mod_id( 'section_' . $section['name']);
		$section_headline_setting = devcon_msummit2024_add_setting($wp_customize, $sectionId . '_headline', [
			'default' => $section['name'],
		]);

		(new Kirki\Pro\Field\Headline([
			'section' => $sections_section->id,
			'settings' => $section_headline_setting->id,
			'label' => $section['name'],
			'default' => $section_headline_setting->default,
			'description' => 'Section Headline',
		]))->add_control($wp_customize);

		$section_title_setting = devcon_msummit2024_add_setting($wp_customize, $sectionId . '_title', [
			'default' => $section['title'],
//			'transport' => 'refresh',
		]);

		(new Kirki\Field\Text([
			'section' => $sections_section->id,
			'settings' => $section_title_setting->id,
			'label' => '"'. $section['name'] .'" Title',
			'default' => $section_title_setting->default,
		]))->add_control($wp_customize);

		$section_description_setting = devcon_msummit2024_add_setting($wp_customize, $sectionId . '_description', [
			'default' => $section['description'] ?? "",
//			'transport' => 'refresh',
		]);

		(new Kirki\Field\Textarea([
			'section' => $sections_section->id,
			'settings' => $section_description_setting->id,
			'label' => '"'. $section['name'] .'" Description',
			'default' => $section_description_setting->default,
		]))->add_control($wp_customize);
	}

	// === Overview ===
	$overview_section = $wp_customize->add_section('devcon-msummit2024_overview_section', [
		'title' => __('Overview', 'devcon-msummit2024'),
		'panel' => $custom_landing_page_panel->id,
		'priority' => 20,
	]);

	// Overview activities (consists of image, activity name, and description)
	$overview_activities_setting = devcon_msummit2024_add_setting($wp_customize, 'overview_activities',  [
		'default' => [
			[
				'image' => 'https://via.placeholder.com/285x280',
				'name' => 'Interactive Workshops',
				'description' => 'Engage in hands-on workshops led by industry experts. Learn new skills and gain practical knowledge in a collaborative environment.'
			],
			[
				'image' => 'https://via.placeholder.com/285x280',
				'name' => 'Tech Talks',
				'description' => 'Hear from prominent speakers and thought leaders as they share their insights and expertise on the latest trends and innovations in tech.'
			],
			[
				'image' => 'https://via.placeholder.com/285x280',
				'name' => 'Networking',
				'description' => 'Connect with fellow tech enthusiasts, industry professionals, and potential employers. Share ideas, collaborate, and build lasting relationships.'
			],
			[
				'image' => 'https://via.placeholder.com/285x280',
				'name' => 'Career Fair',
				'description' => 'Explore career opportunities and connect with potential employers. Get to know the companies and organizations that are looking to hire tech talent.'
			],
		],
	]);

	(new Kirki\Field\Repeater([
		'section' => $overview_section->id,
		'settings' => $overview_activities_setting->id,
		'default' => $overview_activities_setting->default,
		'label' => __('Overview Activities', 'devcon-msummit2024'),
		'row_label' => [
			'type' => 'field',
			'value' => __('Activity', 'devcon-msummit2024'),
			'field' => 'name'
		],
		'fields' => [
			'image' => [
				'type' => 'image',
				'label' => __('Image', 'devcon-msummit2024'),
				'default' => 'https://via.placeholder.com/285x280',
			],
			'name' => [
				'type' => 'text',
				'label' => __('Name', 'devcon-msummit2024'),
				'default' => 'Activity Name',
			],
			'description' => [
				'type' => 'textarea',
				'label' => __('Description', 'devcon-msummit2024'),
				'default' => 'Activity Description',
			],
		],
	]));

	// === Speakers ===
	$speakers_section = $wp_customize->add_section('devcon-msummit2024_speakers_section', [
		'title' => __('Speakers', 'devcon-msummit2024'),
		'panel' => $custom_landing_page_panel->id,
		'priority' => 25,
	]);

	$speakers_setting = devcon_msummit2024_add_setting($wp_customize, 'speakers',  [
		'default' => [
			[
				'name' => 'John Doe',
				'position' => 'CEO, Company Name',
				'photo' => devcon_msummit2024_get_asset_url('speaker_sample.png', true),
				'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
			],
			[
				'name' => 'Jane Doe',
				'position' => 'CTO, Company Name',
				'photo' => devcon_msummit2024_get_asset_url('speaker_sample_2.png', true),
				'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
			],
			[
				'name' => 'John Smith',
				'position' => 'Lead Developer, Company Name',
				'photo' => devcon_msummit2024_get_asset_url('speaker_sample.png', true),
				'bio' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
			],
		],
	]);

	(new Kirki\Field\Repeater([
		'section' => $speakers_section->id,
		'settings' => $speakers_setting->id,
		'default' => $speakers_setting->default,
		'label' => __('Speakers', 'devcon-msummit2024'),
		'row_label' => [
			'type' => 'field',
			'value' => __('Speaker', 'devcon-msummit2024'),
			'field' => 'name'
		],
		'fields' => [
			'name' => [
				'type' => 'text',
				'label' => __('Name', 'devcon-msummit2024'),
				'default' => 'Speaker Name',
			],
			'position' => [
				'type' => 'text',
				'label' => __('Position', 'devcon-msummit2024'),
				'default' => 'Speaker Position',
			],
			'photo' => [
				'type' => 'image',
				'label' => __('Photo', 'devcon-msummit2024'),
				'default' => 'https://via.placeholder.com/150x150',
			],
			'bio' => [
				'type' => 'textarea',
				'label' => __('Bio', 'devcon-msummit2024'),
				'default' => 'Speaker Bio',
			],
		],
	]));

	// === Testimonials ===
	$testimonials_section = $wp_customize->add_section('devcon-msummit2024_testimonials_section', [
		'title' => __('Testimonials', 'devcon-msummit2024'),
		'panel' => $custom_landing_page_panel->id,
		'priority' => 25,
	]);

	$testimonials_setting = devcon_msummit2024_add_setting($wp_customize, 'testimonials',  [
		'default' => [
			[
				'quote' => "Attended last year's summit and it was amazing! Learned so much and made valuable connections. Can't wait for this year's event!",
				'name' => 'John C. Doe',
				'position' => 'CEO',
				'company' => 'Company ABC Inc.'
			],
			[
				'quote' => "As a first-time attendee, I was blown away by the quality of the sessions and the knowledge of the speakers. Highly recommend the Mindanao DEVCON Summit!",
				'name' => 'Jane Smith',
				'position' => 'Attendee'
			],
			[
				'quote' => "The Mindanao DEVCON Summit exceeded my expectations. The sessions were informative and the networking opportunities were invaluable. Looking forward to attending again! ",
				'name' => 'Mark Johnson',
				'position' => 'Volunteer'
			]
		],
	]);

	(new Kirki\Field\Repeater([
		'section' => $testimonials_section->id,
		'settings' => $testimonials_setting->id,
		'default' => $testimonials_setting->default,
		'label' => __('Testimonials', 'devcon-msummit2024'),
		'row_label' => [
			'type' => 'field',
			'value' => __('Testimonial', 'devcon-msummit2024'),
			'field' => 'name'
		],
		'fields' => [
			'quote' => [
				'type' => 'textarea',
				'label' => __('Quote', 'devcon-msummit2024'),
				'default' => 'Testimonial Quote',
			],
			'name' => [
				'type' => 'text',
				'label' => __('Name', 'devcon-msummit2024'),
				'default' => 'Testimonial Name',
			],
			'position' => [
				'type' => 'text',
				'label' => __('Position', 'devcon-msummit2024'),
				'default' => 'Testimonial Position',
			],
			'company' => [
				'type' => 'text',
				'label' => __('Company', 'devcon-msummit2024'),
				'default' => 'Testimonial Company',
			],
		],
	]));

	// === Frequently Asked Questions ===
	$faq_section = $wp_customize->add_section('devcon-msummit2024_faq_section', [
		'title' => __('Frequently Asked Questions', 'devcon-msummit2024'),
		'panel' => $custom_landing_page_panel->id,
		'priority' => 25,
	]);

	$faq_setting = devcon_msummit2024_add_setting($wp_customize, 'faq',  [
		'default' => [
			[
				'question' => 'What is the Mindanao DEVCON Summit?',
				'answer' => 'The Mindanao DEVCON Summit is an annual event that brings together tech enthusiasts, professionals, and industry leaders to share knowledge, insights, and best practices in the field of technology.'
			],
			[
				'question' => 'Who can attend the summit?',
				'answer' => 'The Mindanao DEVCON Summit is open to anyone who is passionate about technology and innovation. Whether you are a student, professional, or tech enthusiast, you are welcome to join us!'
			],
			[
				'question' => 'What can I expect from the summit?',
				'answer' => 'The Mindanao DEVCON Summit features a lineup of prominent speakers, engaging sessions, hands-on workshops, and networking opportunities. You can expect to gain valuable insights, learn new skills, and connect with industry professionals.'
			],
			[
				'question' => 'How can I register for the summit?',
				'answer' => 'You can register for the Mindanao DEVCON Summit by visiting our website and purchasing your tickets online. Early bird rates and group discounts are available, so be sure to secure your spot today!'
			],
			[
				'question' => 'How can I become a sponsor or partner?',
				'answer' => 'If you are interested in becoming a sponsor or partner for the Mindanao DEVCON Summit, please contact us for more information. We offer a range of sponsorship packages and opportunities for organizations looking to support the event.'
			],
		],
	]);

	(new Kirki\Field\Repeater([
		'section' => $faq_section->id,
		'settings' => $faq_setting->id,
		'default' => $faq_setting->default,
		'label' => __('Frequently Asked Questions', 'devcon-msummit2024'),
		'row_label' => [
			'type' => 'field',
			'value' => __('Question', 'devcon-msummit2024'),
			'field' => 'question'
		],
		'fields' => [
			'question' => [
				'type' => 'text',
				'label' => __('Question', 'devcon-msummit2024'),
				'default' => 'Question',
			],
			'answer' => [
				'type' => 'textarea',
				'label' => __('Answer', 'devcon-msummit2024'),
				'default' => 'Answer',
			],
		],
	]));

	// === Countdown ===
	$countdown_section = $wp_customize->add_section('devcon-msummit2024_countdown_section', [
		'title' => __('Countdown', 'devcon-msummit2024'),
		'panel' => $custom_landing_page_panel->id,
		'priority' => 25,
	]);

	// Countdown date
	$countdown_date_setting = devcon_msummit2024_add_setting($wp_customize, 'countdown_date',  [
		'default' => '2024-06-29',
	]);

	(new Kirki\Field\Date([
		'section' => $countdown_section->id,
		'settings' => $countdown_date_setting->id,
		'label' => __('Countdown Date', 'devcon-msummit2024'),
		'default' => $countdown_date_setting->default,
	]))->add_control($wp_customize);

	// === Social Proof ===
	$social_proof_section = $wp_customize->add_section('devcon-msummit2024_social_proof_section', [
		'title' => __('Social Proof', 'devcon-msummit2024'),
		'panel' => $custom_landing_page_panel->id,
		'priority' => 25,
	]);

	$curator_feed_id_setting = devcon_msummit2024_add_setting($wp_customize, 'curator_feed_id',  [
		'default' => '',
	]);

	(new Kirki\Field\Text([
		'section' => $social_proof_section->id,
		'settings' => $curator_feed_id_setting->id,
		'label' => __('Curator Feed ID', 'devcon-msummit2024'),
		'description' => 'The ID of the Curator.io feed to display on the landing page. Leave blank to disable.',
		'default' => $curator_feed_id_setting->default,
	]))->add_control($wp_customize);

	// === Sponsors ===
	$sponsors_section = $wp_customize->add_section('devcon-msummit2024_sponsors_section', [
		'title' => __('Sponsors', 'devcon-msummit2024'),
		'panel' => $custom_landing_page_panel->id,
		'priority' => 30,
	]);

	// Sponsorship package (icon image, title, description, and slots left)
	$sponsorship_packages_setting = devcon_msummit2024_add_setting($wp_customize, 'sponsorship_packages',  [
		'default' => [
			[
				'icon' => devcon_msummit2024_get_asset_url('sponsorship_packages/exhibitor.png', true),
				'title' => 'Exhibitor',
				'label' => 'Exhibitor',
				'description' => 'This package includes: logo visibility on event materials, verbal acknowledgment on-site, website recognition, social media presence, a 45-second video loop, and 2 complimentary sponsor passes.',
				'slots_left' => 10
			],
			[
				'icon' => devcon_msummit2024_get_asset_url('sponsorship_packages/bronze.png', true),
				'title' => 'Bronze',
				'label' => 'Bronze',
				'description' => 'This package includes: 45-minute speaking slot, exhibit space (first-come, first-served), 45-sec video loop, onsite acknowledgment, literature inclusion, logo visibility, social media mentions, press release inclusion, and two complimentary sponsor passes.',
				'slots_left' => 10
			],
			[
				'icon' => devcon_msummit2024_get_asset_url('sponsorship_packages/silver.png', true),
				'title' => 'Silver',
				'label' => 'Silver',
				'description' => 'This package includes: panel & 45-min speaking slots, exhibit space, 2-3 min AVP, 45-sec video loop, three standee placements, onsite acknowledgment, literature inclusion, logo visibility, social media mentions, press release, and 2 complimentary sponsor passes.',
				'slots_left' => 6
			],
			[
				'icon' => devcon_msummit2024_get_asset_url('sponsorship_packages/gold.png', true),
				'title' => 'Gold',
				'label' => 'Gold',
				'description' => 'This package includes: keynote and panel slots, 45-min speaking slot, exhibit space, AVP, 45-sec video loop, five standee placements, onsite acknowledgment, literature inclusion, logo visibility, social media mentions, press release, attendee list, and three complimentary passes.',
				'slots_left' => 0
			],
			[
				'icon' => devcon_msummit2024_get_asset_url('sponsorship_packages/platinum.png', true),
				'title' => 'Platinum',
				'label' => 'Platinum',
				'description' => 'This package includes: 1 co-organized event, keynote and panel slots, a 45-min speaking slot, exhibit space, AVP, 45-sec video loop, six standee placements, onsite acknowledgment, literature inclusion, logo visibility, social media mentions, logo on event shirt, press release, attendee list, and six complimentary passes.',
				'slots_left' => 2
			],
			[
				'icon' => devcon_msummit2024_get_asset_url('sponsorship_packages/copresenter.png', true),
				'title' => 'Co-presenter',
				'label' => 'Co-presenter',
				'description' => 'This package includes the same benefits as all other sponsors + 1 year exposure on all DEVCON Davao events and 2 co-organized events.',
				'slots_left' => 1
			]
		],
	]);

	(new Kirki\Field\Repeater([
		'section' => $sponsors_section->id,
		'settings' => $sponsorship_packages_setting->id,
		'default' => $sponsorship_packages_setting->default,
		'label' => __('Sponsorship Packages', 'devcon-msummit2024'),
		'row_label' => [
			'type' => 'field',
			'value' => __('Package', 'devcon-msummit2024'),
			'field' => 'title'
		],
		'fields' => [
			'icon' => [
				'type' => 'image',
				'label' => __('Icon', 'devcon-msummit2024'),
				'default' => devcon_msummit2024_get_asset_url('sponsorship_packages/exhibitor.png', true),
			],
			'title' => [
				'type' => 'text',
				'label' => __('Title', 'devcon-msummit2024'),
				'default' => 'Package Title',
			],
			'label' => [
				'type' => 'text',
				'label' => __('Label', 'devcon-msummit2024'),
				'default' => 'Package Label (for display purposes)',
			],
			'description' => [
				'type' => 'textarea',
				'label' => __('Description', 'devcon-msummit2024'),
				'default' => 'Package Description',
			],
			'slots_left' => [
				'type' => 'number',
				'label' => __('Slots Left', 'devcon-msummit2024'),
				'default' => 10,
			],
		],
	]));


	// Sponsors list
	$sponsors_setting = devcon_msummit2024_add_setting($wp_customize, 'sponsors',  [
		'default' => [
			[
				'name' => 'Mugna',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/mugna.png', true),
				'url' => 'https://www.mugna.tech'
			],
			[
				'name' => 'StreetWebs',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/street_webs.png', true),
				'url' => 'https://www.streetwebs.com'
			],
			[
				'name' => 'Davao Interschool Computer Enthusiasts',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/dice.png', true),
				'url' => 'https://dicedvo.org'
			],
			[
				'name' => 'Davao DeFi Community',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/ddc.png', true),
				'url' => 'https://www.facebook.com/groups/davaodefi'
			],
			[
				'name' => 'PWA Pilipinas',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/pwa_pilipinas.png', true),
				'url' => 'https://www.pwapilipinas.org'
			],
			[
				'name' => 'Blue Salmon Solutions',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/bluesalmon.png', true),
				'url' => 'https://www.linkedin.com/company/blue-salmon-solutions/'
			],
			[
				'name' => 'UX Davao',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/uxdavao.png', true),
				'url' => 'https://www.facebook.com/uxdavao'
			],
			[
				'name' => 'UP SPARCS',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/sparcs.png', true),
				'url' => 'https://www.facebook.com/UPSPARCS'
			],
			[
				'name' => 'IDEAS Davao',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/ideas.png', true),
				'url' => 'https://www.facebook.com/ideasdavao'
			],
			[
				'name' => 'DICT Region XI',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/dict.png', true),
				'url' => 'https://www.facebook.com/DICTRegionXI'
			],
			[
				'name' => 'ICT Davao',
				'logo' => devcon_msummit2024_get_asset_url('sponsors/ict_davao.png', true),
				'url' => 'https://www.facebook.com/ictdavao'
			]
		],
	]);

	(new Kirki\Field\Repeater([
		'section' => $sponsors_section->id,
		'settings' => $sponsors_setting->id,
		'default' => $sponsors_setting->default,
		'label' => __('Sponsors', 'devcon-msummit2024'),
		'row_label' => [
			'type' => 'field',
			'value' => __('Sponsor', 'devcon-msummit2024'),
			'field' => 'name'
		],
		'fields' => [
			'name' => [
				'type' => 'text',
				'label' => __('Name', 'devcon-msummit2024'),
				'default' => 'Sponsor Name',
			],
			'logo' => [
				'type' => 'image',
				'label' => __('Logo', 'devcon-msummit2024'),
				'default' => 'https://via.placeholder.com/150x50',
			],
			'url' => [
				'type' => 'text',
				'label' => __('URL', 'devcon-msummit2024'),
				'default' => 'https://www.sponsor.com',
			],
		],
	]));
}

add_action('customize_register', 'devcon_msummit2024_customize_register');

add_action('customize_controls_init', function () {
	wp_enqueue_script( 'devcon-msummit2024-customizer', get_template_directory_uri() . '/resources/js/customizer.js', ['jquery', 'customize-preview' ], _S_VERSION, true );
	wp_localize_script( 'devcon-msummit2024-customizer', 'devcon_msummit2024_customizer', [
		'ajax_url' => rest_url(),
		'nonce' => wp_create_nonce('wp_rest'),
	]);
});

add_action('rest_api_init', function() {
	// AJAX call for the "Load Default Settings" button
	register_rest_route('devcon-msummit2024/v1', '/theme/load-default', [
		'methods' => 'POST',
		'callback' => function (WP_REST_Request $request) {
			do_action('customize_register', new WP_Customize_Manager());

			return [
				'success' => true,
				'message' => 'Default settings loaded successfully.'
			];
		},
		'permission_callback' => function () {
			return current_user_can('edit_theme_options');
		}
	]);
});