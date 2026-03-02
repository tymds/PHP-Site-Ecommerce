<?php
return [
    'GET' => [
        '/products' => ['ProductController', 'index'],
    ],
    'POST' => [
        '/products/create' => ['ProductController', 'store'],
    ]
];