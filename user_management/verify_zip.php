<?php
$zipSupported = class_exists('ZipArchive');
echo $zipSupported ? 'ZipArchive is supported.' : 'ZipArchive is not supported.';
