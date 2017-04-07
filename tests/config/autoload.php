<?php require __DIR__ . '/../../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

$capsule = \Corcel\Database::connect($params = [
    'database' => getenv('DATABASE') ?? 'corel',
    'username' => getenv('DATABASE_USER') ?? 'corel',
    'password' => getenv('DATABASE_PASSWORD') ?? 'corel',
    'host' => getenv('DATABASE_HOST') ?? '127.0.0.1'
]);

$capsule->addConnection(array_merge($params, [
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => 'wp_',
]), 'wordpress');
