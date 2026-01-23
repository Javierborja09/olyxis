<?php
return [
    'GET' => [
        '/' => 'HomeController@index',
        '/about' => 'HomeController@about',
        '/contact' => 'HomeController@contact',
    ],
    'POST' => [
        '/contact' => 'HomeController@contactSubmit',
    ]
];