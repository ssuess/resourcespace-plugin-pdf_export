<?php
# English
# Language File for the pdf_export Plugin
# -------
#
#
$lang['pdf_exports']="PDF";
$lang['pdfexportwithnotes']="PDF Export";
$lang["pdf_exportpdfconfig"]="PDF Export Configuration";
$lang["pdf_exportpdfintrotext"]="Select the page size for your PDF.";
$lang["pdf_exportpdfconfiglink"] ="Configure other options <a href='/plugins/pdf_export/pages/setup.php'>here</a>.";
$lang["pdf_export_chooseconfig"]="Select a config to use: ";
$lang["pdf_export_logo_url"]="PDF header image (URL)<br />Supports jpg, png, and svg<br /><br />";
$lang["pdf_export_logo_deets"]="Header image: Left, Top, Width and Height in inches (numeric and comma separated, use 0 for auto calc of width/height)<br /><br />";
$lang["pdf_export_exclude_title"]="Exclude file title from top of page.<br /><br />";
$lang["pdf_export_fields_include"]="Fields to include in the export, in order<br /><br />";
$lang["pdf_export_fields_include_hidden"]="This field just shows the IDs (see <a href='/pages/admin/admin_resource_type_fields.php'>here</a>) from fields you chose above. You can type or rearrange them by hand here to change the order above.<br /><br />";
$lang["pdf_export_ttf_header_font_path"]="Header font conversion from ttf (filepath)<br />(relative to webroot)<br /><br />";
$lang["pdf_export_ttf_list_font_path"]="List font conversion from ttf (filepath)<br />(relative to webroot)<br /><br />";
$lang["pdf_export_imagesizeid"]="Image size to use<br />(use ref ID of <a href='/pages/admin/admin_size_management.php'>preview size</a>)<br /><br />";
$lang["pdf_export_imgheight"]="Image height on page (inches)<br />(if image width is wider than page it will be scaled down and this value will be ignored)<br /><br />";
$lang['pdf_export_whereabouts_integration']="whereabouts plugin integration?<br/>(includes current whereabouts in list, use 'w' as include field ID in list above)<br /><br />";
$lang["pdf_export_configuration"]="PDF Export Options";
$lang["extensions_to_exclude"]="Extensions to exclude<br />(comma separated):";
$lang["resource_types_to_exclude"]="Resource Types to exclude<br />(tick to exclude):";
$lang["pdf_exportdebug"]="Debug:";
$lang["onetimenotes"]="PDF notes";
$lang["onetimenotesdesc"]="Enter notes that will show above other fields (only on this export, not saved)";
$lang["pdf_export_barcode"] = "Generate Barcode?";
$lang["pdf_export_barcode_field"] = "Replace this field (ID) with barcode.<br />Barcode will be generated using string from this field.<br/><br/>";
$lang["pdf_export_barcode_type"] = "Barcode type";
$lang["pdf_export_barcode_type_choices"]=array('S25','I25','EAN8','EAN13','UPCA','CODE11','C39','C93','C128','CODABAR','MSI');


