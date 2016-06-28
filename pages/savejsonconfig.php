<?php
//error_log(getcwd() . "\n");
// Do the include and authorization checking ritual -- don't change this section.
include '../../../include/db.php';
include_once '../../../include/general.php';
include '../../../include/authenticate.php'; if (!checkperm('a')) {exit ($lang['error-permissiondenied']);}

global $storageurl;
global $storagedir;
global $baseurl_short;
$myconfig=$_POST['mydata'];
//$myconfig = json_decode($_POST['data'], true);
$filename =$_POST['configname'];


if (!file_exists("../../../filestore/pdf_export/jsonconfigs")){ mkdir("../../../filestore/pdf_export/jsonconfigs",0755);}

file_put_contents("../../../filestore/pdf_export/jsonconfigs/".$filename .".json",$myconfig);