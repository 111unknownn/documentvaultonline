<?php
    function bytesToSize($bytes){
        if($bytes === 0) return "0 Byte";

        $k = 1024;
        $sizes = ['Bytes','KB', 'MB', 'GB', 'TB'];

        $i = floor(log($bytes, $k));
        $size = round($bytes / pow($k, $i), 2);

        return $size . ' ' . $sizes[$i];
    }
?>