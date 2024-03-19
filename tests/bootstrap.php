<?php

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Filesystem\Filesystem;

require dirname(__DIR__).'/vendor/autoload.php';

if (file_exists(dirname(__DIR__).'/config/bootstrap.php')) {
    require dirname(__DIR__).'/config/bootstrap.php';
} elseif (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

// recreate and migrate DB
if (getenv('FRESH_DB')) {
    $kernel = new App\Kernel('test', true);
    $kernel->boot();

    $application = new Application($kernel);
    $application->setAutoExit(false);
    $application->run(new ArrayInput(['command' => 'app:db:fresh']));

    $kernel->shutdown();
}

// reset documentation
$filesystem = new Filesystem();
$filesystem->remove('api_documentation/');
mkdir('api_documentation');
