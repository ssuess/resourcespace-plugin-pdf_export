<?php
#
# pdf_export setup page
#

// Do the include and authorization checking ritual -- don't change this section.
include '../../../include/db.php';
include_once '../../../include/general.php';
include '../../../include/authenticate.php'; if (!(checkperm('pdf') || checkperm('a'))) {exit ($lang['error-permissiondenied']);}

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
$page_def[] = config_add_text_input('pdf_export_logo_deets', $lang["pdf_export_logo_deets"]);
$page_def[] = config_add_boolean_select('pdf_export_exclude_title', $lang['pdf_export_exclude_title']);
$page_def[] = config_add_boolean_select('pdf_export_includetype_title', $lang['pdf_export_includetype_title']);
$page_def[] = config_add_text_input('pdf_export_title_array', $lang["pdf_export_title_array"]);
$page_def[] = config_add_text_input('pdf_export_imagesizeid', $lang["pdf_export_imagesizeid"]);
$page_def[] = config_add_text_input('pdf_export_imgheight', $lang["pdf_export_imgheight"]);
$page_def[] = config_add_single_select('pdf_export_imagejustify', $lang["pdf_export_imagejustify"],$lang["pdf_export_imagejustify_choices"],false);

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
if (isset($barcode_display_fields)) {
$page_def[] = config_add_boolean_select('pdf_export_barcode', $lang['pdf_export_barcode']);
$page_def[] = config_add_single_select('pdf_export_barcode_type', $lang['pdf_export_barcode_type'],$lang['pdf_export_barcode_type_choices'],false);
$page_def[] = config_add_text_input('pdf_export_barcode_field', $lang["pdf_export_barcode_field"]);
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


?><link rel="stylesheet" type="text/css" media="screen,projection,print" href="<?php echo $baseurl_short?>plugins/pdf_export/js/chosen/chosen.min.css?css_reload_key=<?php echo $css_reload_key?>"/>

<script type="text/javascript" src="<?php echo $baseurl_short?>plugins/pdf_export/js/chosen/chosen.jquery.min.js?css_reload_key=<?php echo $css_reload_key?>"></script>
<script type="text/javascript" src="<?php echo $baseurl_short?>plugins/pdf_export/js/chosen.order.jquery.min.js?css_reload_key=<?php echo $css_reload_key?>"></script>



<script  type="text/javascript">
    
jQuery(document).ready(function () {
if (jQuery('#pdfconfigwrapper').length) {
    jQuery("select#pdf_export_fields_include").chosen();
    
    // Get a reference to the DOM element
    	var myhiddenfield = jQuery('select[multiple]#pdf_export_fields_include').get(0);
    	ChosenOrder.setSelectionOrder(myhiddenfield, jQuery('#pdf_export_fields_include_hidden').val().split(','), true);

		var displayOrder = function() {
		//console.log("something changed");
        var myselection = ChosenOrder.getSelectionOrder(myhiddenfield);
        //console.log(myselection);
        jQuery('#pdf_export_fields_include_hidden').val("");
        jQuery(myselection).each(function(i) {
        jQuery('#pdf_export_fields_include_hidden').val( function( index, val ) {
    	return val + "," + myselection[i];
		});
        });
        var trimmedval = jQuery('#pdf_export_fields_include_hidden').val();
        jQuery('#pdf_export_fields_include_hidden').val(trimmedval.substring(1));


}

    jQuery('#pdf_export_fields_include').change(function() {
        setTimeout(displayOrder, 0);

    });
    

    
    //prejson.push({name: 'configname', value: confignamevalue});

    jQuery( "#jsonit" ).click(function( ) {
        var prejson = jQuery('#pdfconfigwrapper').find('input[name!="pdf_export_fields_include[]"][name!="pdf_export_rt_exclude[]"]').serializeArray();
        var titex = jQuery('#pdf_export_exclude_title').val();
        prejson.push({name:"pdf_export_exclude_title", value: titex});
        var bctrue = jQuery('#pdf_export_barcode').val();
        prejson.push({name:"pdf_export_barcode", value: bctrue});
		var titincludetype = jQuery('#pdf_export_includetype_title').val();
        prejson.push({name:"pdf_export_includetype_title", value: titincludetype});
		var imgposhoriz = jQuery('#pdf_export_imagejustify').val();
        prejson.push({name:"pdf_export_imagejustify", value: imgposhoriz});
        var bctype = jQuery('#pdf_export_barcode_type').val();
        prejson.push({name:"pdf_export_barcode_type", value: bctype});        
        var confignamevalue = jQuery('#configname').val();
    	var dataString = 'mydata=' + JSON.stringify(prejson) + '&configname='+ confignamevalue;

  //console.log(prejson);
  jQuery.ajax({
type: "POST",
url: "<?php echo $baseurl_short?>plugins/pdf_export/pages/savejsonconfig.php",
data: dataString,
cache: false,
success: function(result){
jQuery('#configname').val("");
alert("Your config was saved.");
}});
});

    
   jQuery('#pdf_export_fields_include_hidden').change(function() {
        ChosenOrder.setSelectionOrder(myhiddenfield, jQuery('#pdf_export_fields_include_hidden').val().split(','), true);
    });
    
}
    
});
</script><script  type="text/javascript">
jQuery(document).ready(function () {
    toggleFields(); //call this first so we start out with the correct visibility depending on the selected form values
    //this will call our toggleFields function every time the selection value of our underAge field changes
    jQuery("#pdf_export_barcode").change(function () {
        toggleFields();
    });

 toggleFieldstitle(); //call this first so we start out with the correct visibility depending on the selected form values
    //this will call our toggleFields function every time the selection value of our underAge field changes
    jQuery("#pdf_export_exclude_title").change(function () {
        toggleFieldstitle();
    });

});
//this toggles the visibility of our parent permission fields depending on the current selected value of the underAge field
function toggleFields() {
    if (jQuery('#pdf_export_barcode').val()==1) {
            jQuery('#pdf_export_barcode_type').parent().show();
            jQuery('#pdf_export_barcode_field').parent().show();
               } else {
            jQuery('#pdf_export_barcode_type').parent().hide();
            jQuery('#pdf_export_barcode_field').parent().hide();
            }
            
}    

function toggleFieldstitle() {
    if (jQuery('#pdf_export_exclude_title').val()==0) {
            jQuery('#pdf_export_includetype_title').parent().show();
            jQuery('#pdf_export_title_array').parent().show();
               } else {
            jQuery('#pdf_export_includetype_title').parent().hide();
            jQuery('#pdf_export_title_array').parent().hide();
            }
            
}    
</script><div id="pdfconfigwrapper"><?php
config_gen_setup_html($page_def, $plugin_name, $upload_status, $plugin_page_heading);
?><button type="button" id="jsonit">Save this config as:</button> <input id="configname" name="configname"></div><?php
include '../../../include/footer.php';