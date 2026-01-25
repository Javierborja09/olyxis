<?php
require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../Framework/Core/Helpers.php';
use Framework\Core\Application;

try {
    $app = new Application();
    $app->run();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}