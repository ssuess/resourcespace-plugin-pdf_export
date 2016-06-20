<?php
# Francais
# Language File for the pdf_export Plugin
# -------
#
#
$lang['pdf_exports']="PDF";
$lang['pdfexportwithnotes']="PDF Export";
$lang["pdf_exportpdfconfig"]="PDF Export Configuration";
$lang["pdf_exportpdfintrotext"]="Choisissez la taille de page pour votre PDF.<br /><br />Vous pouvez configurer d'autres options <a href='/plugins/pdf_export/pages/setup.php'>ici</a>.";
if (file_exists($_SERVER["DOCUMENT_ROOT"].'/lib/tcpdf/composer.json')) {
$versionstrfile = file_get_contents($_SERVER["DOCUMENT_ROOT"].'/lib/tcpdf/composer.json');
$jsonarray = json_decode($versionstrfile, true);
$versionstring = $jsonarray['version'];
} else {
$versionstring = 99999999;
}
if (version_compare($versionstring, '6.1.0', '>=')) {
$lang["pdf_export_logo_url"]="Logo/Image d'en-tête pour le PDF (URL)<br />Soutien de jpg, png, et svg<br /><br />";
} else {
$lang["pdf_export_logo_url"]="Logo/Image d'en-tête pour le PDF (URL)<br />Soutien de jpg et png<br /><br />";
}
$lang["pdf_export_fields_include"]="Champs de la base de données à inclure<br />(utilisez ref IDs de <a href='/pages/admin/admin_resource_type_fields.php'>métadonnées</a>, séparées par des virgules et dans l'ordre que vous voulez qu'ils apparaissent)<br /><br />";
$lang["pdf_export_ttf_header_font_path"]="Police en-tete - convertir un fichier ttf<br />(chemin relatif à webroot)<br /><br />";
$lang["pdf_export_ttf_list_font_path"]="Police de liste - convertir un fichier ttf<br />(chemin relatif à webroot)<br /><br />";
$lang["pdf_export_imagesizeid"]="Taille de l'image à utiliser<br />(utilisez ref IDs de <a href='/pages/admin/admin_size_management.php'>'Manage Sizes'</a>)<br /><br />";
$lang['pdf_export_whereabouts_integration']="whereabouts plugin integration?<br/>(includes current whereabouts in list, use 'w' as include field ID in list above)<br /><br />";

$lang["pdf_export_configuration"]="PDF Export Options";
$lang["extensions_to_exclude"]="Extensions to exclude<br />(séparées par des virgules):";
$lang["resource_types_to_exclude"]="Resource Types à exclure<br />(case à cocher):";
$lang["pdf_exportdebug"]="Debug:";
$lang["onetimenotes"]="PDF notes";
$lang["onetimenotesdesc"]="Enter notes that will show above other fields (only on this export, not saved)";