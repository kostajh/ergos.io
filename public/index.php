<?php

require '../vendor/autoload.php';

use \Needcaffeine\Slim\Extras\Views\ApiView;
use \Needcaffeine\Slim\Extras\Middleware\ApiMiddleware;
use Ergos\Ergos;

// Prepare app
$app = new \Slim\Slim(array(
  'debug' => true,
));

// Create monolog logger and store logger in container as singleton
$app->container->singleton('log', function () {
    $log = new \Monolog\Logger('ergos');
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../logs/app.log', \Monolog\Logger::DEBUG));
    return $log;
});

$app->view(new ApiView(true));
$app->add(new ApiMiddleware(true));

// Define routes
$app->get('/', function () use ($app) {
  // TODO: Return something here.
});

// 404s.
$app->notFound(function () use ($app) {
  // TODO.
});

$ergos = new Ergos($app);

// API
$app->group('/api', function () use ($app, $ergos) {
  // All tasks
  $app->get('/tasks', function() use ($app, $ergos) {
    return $ergos->getTasks();
  });
  // Pending tasks.
  $app->get('/tasks/pending', function() use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    echo $app->taskwarrior->loadTasks(null, array('status' => 'pending'), true);
  });
  // Completed tasks.
  $app->get('/tasks/completed', function() use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    echo $app->taskwarrior->loadTasks(null, array('status' => 'completed'), true);
  });
  // Deleted tasks.
  $app->get('/tasks/deleted', function() use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    echo $app->taskwarrior->loadTasks(null, array('status' => 'deleted'), true);
  });
  // Waiting tasks.
  $app->get('/tasks/waiting', function() use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    echo $app->taskwarrior->loadTasks(null, array('status' => 'waiting'), true);
  });
  // Task ID.
  $app->get('/tasks/:uuid', function($uuid) use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    echo $app->taskwarrior->loadTask($uuid, array(), true);
  });
});

// Run app
$app->run();
