## OWC: WP Options

### Example:

	$options = new Options( 'site_title', 'Site title', array(
		'section_1' => array(
			'title'       => 'Section 1',
			'description' => 'Section 1 description',
			'fields'      => array(
				'section_1_field_1' => array(
					'title' => 'Field 1',
					'type'  => 'text',
					'value' => ''
				),
				'section_1_field_2' => array(
					'title' => 'Field 1',
					'type'  => 'text',
					'value' => ''
				)
			)
		),
		'section_2' => array(
			'title'       => 'Section 2',
			'description' => 'Section 2 description',
			'fields'      => array(
				'section_2_field_1' => array(
					'title' => 'Field 1',
					'type'  => 'text',
					'value' => ''
				),
				'section_2_field_2' => array(
					'title' => 'Field 1',
					'type'  => 'text',
					'value' => ''
				)
			)
		)
	) );