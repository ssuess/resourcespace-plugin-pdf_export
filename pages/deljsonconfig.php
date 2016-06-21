<?php

$filename = $_POST['configname'];


if (file_exists("../../../filestore/pdf_export/jsonconfigs/".$filename)){
unlink("../../../filestore/pdf_export/jsonconfigs/".$filename);
}