<?php
declare(strict_types=1);

use App\Application\Actions\User\ListUsersAction;
use App\Application\Actions\User\ViewUserAction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group;

foreach (glob(__DIR__ . '/../app/*.php') as $filename) {
    require_once($filename);
}

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    #$app->get('/', \RcloneToSqlMigrator::class . ':test');
    $app->get('/', \RcloneInterface::class . ':get_all_entries');

    $app->get('/files/{id}', \RcloneInterface::class . ':get_game_base');

    $app->get('/base', \RcloneInterface::class . ':get_all_game_base');
    $app->get('/base/{base}', \RcloneInterface::class . ':get_all_game_base');

    $app->get('/updates/{update}', \RcloneInterface::class . ':get_all_game_updates');
    $app->get('/updates', \RcloneInterface::class . ':get_all_game_updates');

    $app->get('/dlc/{dlc}', \RcloneInterface::class . ':get_all_game_dlc');
    $app->get('/dlc', \RcloneInterface::class . ':get_all_game_dlc');

    $app->get('/all', \RcloneInterface::class . ':get_all_entries');
    $app->get('/all/', \RcloneInterface::class . ':get_all_entries');
    $app->post('/', \RcloneToSqlMigrator::class . ':migrate_drive_entries_to_db');
    
};
