<?php
include "../../../include/db.php";
include_once "../../../include/general.php";
include "../../../include/authenticate.php"; 
include("../../../include/resource_functions.php");
include_once ("../../../include/collections_functions.php");
include("../../../include/search_functions.php");
include_once("../include/general.php");

$ref=getvalescaped("ref","");
$col=getvalescaped("col","");
$previewpage=getvalescaped("previewpage",1,true);

if ($col!=""){
	$is_collection=true;
	$collection=get_collection($col);
	$resources=do_search("!collection".$col);
	set_user_collection($userref,$col);
	refresh_collection_frame();
	$ref="C".$col;$realref=$col; // C allows us to distinguish a collection from a resource in the JS without adding extra params.
} 
else { 
	$is_collection=false;
	$resources=do_search("!list".$ref);
	$realref=$ref;
}

// prune unnannotated resources if necessary
	$pdf_export=true;

	//if (count($resources)==0){$pdf_export=false;}


# Fetch search details (for next/back browsing and forwarding of search params)
$search=getvalescaped("search","");
$order_by=getvalescaped("order_by","relevance");
$offset=getvalescaped("offset",0,true);
$restypes=getvalescaped("restypes","");
if (strpos($search,"!")!==false) {$restypes="";}
$archive=getvalescaped("archive",0,true);

$default_sort_direction="DESC";
if (substr($order_by,0,5)=="field"){$default_sort_direction="ASC";}
$sort=getval("sort",$default_sort_direction);



include "../../../include/header.php";

// a unique id allows us to isolate this page's temporary files. 	
$uniqid=uniqid($ref."-");

$jpghttppath=get_pdf_export_file_path($realref,false,"jpg");

?>

<?php if ($pdf_export){?>
<script type="text/javascript" language="JavaScript">
var pdf_export_previewimage_prefix = "";

(function($) {

jQuery("a#deleteconfig").live('click', function(event) {
 event.preventDefault();
        var confignamevalue = jQuery('#configselect').val();
    	var dataString = 'configname='+ confignamevalue;

  console.log(dataString);
  jQuery.ajax({
type: "POST",
url: "<?php echo $baseurl_short?>plugins/pdf_export/pages/deljsonconfig.php",
data: dataString,
cache: false,
success: function(result){
location.reload();
}});
});
	
	 var methods = {
		
		preview : function() { 
			var url = '<?php echo $baseurl_short?>plugins/pdf_export/pages/pdf_export_gen.php';
        	var confignameval = jQuery('#configselect').val();
        	if (confignameval ==''){
  			jQuery("a#deleteconfig").hide();
  			} else {
  			jQuery("a#deleteconfig").show();
  			}

			var formdata = $('#annotateform').serialize() + '&preview=true&configname='+ confignameval; 

			$.ajax(url,{
			data: formdata,
			success: function(response) {$(this).annotate('refresh',response);},
			complete: function(response) {
				$('#error').html(response.responseText);
				if (response.responseText=="nothing"){
					$('#heading').hide();
					$('#configform').hide();
					$('#previewdiv').hide();
					$('#introtext').hide();
				} 
			},
			beforeSend: function(response) {loadIt();}
			});
		},
		
		refresh : function( pagecount ) { 

			document.previewimage.src = '<?php echo $jpghttppath;?>?'+ Math.random();
			
			if (pagecount>1){
				$('#previewPageOptions').show(); // display selector  
				pagecount++;
				curval=$('#previewpage').val();
				$('#previewpage')[0].options.length = 0;
	
				for (x=1;x<pagecount;x++){ 
					selected=false;
					var selecthtml="";
					if (x==curval){selected=true;}
					if (selected){selecthtml=' selected="selected" ';}
					$('#previewpage').append('<option value='+x+' '+selecthtml+'>'+x+'/'+(pagecount-1)+'</option>');
				}
			}
			else {
				$('#previewPageOptions').hide();
			}
			$.ajax("<?php echo $baseurl_short?>plugins/pdf_export/pages/pdf_export_gen.php?cleartmp=true&ref=<?php echo $ref?>&uniqid=<?php echo $uniqid?>&page=<?php echo $previewpage ?>",{complete: function(response){ $('#error2').html(response.responseText);}});
		},
		
		
		 
		revert : function() { 
			$('#previewpage')[0].options.length = 0;
			$('#previewpage').append(new Option(1, 1,true,true));
			$('#previewpage').value=1;$('#previewPageOptions').hide();
			$(this).annotate('preview');
		}
	};

  $.fn.annotate = function( method ) {
    
    // Method calling logic
    if ( methods[method] ) {

      return methods[ method ].apply( this, Array.prototype.slice.call( arguments, 1 ));
    }  
  
  };
	

})(jQuery) 
</script>
<script>
function loadIt() {
   document.previewimage.src = '<?php echo $baseurl_short?>gfx/images/ajax-loader-on-sheet.gif';}
</script>
<?php } ?>

<div class="BasicsBox" >

<?php if (!$is_collection){?>
<p><a href="<?php echo $baseurl_short?>pages/view.php?ref=<?php echo $ref?>&search=<?php echo urlencode($search)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>&pdf_export=true" onClick="return CentralSpaceLoad(this);">&lt;&nbsp;<?php echo $lang["backtoresourceview"]?></a></p>
<?php } else {?>
<p><a href="<?php echo $baseurl_short?>pages/search.php?search=!collection<?php echo substr($ref,1)?>&offset=<?php echo $offset?>&order_by=<?php echo $order_by?>&sort=<?php echo $sort?>&archive=<?php echo $archive?>" onClick="return CentralSpaceLoad(this);">&lt;&nbsp;<?php echo $lang["backtoresults"]?></a></p>
<?php } ?>

<h1><?php echo $lang["pdf_exportpdfconfig"]?></h1>

<?php if ($pdf_export){?>
<div id="heading" class="BasicsBox" style="float:left;margin-bottom:0;" >
<p id="introtext"><?php echo $lang["pdf_exportpdfintrotext"]?></p>
<p class="pickconfig"><?php echo $lang["pdf_export_chooseconfig"]?> <?php $file_matcher = '../../../filestore/pdf_export/jsonconfigs/*.{json}';
if (glob($file_matcher, GLOB_BRACE)){
$myselect ='';
foreach( glob($file_matcher, GLOB_BRACE) as $myfile ) {
  $file_name = basename($myfile);
  $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_name);
  $myselect .= "<option value='$file_name'>$withoutExt</option>\n";
} ?><select id="configselect" onChange="jQuery().annotate('preview');"><option value="" selected>Last Saved Config</option><?php echo $myselect; ?></select><a href="#" id="deleteconfig"><i class="fa fa-arrow-left"></i> delete config</a></p><?php } else { ?><select id="configselect" onChange="jQuery().annotate('preview');"><option value="" selected>Last Saved Config</option></select><?php }?>
</div>
<div style="clear:left;"></div>

<div id="configform" class="BasicsBox" style="width:450px;float:left;margin-top:0;" >

<form method=post name="annotateform" id="annotateform" action="<?php echo $baseurl_short?>plugins/pdf_export/pages/pdf_export_gen.php" >
<input type=hidden name="ref" value="<?php echo $ref?>">
<input type=hidden name="uniqid" value="<?php echo $uniqid?>">

<?php if ($is_collection){?>
<div class="Question">
<label><?php echo $lang["collection"]?></label><div class="Fixed"><?php echo i18n_get_collection_name($collection)?></div>
<div class="clearerleft"> </div>
</div>

<?php } else { ?>
<div class="Question">
<label><?php echo $lang["resourcetitle"]?></label><div class="Fixed"><?php echo i18n_get_translated($resources[0]['field'.$view_title_field])?></div>
<div class="clearerleft"> </div>
</div>
<?php } ?>

<div class="Question">
<label class="onetimenoteslabel"><?php echo $lang["onetimenotes"];?><br/><br/><?php echo $lang["onetimenotesdesc"];?></label><br /><br /><textarea name="onetimenotes" id="onetimenotes"></textarea>
<div class="clearerleft"> </div>
</div>

<div class="Question">
<label><?php echo $lang["size"]?></label>
<select class="shrtwidth" name="size" id="size" onChange="jQuery().annotate('preview');	"><?php echo $papersize_select ?>
</select>
<div class="clearerleft"> </div>
</div>

<div name="previewPageOptions" id="previewPageOptions" class="Question" style="display:none">
<label><?php echo $lang['previewpage']?></label>
<select class="shrtwidth" name="previewpage" id="previewpage" onChange="jQuery().annotate('preview');	">
</select>
</div>
<?php if ($pdf_export_debug){?><div name="error" id="error"></div><?php } ?>
<?php if ($pdf_export_debug){?><div name="error2" id="error2"></div><?php } ?>
<div class="QuestionSubmit">
<label for="buttons"> </label>	
<input name="preview" type="button" value="&nbsp;&nbsp;<?php echo $lang["preview"]?>&nbsp;&nbsp;" onClick="jQuery().annotate('preview');	"/>
<input name="save" type="submit" value="&nbsp;&nbsp;<?php echo $lang["create"]?>&nbsp;&nbsp;" />
</div>
</form><?php 
//$thisrefarray = get_resource_field_data ($ref,false);
//$fieldsf = get_field('73');
//echo $fieldsf["title"];
//echo get_data_by_field ($ref, '73');
//$includearr=explode(",",$pdf_export_fields_include);

//echo $fdata["value"];
//echo "<pre>";
//print_r($includearr);
//echo "</pre>";

?>
</div>

<div id="previewdiv" style="float:left;padding:0px -50px 15px 0;height:auto;margin-right:-50px;position: relative;
	top: -80px;border:1px solid silver;">
	<img id="previewimage" name="previewimage" src=''/>
</div>

<?php }
 ?>
<div <?php if ($pdf_export){?>style="display:none;"<?php } ?> id="noannotations"><?php if (!$pdf_export){?>There are no annotations.<?php } ?></div></div>
<?php if ($pdf_export){?>
<script>
	jQuery().annotate('preview');
</script>
<?php } ?>
<?php		
include "../../../include/footer.php";
?>
