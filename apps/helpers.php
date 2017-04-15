<?php
/**
 * Created by PhpStorm.
 * User: lizhipeng
 * Date: 2017/4/15
 * Time: 下午4:46
 */

if (!function_exists('mkdirs')) {
    function mkdirs($dir, $mode = 0777)
    {
        if (is_dir($dir) || @mkdir($dir, $mode)) return TRUE;
        if (!mkdirs(dirname($dir), $mode)) return FALSE;
        return @mkdir($dir, $mode);
    }
}