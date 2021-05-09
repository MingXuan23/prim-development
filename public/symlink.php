<?php
$targetFolder = $_SERVER['DOCUMENT_ROOT'].'/storage/app/public/donation-poster';
$linkFolder = $_SERVER['DOCUMENT_ROOT'].'/public_html/donation-poster';
symlink($targetFolder, $linkFolder);
echo 'Symlink completed';
