<?php

function clear_pdf_export_temp($ref,$uniqid){
	if ($uniqid!=""){$uniqfolder="/".$uniqid;} else {$uniqfolder="";}
	$tmpfolder=get_temp_dir(false,"pdf_export$uniqfolder");
	$jpg_path=get_pdf_export_file_path($ref,true,"jpg");
	$pdf_path=get_pdf_export_file_path($ref,true,"pdf");
	
	if (file_exists($jpg_path)){unlink($jpg_path);}
	if (file_exists($pdf_path)){unlink($pdf_path);}
	if ($uniqfolder!="" && file_exists($tmpfolder)){rmdir($tmpfolder);}
}


function get_pdf_export_file_path($ref,$getfilepath,$extension){
	global $storageurl;
	global $storagedir;
	global $scramble_key;	
	
	if (!file_exists($storagedir . "/pdf_export")){mkdir($storagedir . "/pdf_export",0777);}
	
	global $uniqid; // if setting uniqid before manual create_annotated_pdf function use
	$uniqid=getvalescaped("uniqid",$uniqid); //or if sent through a request
	if ($uniqid!=""){$uniqfolder="/".$uniqid;} else {$uniqfolder="";}
	
	$tmpfolder=get_temp_dir(!$getfilepath,"pdf_export$uniqfolder");
	$file=$tmpfolder."/$uniqid-annotations.".$extension;

	return  $file;
}
	

function create_pdf_export_pdf($ref,$is_collection=false,$size="letter",$cleanup=false,$preview=false){
	# function to create pdf of resources.
	# This leaves the pdfs and jpg previews in filestore/annotate so that they can be grabbed later.
	# $cleanup will result in a slightly different path that is not cleaned up afterwards.
	
	global $pdf_export_whereabouts_integration,$pdf_export_imagesizeid,$pdf_export_ttf_list_font_path,$pdf_export_ttf_header_font_path,$pdf_export_fields_exclude,$pdf_export_logo_url,$contact_sheet_preview_size,$pdf_output_only_annotated,$lang,$userfullname,$view_title_field,$baseurl,$imagemagick_path,$imagemagick_colorspace,$ghostscript_path,$previewpage,$storagedir,$storageurl,$pdf_export_font,$access,$k;
	$date= date("m-d-Y h:i a");
	
	include_once($storagedir.'/../include/search_functions.php');
	include_once($storagedir.'/../include/resource_functions.php');
	include_once($storagedir.'/../include/collections_functions.php');
	include_once($storagedir.'/../include/image_processing.php');
	include_once($storagedir.'/../lib/tcpdf/tcpdf.php');

	$pdfstoragepath=get_pdf_export_file_path($ref,true,"pdf");
	$jpgstoragepath=get_pdf_export_file_path($ref,true,"jpg");
	$pdfhttppath=get_pdf_export_file_path($ref,false,"pdf");
	$jpghttppath=get_pdf_export_file_path($ref,false,"jpg");
	
	class MYPDF extends TCPDF {
		
		public function MultiRow($left, $right) {
			
			$page_start = $this->getPage();
			$y_start = $this->GetY();
		
		
			// write the left cell
			$this->MultiCell(1.5, 0, $left, 'T', 'L', 1, 2, '', '', true, 0);
		
			$page_end_1 = $this->getPage();
			$y_end_1 = $this->GetY();
		
			$this->setPage($page_start);
		
			// write the right cell
			$right=str_replace("<br />","\n",$right);
			$this->MultiCell(0, 0, $right, 'T', 'L', 0, 1, $this->GetX() ,$y_start, true, 0);
		
			$page_end_2 = $this->getPage();
			$y_end_2 = $this->GetY();
		
			// set the new row position by case
			if (max($page_end_1,$page_end_2) == $page_start) {
				$ynew = max($y_end_1, $y_end_2);
			} elseif ($page_end_1 == $page_end_2) {
				$ynew = max($y_end_1, $y_end_2);
			} elseif ($page_end_1 > $page_end_2) {
				$ynew = $y_end_1;
			} else {
				$ynew = $y_end_2;
			}
			
			$this->setPage(max($page_end_1,$page_end_2));
			$this->SetXY($this->GetX(),$ynew);
		}

	}
	if ($is_collection){
		$collectiondata=get_collection($ref);
		$resources=do_search("!collection$ref");
	} 
	else { 
		$resourcedata=get_resource_data($ref);
		$resources= do_search("!list$ref");
	}

	if (count($resources)==0){echo "nothing"; exit();}
	if ($size == "a4") {$width=210/25.4;$height=297/25.4;} // convert to inches
	if ($size == "a3") {$width=297/25.4;$height=420/25.4;}
	if ($size == "letter") {$width=8.5;$height=11;}
	if ($size == "legal") {$width=8.5;$height=14;}
	if ($size == "tabloid") {$width=11;$height=17;}

	#configuring the sheet:
	$pagewidth=$pagesize[0]=$width;
	$pageheight=$pagesize[1]=$height;
	
	$pdf = new MYPDF("portrait", "in", $size, true, 'UTF-8', false);
	
	if ($pdf_export_ttf_header_font_path) {	
	$ttf_header_font = $pdf->addTTFfont($_SERVER["DOCUMENT_ROOT"].'/'.$pdf_export_ttf_header_font_path, 'TrueTypeUnicode', '', 32);
	$pdf->SetFont($ttf_header_font, '', 15);
	}
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor($userfullname);
	if ($is_collection){ $pdf->SetTitle(i18n_get_collection_name($collectiondata).' '.$date);}
	else { $pdf->SetTitle(i18n_get_translated($resourcedata['field'.$view_title_field]).' '.$date);}
	$pdf->SetSubject($lang['pdf_exports']);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->setMargins(.5,.5,.5);
	$excludearr=explode(",",$pdf_export_fields_exclude);
	
	$page=1;
	$totalpages=1;
	$m=1;
	do // Do the following for each pdf page
		{
		// Add a page for each resource
		for ($n=0;$n<count($resources);$n++)
			{
			$pdf->AddPage();
			$currentpdfpage=$pdf->getPage();
			$resourcedata= $resources[$n];
			$ref=$resources[$n]['ref'];
			$access=get_resource_access($resources[$n]['ref']); // feed get_resource_access the resource array rather than the ref, since access is included.
			$use_watermark=check_use_watermark();
			if ($pdf_export_imagesizeid) {
			$imagesizeid = $pdf_export_imagesizeid;
			} else {
			$imagesizeid = "hpr";
			}
			$imgpath = get_resource_path($ref,true,$imagesizeid,false,"jpg",-1,$page,$use_watermark);
			if (!file_exists($imgpath)){$imgpath=get_resource_path($ref,true,"lpr",false,"jpg",-1,$page,$use_watermark);}
			if (!file_exists($imgpath)){$imgpath=get_resource_path($ref,true,"scr",false,"jpg",-1,$page,$use_watermark);}
			if (!file_exists($imgpath)){$imgpath=get_resource_path($ref,true,"",false,"jpg",-1,$page,$use_watermark);}
			if (!file_exists($imgpath)){$imgpath=get_resource_path($ref,true,"pre",false,"jpg",-1,$page,$use_watermark);}
			if (!file_exists($imgpath))continue;
			$imagesize=getimagesize($imgpath);
			
			$whratio=$imagesize[0]/$imagesize[1];
			$hwratio=$imagesize[1]/$imagesize[0];
	
			if ($whratio<1){
			$imageheight=$height-7; // vertical images can take up half the page
			$whratio=$imagesize[0]/$imagesize[1];
			$imagewidth=$imageheight*$whratio;}
			if ($whratio>=1 || $imagewidth>$width+1){
			$imagewidth=$width-1; // horizontal images are scaled to width - 1 in
			$hwratio=$imagesize[1]/$imagesize[0];
			$imageheight=$imagewidth*$hwratio;}
			$logourl = $pdf_export_logo_url;
			$filename_from_url = parse_url($logourl);
			$logoext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);
			$logosizes = getimagesize($logourl);
			$logoextension = image_type_to_extension($logosizes[2]);
			$logowidth = ($logosizes[0]/139.5);
			$logoheight = ($logosizes[1]/139.5);
			$pdf->Image($logourl,.5,.30,$logowidth,$logoheight,$logoext);	
			$pdf->Text(.5,.8,strtoupper(i18n_get_translated($resourcedata['field'.$view_title_field])));
			if ($pdf_export_ttf_list_font_path) {
			$ttf_list_font = $pdf->addTTFfont($_SERVER["DOCUMENT_ROOT"].'/'.$pdf_export_ttf_list_font_path, 'TrueTypeUnicode', '', 32);
			$pdf->SetFont($ttf_list_font, '', 10);
			}
			$pdf->Text(.5,1.1,$date);
			//$pdf->Image($imgpath,((($width-1)/2)-($imagewidth-1)/2),1.5,$imagewidth,$imageheight,"jpg",$baseurl. '/?r=' . $ref);	
			$pdf->Image($imgpath,.5,1.4,$imagewidth,$imageheight,"jpg",$baseurl. '/?r=' . $ref);	
	
			// set color for background
			$pdf->SetFillColor(255, 255, 255);
			$pdf->setCellPaddings(0.01, 0.01, 0.01, 0.1);
			$style= array('width' => 0.01, 'cap' => 'butt', 'join' => 'round' ,'dash' => '0', 'color' => array(192,192,192));
			$style1 = array('width' => 0.04, 'cap' => 'butt', 'join' => 'round', 'dash' => '0', 'color' => array(255, 255, 0));
			$style2 = array('width' => 0.02, 'cap' => 'butt', 'join' => 'round', 'dash' => '3', 'color' => array(255, 0, 0));
			$ypos=$imageheight+1.6;$pdf->SetY($ypos);
			unset($notes);
			//if ($resources[$n]['annotation_count']!=0){
				$thisrefarray = get_resource_field_data($ref,false);
				if ($pdf_export_whereabouts_integration) {
				$checkwherabouts = sql_query("SHOW TABLES LIKE 'whereabouts'");
				$whereabouts = '';
				if ($checkwherabouts) {
				$whereabouts=sql_query("select mylocation AS value,'-9999999' AS ref, 'Current Location' AS title from whereabouts where ref='$ref' ORDER by date_movement DESC LIMIT 1");
				}
				if ($whereabouts) {
				$resultmerge = array_merge($thisrefarray, $whereabouts);
				}} else {
				$resultmerge = $thisrefarray;
				}
				$notepages=1; // Normally notes will all fit on one page, but may not
				//if ($whereabouts) {
				//	$pdf->MultiRow($whereabouts[0]['title'],ltrim(trim($whereabouts[0]['value']),','));
					//}
				foreach ($resultmerge as $note)
					{
					if ($note['value'] && $note['value']!=',') {
					// If the notes took us to a new page, return to the image page before marking annotation
					if($notepages>1){$pdf->setPage($currentpdfpage);}
					
					
					$pdf->SetLineStyle($style1);
					//$pdf->Rect(((($width-1)/2)-($imagewidth-1)/2)+$note_x,$note_y+1.5,$note_width,$note_height);
					//$pdf->Rect(((($width-1)/2)-($imagewidth-1)/2)+$note_x,$note_y+1.5,.1,.1,'DF',$style1,array(255,255,0));					
					$ypos=$pdf->GetY();			
					//$pdf->Text(((($width-1)/2)-($imagewidth-1)/2)+$note_x-.01,$note_y+1.49,$m,false,false,true,0,0,'L');
					
					//$pdf->SetLineStyle($style2);
					//$pdf->Rect(((($width-1)/2)-($imagewidth-1)/2)+$note_x,$note_y+1,$note_width,$note_height);					
					$pdf->SetY($ypos);
					$pdf->SetLineStyle($style);
					// If the notes went over the page, we  went back to image for annotation, so we need to return to the page with the last row of the table before adding next row
					if($notepages>1){$pdf->setPage($currentpdfpage+($notepages-1));}
					if(!in_array($note['ref'],$excludearr)){
					$pdf->MultiRow(i18n_get_translated($note['title']),ltrim(trim(i18n_get_translated($note['value'])),','));
					}
					// Check if this new table row has moved us to a new page, in which case we need to record this and go back to image page before the next annotation
					if(isset($notepos)){$lastnotepos=$notepos;}
					$notepos=$pdf->GetY();						
					if(isset($lastnotepos) && $notepos<$lastnotepos){unset($lastnotepos);$notepages++;}
					$ypos=$ypos+.5;$m++;				
					
					}
					}
						
				//}
			}
		// Check if there is another page?
		if (file_exists(get_resource_path($ref,true,"scr",false,"jpg",-1,$page+1,$use_watermark,""))) {unset($notepos);unset($lastnotepos);$totalpages++;}
		
		$page++;
		}
		while
			($page<=$totalpages);

	// reset pointer to the last page
	$pdf->lastPage();

	#Make AJAX preview?:
	if ($preview==true && isset($imagemagick_path)) 
		{
		if (file_exists($jpgstoragepath)){unlink($jpgstoragepath);}
		if (file_exists($pdfstoragepath)){unlink($pdfstoragepath);}
		echo ($pdf->GetPage()); // for paging
		$pdf->Output($pdfstoragepath,'F');
		# Set up  
		
		putenv("MAGICK_HOME=" . $imagemagick_path); 
		putenv("PATH=" . $ghostscript_path . ":" . $imagemagick_path); # Path 

        $ghostscript_fullpath = get_utility_path("ghostscript");
		

        $command = $ghostscript_fullpath . " -sDEVICE=jpeg -dFirstPage=" . $previewpage . " -o -r100 -dLastPage=" . $previewpage . " -sOutputFile=" . escapeshellarg($jpgstoragepath) . " " . escapeshellarg($pdfstoragepath);
		run_command($command);
		
		$command=$imagemagick_path . "/bin/convert";
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert.exe";}
		if (!file_exists($command)) {$command=$imagemagick_path . "/convert";}
		if (!file_exists($command)) {exit("Could not find ImageMagick 'convert' utility at location '$command'");}	
		
		$command.= " -resize $contact_sheet_preview_size -quality 90 -colorspace ".$imagemagick_colorspace." " . escapeshellarg($jpgstoragepath) ." " . escapeshellarg($jpgstoragepath);
		run_command($command);
		return true;
		}
		
	if (!$is_collection){
		$filename=$lang['pdf_exports']."-".i18n_get_translated($resourcedata["field".$view_title_field]);
	}
	else {
		$filename=$lang['pdf_exports']."-".i18n_get_collection_name($collectiondata);
	}
		
	if ($cleanup){
		// cleanup
		if (file_exists($pdfstoragepath)){unlink($pdfstoragepath);}
		if (file_exists($jpgstoragepath)){unlink($jpgstoragepath);}
		$pathinfo=pathinfo($jpgstoragepath);
		if (file_exists($pathinfo['dirname'])){rmdir($pathinfo['dirname']);}
		$pdf->Output($filename.".pdf",'D');
		}
	else {
		// in this case it's not cleaned up automatically, but rather left in place for later use of the path.
		
		$pdf->Output($pdfstoragepath,'F');
		echo $pdfhttppath;
	}
}
