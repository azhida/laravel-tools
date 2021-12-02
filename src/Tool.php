<?php

namespace Azhida\Tools;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Tool
{
    public static function resSuccessMsg($msg = '', $data = [], $meta = [])
    {
        $msg = $msg ? $msg : 'Operation success!';
        return ['code' => '0', 'msg' => $msg, 'data' => $data, 'meta' => $meta];
    }

    public static function resFailMsg($msg = '', $data = [], $meta = [], $code = '1') {
        $msg = $msg ? $msg : 'Operation failure!';
        return ['code' => $code, 'msg' => $msg, 'data' => $data, 'meta' => $meta];
    }

    public static function sha512($data, $rawOutput = false){
        if(!is_scalar($data)){
            return false;
        }
        $data = (string)$data;
        $rawOutput = !!$rawOutput;
        return hash('sha512', $data, $rawOutput);
    }

    // 生成签名
    public static function makeSign($secret, $data) {
        // 对数组的值按key排序
        ksort($data);
        // 生成url的形式
        $params = http_build_query($data);
        // 生成sign
        // $secret是通过key在api的数据库中查询得到
        $sign = md5($params . $secret);
        return $sign;
    }

    // 验证签名
    function verifySign($secret, $data, $check_timestamp = true) {
        // 验证参数中是否有签名
        if (!isset($data['sign']) || !$data['sign']) {
            return res_fail_msg('Invalid signature.'); // 签名无效
        }

        if ($check_timestamp) {
            if (!isset($data['timestamp']) || !$data['timestamp']) {
                return res_fail_msg('Parameters error!'); // 参数错误
            }
            // 验证请求， 5分钟失效
            if (time() - $data['timestamp'] > 300) {
                return res_fail_msg('Signature failure!'); // 签名失效
            }
        }

        $sign = $data['sign'];
        unset($data['sign']);
        if ($sign == make_sign($secret, $data)) {
            return res_success_msg('Ok'); // 验证通过
        } else {
            return res_fail_msg('Signature error!'); // 签名错误
        }
    }

    /**
     * @return array
     * @param $socure array 原数据[二维数组]
     * @param array $condition 查询条件[一维数组]
     * 查询二维数组中指定的 键值对
     */
    public static function fnArrayFilter($socure, array $condition) {
        return array_filter($socure, function ($value) use($condition) {
            $re = true;
            foreach ($condition as $k => $v) {
                if (!isset($value[$k]) || $value[$k] != $v) {
                    $re = false;
                    break;
                }
            }
            return $re;
        });
    }

    // 自定义日志
    public static function loggerCustom($controller_name, $function_name, $message, $context = [], $echo_only = false, $log_file_name = '') {
        $echo_message = $controller_name . '::' . $function_name . '() ' . $message . " => ";
        if (!is_array($context)) {
            $context = [$context];
        }
        if ($echo_only) return $echo_message . json_encode($context, JSON_UNESCAPED_UNICODE) . "\n";
        $message .= "\n";
//        logger($message, $context);

        $log_file = self::getLogFile($log_file_name);
        $logger = new Logger($controller_name .'::'. $function_name);
        $logger->pushHandler(new StreamHandler($log_file, Logger::INFO));
        $logger->info($message, $context);
    }

    private static function getLogFile($log_file_name = '')
    {
        if (!$log_file_name) $log_file_name = 'default';
        $log_file = '/logs/' . $log_file_name . '.log-' . date('Y-m-d');
        if (function_exists('storage_path')) {
            $log_file = storage_path() . $log_file;
        } else {
            $log_file = '.' . $log_file;
        }
        return $log_file;
    }

    /**
     * @param array $array
     * @param string $id_name
     * @param string $parent_id_name
     * @param string $children_name
     * @return array
     * 一维数组转树形结构
     */
    public static function arrayToTree(array $array = [], $id_name = 'id', $parent_id_name = 'parent_id', $children_name = 'children')
    {
        $items = [];
        foreach ($array as $value) {
            if (!isset($value[$id_name]) || !isset($value[$parent_id_name])) return [];
            if (!isset($value[$children_name])) $value[$children_name] = [];
            $items[$value[$id_name]] = $value;
        }

        $tree  =  array ();  //格式化好的树
        foreach  ( $items  as $key => $item ) {
            if  (isset( $items [ $item [$parent_id_name]])) {
                $items [ $item [$parent_id_name]][$children_name][] = & $items [ $item [$id_name]];
            } else {
                $tree [] = & $items [ $item [$id_name]];
            }
        }
        return  $tree ;
    }
}