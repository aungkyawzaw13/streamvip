<?php

$routes = [
    '/login'    => __DIR__ . '/login.php',
    '/register' => __DIR__ . '/register.php',
    '/profile' => __DIR__ . '/user/profile.php',
    '/info' => __DIR__ . '/user/info.php',
    '/wallet' => __DIR__ . '/user/wallet.php',
    '/vip' => __DIR__ . '/vipbuy/vip.php',
    '/buyvip' => __DIR__ . '/vipbuy/vip_buy.php',
    '/telegram' => __DIR__ . '/telegramtasks/tele.php',
    '/withdraw' => __DIR__ . '/Withdraws/w.php',
    '/recharge' => __DIR__ . '/recharges/recharge.php',
    '/video' => __DIR__ . '/videosss/video.php',
    '/play' => __DIR__ . '/videosss/player.php',
    '/CompanyProfile' => __DIR__ . '/company.php',
    
];

// Get clean URI
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// PROJECT FOLDER NAME
$base_dir = '/sp';

// Remove base directory from URI
if (strpos($request_uri, $base_dir) === 0) {
    $request_uri = substr($request_uri, strlen($base_dir));
}

// Fix empty URI
if ($request_uri === '') {
    $request_uri = '/';
}

// Default home
if ($request_uri === '/') {
    include __DIR__ . '/home.php';
    exit;
}

// Routes
if (isset($routes[$request_uri])) {
    include $routes[$request_uri];
    exit;

} elseif (strpos($request_uri, '/admin') === 0) {
    include __DIR__ . '/admin/index.php';
    exit;

} else {
    http_response_code(404);
    echo "404 Page Not Found";
}
