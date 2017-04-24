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

if (!function_exists('api_verify')) {
    function api_verify($secret, $timestamp, $key)
    {
        $time = new DateTime('now', new DateTimeZone('PRC'));
        if ($time->getTimestamp() - $timestamp > 12 * 3600) {
            return false;
        }
        if (md5($key . $timestamp) !== $secret) {
            return false;
        }
        return true;
    }
}