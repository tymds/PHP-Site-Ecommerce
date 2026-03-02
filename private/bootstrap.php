<?php
declare(strict_types=1);

require_once __DIR__ . '/auth.php';

configure_runtime_security();
send_security_headers();
start_app_session();
send_no_cache_headers();
