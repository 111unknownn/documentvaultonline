<?php

$zip = new ZipArchive;
$res = $zip->open('../upload/encrypted_file');
if ($res === TRUE) {
    $zip->extractTo('path/to/extract');
    $zip->close();
    echo 'Zip archive successfully extracted.';
} else {
    echo 'Failed to open Zip archive.';
}
