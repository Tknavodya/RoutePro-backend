<?php
require_once '../app/core/Database.php';
require_once '../app/core/Model.php';
require_once '../app/core/Controller.php';

// Autoload controllers/models
spl_autoload_register(function ($className) {
    if (file_exists("../app/controllers/$className.php")) {
        require_once "../app/controllers/$className.php";
    } elseif (file_exists("../app/models/$className.php")) {
        require_once "../app/models/$className.php";
    }
});

// Parse URL
$url = isset($_GET['url']) ? explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL)) : [];

$controllerName = isset($url[0]) && $url[0] !== '' ? ucfirst($url[0]) . 'Controller' : 'UserController';
$method = isset($url[1]) ? $url[1] : 'index';

$controller = new $controllerName();

if (method_exists($controller, $method)) {
    call_user_func_array([$controller, $method], array_slice($url, 2));
} else {
    http_response_code(404);
    echo json_encode(["error" => "Route not found"]);
}
