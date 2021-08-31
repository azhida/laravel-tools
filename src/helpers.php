<?php

if (! function_exists('res_success_msg')) {
    function res_success_msg($msg = '', $data = [], $meta = []) {
        $msg = $msg ? $msg : 'Operation success!';
        return ['code' => '0', 'msg' => $msg, 'data' => $data, 'meta' => $meta];
    }
}

if (! function_exists('res_fail_msg')) {
    function res_fail_msg($msg = '', $data = [], $meta = [], $code = '1') {
        $msg = $msg ? $msg : 'Operation failure!';
        return ['code' => $code, 'msg' => $msg, 'data' => $data, 'meta' => $meta];
    }
}

if (! function_exists('sha512')) {
    function sha512($data, $rawOutput = false){
        if(!is_scalar($data)){
            return false;
        }
        $data = (string)$data;
        $rawOutput = !!$rawOutput;
        return hash('sha512', $data, $rawOutput);
    }
}

if (! function_exists('make_sign')) {
    // 生成签名
    function make_sign($secret, $data) {
        // 对数组的值按key排序
        ksort($data);
        // 生成url的形式
        $params = http_build_query($data);
        // 生成sign
        // $secret是通过key在api的数据库中查询得到
        $sign = md5($params . $secret);
        return $sign;
    }
}

if (! function_exists('verify_sign')) {
    // 验证签名
    function verify_sign($secret, $data, $check_timestamp = true) {
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
}

if (! function_exists('fn_array_filter')) {
    /**
     * @return array
     * @param $socure array 原数据[二维数组]
     * @param array $condition 查询条件[一维数组]
     * 查询二维数组中指定的 键值对
     */
    function fn_array_filter($socure, array $condition) {
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
}

if (! function_exists('logger_custom')) {
    // 自定义日志
    function logger_custom($controller_name, $function_name, $message, $context = [], $echo_only = false) {
        $message = $controller_name . '::' . $function_name . '() ' . $message . " => ";
        if (!is_array($context)) {
            $context = [$context];
        }
        if ($echo_only) return $message . json_encode($context, JSON_UNESCAPED_UNICODE) . "\n";
        $message .= "\n";
        \Azhida\Tools\Log::loggerCustom($message, $context);
    }
}