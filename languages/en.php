<?php
# English
# Language File for the pdf_export Plugin
# -------
#
#
$lang['pdf_exports']="PDF";
$lang['pdfexportwithnotes']="PDF Export";
$lang["pdf_exportpdfconfig"]="PDF Export Configuration";
$lang["pdf_exportpdfintrotext"]="Select the page size for your PDF.<br /><br />Configure other options <a href='/plugins/pdf_export/pages/setup.php'>here</a>.";
$lang["pdf_export_chooseconfig"]="Select a config to use: ";
if (file_exists($_SERVER["DOCUMENT_ROOT"].'/lib/tcpdf/composer.json')) {
$versionstrfile = file_get_contents($_SERVER["DOCUMENT_ROOT"].'/lib/tcpdf/composer.json');
$jsonarray = json_decode($versionstrfile, true);
$versionstring = $jsonarray['version'];
} else {
$versionstring = 99999999;
}
if (version_compare($versionstring, '6.1.0', '>=')) {
$lang["pdf_export_logo_url"]="PDF header image (URL)<br />Supports jpg, png, and svg<br /><br />";
} else {
$lang["pdf_export_logo_url"]="PDF header image (URL)<br />Supports jpg and png<br /><br />";
}
$lang["pdf_export_logo_deets"]="Header image: Left, Top, Width and Height in inches (numeric and comma separated, use 0 for auto calc of width/height)<br /><br />";


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
