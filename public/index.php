<?php
require __DIR__ . '/../vendor/autoload.php';

use Framework\Core\Application;

try {
    $app = new Application();
    $app->run();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}