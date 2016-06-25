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
	$file=$tmpfolder."/$uniqid-pdf_export.".$extension;
// wait for a half second
usleep(500000);
	return  $file;
}
	

function create_pdf_export_pdf($ref,$is_collection=false,$size="letter",$cleanup=false,$preview=false){
	# function to create pdf of resources.
	# This leaves the pdfs and jpg previews in filestore/annotate so that they can be grabbed later.
	# $cleanup will result in a slightly different path that is not cleaned up afterwards.
	
	global $pdf_export_logo_deets,$pdf_export_imgheight,$onetimenotes,$pdf_export_whereabouts_integration,$pdf_export_imagesizeid,$pdf_export_ttf_list_font_path,$pdf_export_ttf_header_font_path,$pdf_export_fields_include_hidden,$pdf_export_logo_url,$contact_sheet_preview_size,$pdf_output_only_annotated,$lang,$userfullname,$view_title_field,$baseurl,$imagemagick_path,$imagemagick_colorspace,$ghostscript_path,$previewpage,$storagedir,$storageurl,$pdf_export_font,$access,$k;
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
	$onetimenotes=getvalescaped("onetimenotes","");
	if(function_exists("ssrscm")){
    $cprealref=ssrscm($ref);
	} else {
	$cprealref = $ref;
	}
	
	class MYPDF extends TCPDF {

		public function MultiRow($left, $right) {
			
			$page_start = $this->getPage();
			$y_start = $this->GetY();			
			
			$borderstyle='T';
			$this->MultiCell(1.5, 0, $left, $borderstyle, 'L', 1, 2, '', '', true, 0);
		
			$page_end_1 = $this->getPage();
			$y_end_1 = $this->GetY();
		
			$this->setPage($page_start);
		
			// write the right cell
			$right=str_replace("<br />","\n",$right);
			$this->MultiCell(0, 0, $right, $borderstyle, 'L', 0, 1, $this->GetX() ,$y_start, true, 0);
		
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
	$selectedconfig=getvalescaped("configname","");
	if ($selectedconfig!="") {
	$configfile = file_get_contents('../../../filestore/pdf_export/jsonconfigs/'. $selectedconfig);
	$configarray = json_decode($configfile, true);
	} else {
	$configarray="";
	}
	if ($configarray!="") {
	$ttfheaderfontvar = $configarray[0]['value'];
	$ttflistfontvar = $configarray[1]['value'];
	$logourlvar = $configarray[2]['value'];
	$logodeetsvar=$configarray[3]['value'];
	$imagesizeidvar = $configarray[4]['value'];
	$pdf_export_imgheight = $configarray[5]['value'];
	$exportfieldslistvar = $configarray[6]['value'];
	} else {
	$ttfheaderfontvar = $pdf_export_ttf_header_font_path;
	$ttflistfontvar = $pdf_export_ttf_list_font_path;
	$logourlvar = $pdf_export_logo_url;
	$logodeetsvar=$pdf_export_logo_deets;
	$pdf_export_imgheight=$pdf_export_imgheight;
	$imagesizeidvar = $pdf_export_imagesizeid;
	$exportfieldslistvar = $pdf_export_fields_include_hidden;
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
	
	$logodeetsarr=explode(",",$logodeetsvar);
	if ($logodeetsarr[0]) {
	$mylogoleft = $logodeetsarr[0];
	} else {
	$mylogoleft=0;
	}
	if ($logodeetsarr[1]) {
	$mylogotop = $logodeetsarr[1];
	} else {
	$mylogotop=0;
	}	
	if ($logodeetsarr[2]) {
	$mylogowidth = $logodeetsarr[2];
	} else {
	$mylogowidth=0;
	}
	if ($logodeetsarr[3]) {
	$mylogoheight = $logodeetsarr[3];
	} else {
	$mylogoheight=0;
	}
	$includearr=explode(",",$exportfieldslistvar);
	
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
			if ($imagesizeidvar) {
			$imagesizeid = $imagesizeidvar;
			} else {
			$imagesizeid = "hpr";
			}
			
			$imgpath = get_resource_path($cprealref,true,$imagesizeid,false,"jpg",-1,$page,$use_watermark);
			if (!file_exists($imgpath)){$imgpath=get_resource_path($cprealref,true,"lpr",false,"jpg",-1,$page,$use_watermark);}
			if (!file_exists($imgpath)){$imgpath=get_resource_path($cprealref,true,"scr",false,"jpg",-1,$page,$use_watermark);}
			if (!file_exists($imgpath)){$imgpath=get_resource_path($cprealref,true,"",false,"jpg",-1,$page,$use_watermark);}
			if (!file_exists($imgpath)){$imgpath=get_resource_path($cprealref,true,"pre",false,"jpg",-1,$page,$use_watermark);}
			if (!file_exists($imgpath))continue;
			$imagesize=getimagesize($imgpath);
			
			
			$whratio=$imagesize[0]/$imagesize[1];
			$hwratio=$imagesize[1]/$imagesize[0];
	
			if ($whratio<1){
			$imageheight=$pdf_export_imgheight; // height variable
			$whratio=$imagesize[0]/$imagesize[1];
			$imagewidth=$imageheight*$whratio;}
			if ($whratio>=1 || $imagewidth>$width+1){
			$imagewidth=$width-1; // horizontal images are scaled to width - 1 in
			$hwratio=$imagesize[1]/$imagesize[0];
			$imageheight=$imagewidth*$hwratio;}
			$logourl = $logourlvar;
			$filename_from_url = parse_url($logourl);
			$logoext = pathinfo($filename_from_url['path'], PATHINFO_EXTENSION);
			if (($logoext != 'svg') && ($logourl !='')) {
			$logosizes = getimagesize($logourl);
			$logoextension = image_type_to_extension($logosizes[2]);
			$logowidth = ($logosizes[0]/139.5);
			$logoheight = ($logosizes[1]/139.5);
			}
			if ($logourl !='') {
			if ($logoext == 'svg') { 
			$pdf->ImageSVG($logourl,$mylogoleft,$mylogotop,$mylogowidth,$mylogoheight,'','','',0,true);
			} else {
			$pdf->Image($logourl,$mylogoleft,$mylogotop,$mylogowidth,$mylogoheight,$logoext);
			}}
			$logofinalY = $pdf->getImageRBY();
			if ($ttfheaderfontvar) {	
			$ttf_header_font = $pdf->addTTFfont('../../../'.$ttfheaderfontvar, 'TrueTypeUnicode', '', 32);
			$pdf->SetFont($ttf_header_font, '', 15);
			} else {
			$pdf->SetFont('helvetica', 'B', 15,'',false);
			}	
			$ypos=$pdf->GetY();									
			$righttitle=str_replace("\\r\\n","\n",strtoupper(i18n_get_translated($resourcedata['field'.$view_title_field])));
			$pdf->MultiCell(0,0, $righttitle, 0, 'L', 0, 1,.45,$ypos+$logofinalY, true, 0,false,false);		
			// store current object
			$pdf->startTransaction();
			// get the number of lines for multicell
			$lines = $pdf->MultiCell(0,0, $righttitle, 0, 'L', 0, 1,.45,$ypos+$logofinalY, true, 0,false,false);		
			// restore previous object
			$pdf = $pdf->rollbackTransaction();
			if ($ttflistfontvar) {
			$ttf_list_font = $pdf->addTTFfont('../../../'.$ttflistfontvar,'','','','',3,1,false,false);
			$pdf->SetFont($ttf_list_font, '', 10);
			}  else {
			$pdf->SetFont('helvetica', '', 10,'',false);
			}
			$titleheight = (($lines*0.20833333333334));
			$ypos=$logofinalY+.5+$titleheight+.5;$pdf->SetY($ypos);
			$pdf->Image($imgpath,.5,$ypos,$imagewidth,$imageheight,"jpg",$baseurl. '/?r=' . $ref);	
			// set color for background
			$pdf->SetFillColor(255, 255, 255);
			$pdf->setCellPaddings(0.01, 0.06, 0.01, 0.1);
			$style= array('width' => 0.01, 'cap' => 'butt', 'join' => 'round' ,'dash' => '0', 'color' => array(192,192,192));
			$style1 = array('width' => 0.02, 'cap' => 'butt', 'join' => 'round', 'dash' => '0', 'color' => array(0, 0, 0));
			$style2 = array('width' => 0.02, 'cap' => 'butt', 'join' => 'round', 'dash' => '3', 'color' => array(255, 0, 0));
			
			$ypos=$imageheight+($titleheight)+($logofinalY)+.5;$pdf->SetY($ypos);
			
			unset($notes);
				if ($pdf_export_whereabouts_integration) {
				$checkwherabouts = sql_query("SHOW TABLES LIKE 'whereabouts'");
				if ($checkwherabouts) {
				$whereabouts=sql_query("select mylocation AS value,'-9999999' AS ref, 'Current Location' AS title from whereabouts where ref='$ref' ORDER by date_movement DESC LIMIT 1");
				}
				} else {
				$whereabouts = false;
				}
				$notepages=1; // Normally notes will all fit on one page, but may not
				if ($onetimenotes) {
				$pdf->SetLineStyle($style1);
				$ypos=$pdf->GetY();									
				$pdf->SetY($ypos+($titleheight/$lines)+.6);
				$pdf->MultiRow($lang["onetimenotes"],str_replace("\\r\\n","\n",$onetimenotes));
				$ypos=$pdf->GetY();									
				$pdf->SetY($ypos);
				$pdf->Line(.5,$ypos,$pdf->getPageWidth()-.5,$ypos);
				} else {
				$pdf->SetY($ypos+($titleheight/$lines)+.6);
				}
				foreach ($includearr as $include) {
					$fieldsf = get_field($include);
					// If the notes took us to a new page, return to the image page before marking annotation
					//if($notepages>1){$pdf->setPage($currentpdfpage);}
										
					$ypos=$pdf->GetY();			
										
					$pdf->SetY($ypos);
					$pdf->SetLineStyle($style);
					// If the notes went over the page, we  went back to image for annotation, so we need to return to the page with the last row of the table before adding next row
					//if($notepages>1){$pdf->setPage($currentpdfpage+($notepages-1));}
					if (($whereabouts)&&($include =='w')) {
					$pdf->MultiRow($whereabouts[0]['title'],ltrim(trim($whereabouts[0]['value']),','));
					} else {
					if (get_data_by_field ($ref, $include) && get_data_by_field ($ref, $include)!=',') {
					$pdf->MultiRow(i18n_get_translated($fieldsf["title"]),ltrim(trim(i18n_get_translated(get_data_by_field ($ref, $include))),','));
					}
					}
					// Check if this new table row has moved us to a new page, in which case we need to record this and go back to image page before the next annotation
					if(isset($notepos)){$lastnotepos=$notepos;}
					$notepos=$pdf->GetY();						
					if(isset($lastnotepos) && $notepos<$lastnotepos){unset($lastnotepos);$notepages++;}
					$ypos=$ypos+.5;$m++;				
					
					}
						
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
