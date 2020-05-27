<?php

define('APP_ROOT_DIR', dirname(__DIR__));

require dirname(__DIR__).'/vendor/autoload.php';

// Run database migration
passthru(sprintf(
    'php "%s/../bin/console" doctrine:migrations:migrate --no-interaction --env=test',
    __DIR__
));

// Load fixtures in test database
passthru(sprintf(
    'php "%s/../bin/console" doctrine:fixtures:load --purge-with-truncate --no-interaction --env=test',
    __DIR__
));

require __DIR__.'/../config/bootstrap.php';
