<?php

return [
    'POST' => [
        '/register' => ['AuthController', 'register'],
        '/login' => ["AuthController", "login"],
    ]
]