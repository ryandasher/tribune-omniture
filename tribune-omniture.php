<?php
/*
Plugin Name: Tribune Omniture
Plugin URI: http://tribpub.com
Description: A simple plugin used for configuring Omniture metrics on Tribune Publishing WordPress sites.
Author: Ryan Asher, Tribune Publishing
Version: 1.0
Author URI: http://tribpub.com
*/

class TribuneOmniture {

    public function __construct() {
        // Render our HTML on the front end.
        add_action('wp_head', array($this, 'render_omniture_HTML'));
        add_action('wp_enqueue_scripts', array($this,
                                               'render_omniture_script'));
    }


    public function render_omniture_HTML() {
        // Output the HTML of the Omniture tag itself.
        ob_start();
?>
<script>
    ((((window.trb || (window.trb = {})).data || (trb.data = {})).metrics || (trb.data.metrics = {})).thirdparty = {
        pageName: '<?php echo $this->build_pageName(); ?>',
        channel: '<?php echo get_option('tribune_omniture_section_path'); ?>',
        server: '<?php echo get_option('tribune_omniture_project_domain'); ?>',
        hier1: '<?php echo $this->build_hier1(); ?>',
        hier2: '<?php echo get_option('tribune_omniture_section_path'); ?>',
        prop1: 'D=pageName',
        prop2: '<?php echo $this->build_prop2(); ?>',
        prop38: '<?php echo get_option('tribune_omniture_project_type'); ?>',
        prop57: 'D=c38',
        eVar20: '<?php echo get_option('tribune_omniture_property_name'); ?>',
        eVar21: 'D=c38',
        eVar34: 'D=ch',
        eVar35: 'D=pageName',
        events: ''
    });
</script>
<?php
        $omniture_tag = ob_get_clean();

        echo $omniture_tag;
    }


    public function render_omniture_script() {
        // Output the HTML of the Omniture third party script.
        $property_name = get_option('tribune_omniture_property_name');

        if (get_option('tribune_omniture_enable_navigation') == 1) {
          $nav_disabled = 'false';
        } else {
          $nav_disabled = 'true';
        }

        if (get_option('tribune_omniture_enable_ssor') == 1) {
          $ssor_disabled = 'false';
        } else {
          $ssor_disabled = 'true';
        }

        $omniture_script = '//' . $property_name
            . '.com/thirdpartyservice?disablenav=' . $nav_disabled
            . '&disablessor=' . $ssor_disabled;

        wp_enqueue_script('omniture-script',
                          $omniture_script,
                          array(),
                          $var=false,
                          $in_footer=true);
    }


    public function build_page_id() {
        // Build the unique identifier for individual pages in our project.
        if (is_front_page() || is_home()) {
            $page_id = 'Home';
        } else if (is_single() || is_page()) {
            $page_id = get_the_title();
        } else if (is_search()) {
            $page_id = 'Search Results';
        } else if (is_404()) {
            $page_id = '404 Error Page';
        } else if (is_archive()) {
            if (is_tag()) {
                $page_id = single_tag_title() . ' Archives';
            } else if (is_category()) {
                $page_id = single_cat_title() . ' Archives';
            } else if (is_author()) {
                $page_id = get_the_author() . ' Archives';
            } else {
                $page_id = 'Archives';
            }
        } else {
            $page_id = 'Missing page';
        }

        $page_id = substr($page_id, 0, 40);

        return $page_id;
    }


    public function build_pageName() {
        // Build the full page name, which involves much concatenation.
        $property_abbreviation =
            get_option('tribune_omniture_property_abbreviation');
        $project_name = get_option('tribune_omniture_project_name');
        $section_path = get_option('tribune_omniture_section_path');
        $page_id = $this->build_page_id();
        $project_type = get_option('tribune_omniture_project_type');

        // Put all the pieces together.
        $pageName = $property_abbreviation . ':' . $project_name . ':'
            . $section_path . ':' . $page_id . ':' . $project_type . '.';

        return $pageName;
    }

    public function build_hier1() {
        // Build the hierarchy, which involves some concatenation.
        $hier1_property = get_option('tribune_omniture_property_name');
        $hier1_path = get_option('tribune_omniture_section_path');
        $hier1 = $hier1_property . ':' . $hier1_path;

        return $hier1;
    }

    public function build_prop2() {
        // Build the prop_2 variable by trimming our section path.
        $prop2_untrimmed = get_option('tribune_omniture_section_path');
        if (strpos($prop2_untrimmed, ':') !== false) {
            $prop2 = explode(":", $prop2_untrimmed);
            return $prop2[0];
        } else {
            return $prop2_untrimmed;
        }
    }
}

$omniture = new TribuneOmniture();

if (is_admin()){
	require_once (dirname(__FILE__).'/admin/tribune-omniture-options.php');
}

// Add settings link on plugin page
function tribune_omniture_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=tribune-omniture">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_$plugin", 'tribune_omniture_settings_link' );