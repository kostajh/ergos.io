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
$app->container->singleton('log', function () {
    $log = new \Monolog\Logger('ergos');
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
    $app->render('index.twig');
});

// 404s.
$app->notFound(function () use ($app) {
    $app->render('404.html');
});

// API
$app->taskwarrior = new Taskwarrior();
$app->group('/api', function () use ($app) {
  // All tasks
  $app->get('/tasks', function() use ($app) {
    $app->response->headers->set('Content-Type', 'application/json');
    echo $app->taskwarrior->loadTasks(null, array(), true);
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
