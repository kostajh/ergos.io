<?php
require '../vendor/autoload.php';
use LibTask\Task\Task;
use LibTask\Task\Annotation;
use LibTask\Taskwarrior;

// Prepare app
$app = new \Slim\Slim(array(
    'templates.path' => '../templates',
));

// Create monolog logger and store logger in container as singleton 
// (Singleton resources retrieve the same log resource definition each time)
$app->container->singleton('log', function () {
    $log = new \Monolog\Logger('slim-skeleton');
    $log->pushHandler(new \Monolog\Handler\StreamHandler('../logs/app.log', \Monolog\Logger::DEBUG));
    return $log;
});

// Prepare view
$app->view(new \Slim\Views\Twig());
$app->view->parserOptions = array(
    'charset' => 'utf-8',
    'cache' => realpath('../templates/cache'),
    'auto_reload' => true,
    'strict_variables' => false,
    'autoescape' => true
);
$app->view->parserExtensions = array(new \Slim\Views\TwigExtension());

// Define routes
$app->get('/', function () use ($app) {
    // Sample log message
    $app->log->info("Slim-Skeleton '/' route");
    // Render index view
    $app->render('index.html');
});

// API
$app->taskwarrior = new Taskwarrior();
$app->group('/api', function () use ($app) {
  // All tasks
  $app->response->headers->set('Content-Type', 'application/json');
  $app->get('/tasks', function() use ($app) {
    echo $app->taskwarrior->loadTasks(null, array(), true);
  });
  // Pending tasks.
  $app->get('/tasks/pending', function() use ($app) {
    echo $app->taskwarrior->loadTasks(null, array('status' => 'pending'), true);
  });
  // Completed tasks.
  $app->get('/tasks/completed', function() use ($app) {
    echo $app->taskwarrior->loadTasks(null, array('status' => 'completed'), true);
  });
  // Deleted tasks.
  $app->get('/tasks/deleted', function() use ($app) {
    echo $app->taskwarrior->loadTasks(null, array('status' => 'deleted'), true);
  });
  // Waiting tasks.
  $app->get('/tasks/waiting', function() use ($app) {
    echo $app->taskwarrior->loadTasks(null, array('status' => 'waiting'), true);
  });
  // Task ID.
  $app->get('/tasks/:uuid', function($uuid) use ($app) {
    $taskwarrior = new Taskwarrior();
    echo json_encode($taskwarrior->loadTask($uuid, array(), true));
  });
});

// Run app
$app->run();
