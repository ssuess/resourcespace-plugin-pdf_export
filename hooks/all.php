<?php function HookPdf_exportAllRender_actions_add_collection_option($top_actions,$options){
	global $lang,$pagename,$pdf_output,$pdf_output_only_annotated,$baseurl_short,$collection,$count_result;
	
	$c=count($options);
	
	if ($pdf_output || $count_result!=0){
		$data_attribute['url'] = sprintf('%splugins/pdf_export/pages/pdf_export_config.php?col=%s',
            $baseurl_short,
            $collection
        );
        $options[$c]['value']='pdf_export';
		$options[$c]['label']=$lang['pdfexportwithnotes'];
		$options[$c]['data_attr']=$data_attribute;
		
		return $options;
	}
}

function HookPdf_exportAllAdditionalheaderjs(){
	global $baseurl,$k,$baseurl_short,$css_reload_key;
?>
<link rel="stylesheet" type="text/css" media="screen,projection,print" href="<?php echo $baseurl_short?>plugins/pdf_export/js/chosen/chosen.min.css?css_reload_key=<?php echo $css_reload_key?>"/>

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
</script>
<?php }


?>