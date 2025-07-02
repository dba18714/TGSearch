<?php

// 开启错误报告
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Shanghai');

// 记录脚本开始时间
$script_start_time = microtime(true);

// 设置返回内容类型为纯文本并使用 UTF-8 编码，方便在浏览器中直观查看测试结果
header('Content-Type: text/plain; charset=utf-8');

/*
 * 测试是否有并行处理请求的能力 - 原生 PHP 版本（带用时统计）
 */

// 检查是否是内部调用，防止死循环
if (isset($_GET['internal_call'])) {
    $internal_start_time = microtime(true);
    echo "这是一个内部调用，避免死循环。当前时间：" . date('Y-m-d H:i:s') . "\n";
    echo "服务器可以处理并行请求！\n";
    $internal_end_time = microtime(true);
    $internal_duration = round(($internal_end_time - $internal_start_time) * 1000, 2);
    echo "内部调用处理时间: {$internal_duration} 毫秒\n";
    exit;
}

// 获取当前请求的完整 URL（协议 + 主机 + 路径 + 查询字符串）
$scheme      = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$currentUrl  = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// 添加查询参数标识这是内部调用
$internalUrl = $currentUrl . (strpos($currentUrl, '?') !== false ? '&' : '?') . 'internal_call=1';

echo "正在测试并行处理能力...\n";
echo "当前 URL: " . $currentUrl . "\n";
echo "内部调用 URL: " . $internalUrl . "\n";
echo "测试开始时间: " . date('Y-m-d H:i:s.') . sprintf('%03d', ($script_start_time - floor($script_start_time)) * 1000) . "\n\n";

// 使用 cURL 进行 HTTP 请求
function makeHttpRequest($url, $timeout = 5) {
    $request_start_time = microtime(true);
    
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Parallel Processing Test');
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    // 获取连接和传输的详细时间信息
    $connect_time = curl_getinfo($ch, CURLINFO_CONNECT_TIME);
    $total_time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
    
    curl_close($ch);
    
    $request_end_time = microtime(true);
    $request_duration = round(($request_end_time - $request_start_time) * 1000, 2);
    
    return [
        'body' => $response,
        'status' => $httpCode,
        'error' => $error,
        'timing' => [
            'request_duration' => $request_duration,
            'connect_time' => round($connect_time * 1000, 2),
            'total_time' => round($total_time * 1000, 2)
        ]
    ];
}

try {
    echo "开始发起 HTTP 请求...\n";
    $http_start_time = microtime(true);
    
    // 访问当前页面，查看是否可以并行处理请求
    $response = makeHttpRequest($internalUrl);
    
    $http_end_time = microtime(true);
    $http_duration = round(($http_end_time - $http_start_time) * 1000, 2);
    
    if (!empty($response['error'])) {
        throw new Exception("cURL 错误: " . $response['error']);
    }
    
    echo "=== 内部调用响应 ===\n";
    echo $response['body'];
    echo "\n=== 响应状态码: " . $response['status'] . " ===\n";
    
    echo "\n=== 用时统计 ===\n";
    echo "HTTP 请求总时间: {$http_duration} 毫秒\n";
    echo "连接建立时间: " . $response['timing']['connect_time'] . " 毫秒\n";
    echo "数据传输时间: " . $response['timing']['total_time'] . " 毫秒\n";
    echo "请求处理时间: " . $response['timing']['request_duration'] . " 毫秒\n";
    
    if ($response['status'] >= 200 && $response['status'] < 300) {
        echo "\n✅ 测试成功！服务器支持并行处理请求。\n";
    } else {
        echo "\n❌ 测试失败！响应状态码: " . $response['status'] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ 发生错误: " . $e->getMessage() . "\n";
    echo "这可能意味着服务器不支持并行处理，或者存在其他问题。\n";
}

// 计算脚本总执行时间
$script_end_time = microtime(true);
$script_duration = round(($script_end_time - $script_start_time) * 1000, 2);

echo "\n";
echo "=== 系统信息 ===\n";
echo "PHP 版本: " . phpversion() . "\n";
echo "cURL 扩展: " . (extension_loaded('curl') ? "已启用" : "未启用") . "\n";
echo "当前时间: " . date('Y-m-d H:i:s') . "\n";
echo "脚本总执行时间: {$script_duration} 毫秒\n"; 