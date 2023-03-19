<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env.test');
}

if (isset($_ENV['BOOTSTRAP_RUN_SCRIPT'])) {
    passthru(sprintf(
        'php "%s/../bin/console" lexik:jwt:generate-keypair --skip-if-exists',
        __DIR__
    ));
}
