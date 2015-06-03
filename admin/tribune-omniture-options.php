<?php
if (is_admin()) {
    add_action('admin_menu', 'tribune_omniture_menu');
    add_action('admin_init', 'tribune_omniture_register_settings');
}

// Initialize all of our admin fields.
$tribune_omniture_fields = array(
    array(
        'Field Label' => 'Property Name',
        'Field Name' => 'tribune_omniture_property_name',
        'Description' => 'Enter the name of the affiliated property (e.g. - chicagotribune, latimes, etc.).'
    ),
    array(
        'Field Label' => 'Property Abbreviation',
        'Field Name' => 'tribune_omniture_property_abbreviation',
        'Description' => 'Enter the property abbreviation (e.g. - ct, lat, etc.).'
    ),
    array(
        'Field Label' => 'Project Name',
        'Field Name' => 'tribune_omniture_project_name',
        'Description' => 'Enter an identifying name for your project.'
    ),
    array(
        'Field Label' => 'Project Domain',
        'Field Name' => 'tribune_omniture_project_domain',
        'Description' => 'Enter the domain for your project (e.g. - projectname.latimes.com).'
    ),
    array(
        'Field Label' => 'Project Type',
        'Field Name' => 'tribune_omniture_project_type',
        'Description' => 'Enter an Omniture project type (e.g. - articleproject, dataproject, etc.).'
    ),
    array(
        'Field Label' => 'Section Path',
        'Field Name' => 'tribune_omniture_section_path',
        'Description' => 'Enter your project\'s section path (e.g. - news:local, sports:hockey, etc.).'
    )
);

function tribune_omniture_register_settings() {
    // Register our fields.
    global $tribune_omniture_fields;

    $counter = 0;

    foreach ($tribune_omniture_fields as $field) {
        register_setting('tribune_omniture_group', 
                         $tribune_omniture_fields[$counter]['Field Name']);
        $counter += 1;
    }
}


function tribune_omniture_menu() {
    // Add our options page to the menu.
    add_options_page('Tribune Omniture Options',
                     'Tribune Omniture',
                     'manage_options',
                     'tribune-omniture',
                     'tribune_omniture_options_page');
}


function tribune_omniture_options_page() {
    // Build our options page.
    global $tribune_omniture_fields;

    $counter = 0;

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
?>
<div class="wrap">
    <h2>Tribune Omniture Options</h2>
    <form method="post" action="options.php">
    <?php settings_fields('tribune_omniture_group'); ?>
        <table class="form-table">
            <?php
            foreach ($tribune_omniture_fields as $field) {
            ?>
			<tr valign="top">
				<th scope="row"><?php echo $tribune_omniture_fields[$counter]['Field Label'] ?>:</th>
				<td>
				    <input type="text" name="<?php echo $tribune_omniture_fields[$counter]['Field Name'] ?>"
					value="<?php echo get_option($tribune_omniture_fields[$counter]['Field Name']); ?>" /><br/>
					<span style="font-size: 11px;" class="description"><?php echo $tribune_omniture_fields[$counter]['Description'] ?></span>
				</td>
			</tr>
			<?php $counter += 1;
			} ?>
    	</table>
    	<p class="submit">
			<input type="submit" class="button-primary" value="Save Changes" />
		</p>
    </form>
</div>
<?php
}