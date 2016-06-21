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
$page_def[] = config_add_text_input('pdf_export_imagesizeid', $lang["pdf_export_imagesizeid"]);

$pdf_export_full_fields_options=array();
$allfields = sql_query('SELECT ref, title FROM resource_type_field;');
foreach ($allfields as $afield) {
$pdf_export_full_fields_options[$afield['ref']] = i18n_get_translated($afield['title']);
}
$whereabouts_array = array();
$fullfieldmerge = array_merge($pdf_export_full_fields_options,$whereabouts_array);
$pdf_export_full_fields_options['w']='Whereabouts';
$page_def[] = config_add_multi_select('pdf_export_fields_include', $lang["pdf_export_fields_include"],$pdf_export_full_fields_options);
$page_def[] = config_add_text_input('pdf_export_fields_include_hidden', $lang["pdf_export_fields_include_hidden"]);

// Integrate the whereabouts plugin
if (isset($whereabouts_rt_exclude)) {
$page_def[] = config_add_boolean_select('pdf_export_whereabouts_integration', $lang['pdf_export_whereabouts_integration']);
}
// Do the page generation ritual -- don't change this section.
$upload_status = config_gen_setup_post($page_def, $plugin_name);
include '../../../include/header.php';

// Purge old config if prior to version 1.4
 function do_alert($msg) 
{
    echo '<script type="text/javascript">document.getElementById("form1").reset();alert("incompatible old config ' . $msg . ' cleared. Please put in your values anew.");</script>';
}
$versionquery = sql_query('SELECT inst_version from plugins where name="pdf_export";');
$previousversion = $versionquery[0]['inst_version'];
if (version_compare($previousversion, '1.4', '<')) {
purge_plugin_config($plugin_name);
sql_query("UPDATE plugins SET inst_version=1.4 where name='pdf_export'");
do_alert($previousversion);
//error_log('pdf_export config purged');

}


?><div id="pdfconfigwrapper"><?php
config_gen_setup_html($page_def, $plugin_name, $upload_status, $plugin_page_heading);
?><button type="button" id="jsonit">Save this config as:</button> <input id="configname" name="configname"></div><?php
include '../../../include/footer.php';