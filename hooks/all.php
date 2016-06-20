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

?>