<?php
#
# pdf_export setup page
#

// Do the include and authorization checking ritual -- don't change this section.
include '../../../include/db.php';
include_once '../../../include/general.php';
include '../../../include/authenticate.php'; if (!checkperm('a')) {exit ($lang['error-permissiondenied']);}

// Specify the name of this plugin and the heading to display for the page.
$plugin_name = 'pdf_export';
$plugin_page_heading = $lang['pdf_export_configuration'];

// Build the $page_def array of descriptions of each configuration variable the plugin uses.

//$page_def[] = config_add_text_list_input('pdf_export_ext_exclude', $lang['extensions_to_exclude']);
$page_def[] = config_add_multi_rtype_select('pdf_export_rt_exclude', $lang['resource_types_to_exclude']);
$page_def[] = config_add_text_input('pdf_export_ttf_header_font_path', $lang["pdf_export_ttf_header_font_path"]);
$page_def[] = config_add_text_input('pdf_export_ttf_list_font_path', $lang["pdf_export_ttf_list_font_path"]);
//$page_def[] = config_add_boolean_select('pdf_export_debug', $lang['pdf_exportdebug']);
$page_def[] = config_add_text_input('pdf_export_logo_url', $lang["pdf_export_logo_url"]);
$page_def[] = config_add_text_input('pdf_export_fields_include', $lang["pdf_export_fields_include"]);
$page_def[] = config_add_text_input('pdf_export_imagesizeid', $lang["pdf_export_imagesizeid"]);
// Integrate the whereabouts plugin
if (isset($whereabouts_rt_exclude)) {
$page_def[] = config_add_boolean_select('pdf_export_whereabouts_integration', $lang['pdf_export_whereabouts_integration']);
}

// Do the page generation ritual -- don't change this section.
$upload_status = config_gen_setup_post($page_def, $plugin_name);
include '../../../include/header.php';
config_gen_setup_html($page_def, $plugin_name, $upload_status, $plugin_page_heading);
include '../../../include/footer.php';
