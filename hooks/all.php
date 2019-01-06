<?php function HookPdf_exportAllRender_actions_add_collection_option($top_actions,$options){
	// global $lang,$pagename,$pdf_output,$pdf_output_only_annotated,$baseurl_short,$collection,$count_result;
// 	$selectedcoll = '';
// 	foreach ($options as $theoption){
// 		if (isset($theoption['extra_tag_attributes'])){
// 			if (preg_match("/\/pages\/collection_log\.php\?collection=(\\d+)/",$theoption['extra_tag_attributes'],$thiscoll)){
// 				$selectedcoll = $thiscoll[1];
// 			}
// 		}
// 	}
	$c=count($options);
	$colreff = $collection;
	if ($pdf_output || $count_result!=0){
		$data_attribute['url'] = sprintf('%splugins/pdf_export/pages/pdf_export_config.php?col=%s',
            $baseurl_short,
            urlencode($colreff)
        );
        $options[$c]['value']='pdf_export';
		$options[$c]['label']=$lang['pdfexportwithnotes'];
		$options[$c]['data_attr']=$data_attribute;
		
		return $options;
	}
}



?>