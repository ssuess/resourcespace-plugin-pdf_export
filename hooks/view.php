<?php


function HookPdf_exportViewAfterresourceactions(){
	global $baseurl_short,$ajax,$ref,$ffmpeg_preview_extension,$resource,$k,$search,$offset,$order_by,$sort,$archive,$lang,$download_multisize,$baseurl,$pdf_export_ext_exclude,$pdf_export_rt_exclude,$pdf_export_public_view,$pdf_export_pdf_output;

if (in_array($resource['file_extension'],$pdf_export_ext_exclude)){return false;}
if (in_array($resource['resource_type'],$pdf_export_rt_exclude)){return false;}

if (!($k=="") && !$pdf_export_public_view){return false;}

if (!in_array($resource["resource_type"],$pdf_export_rt_exclude)){ ?>
<li><a style="display:inline;float:right;" class="nowrap" href="<?php echo $baseurl_short?>plugins/pdf_export/pages/pdf_export_config.php?ref=<?php echo $ref?>&ext=<?php echo $resource["preview_extension"]?>&k=<?php echo $k?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>" onClick="return CentralSpaceLoad(this);">&gt;&nbsp;<?php echo $lang["pdfexportwithnotes"]?></a></li>
<?php }	
return true;	
}

?>
