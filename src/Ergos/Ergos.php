<?php

namespace Ergos;

use LibTask\Task\Task;
use LibTask\Task\Annotation;
use LibTask\Taskwarrior;

class Ergos {

  protected $app;

  public function __construct($app) {
    $this->app = $app;
    $this->taskwarrior = new Taskwarrior();
  }

  public function getTasks($status = '') {
    $filter = array();
    if ($status) {
      $filter = array('status' => $status);
    }
    $tasks = json_decode($this->taskwarrior->loadTasks(null, $filter, true), true);
    // TODO: Handle empty result set.
    // TODO: Fix a bug in LibTask that requires us to load JSON.
    $response['notifications'][] = sprintf('Retrieved %d tasks', count($tasks));
    foreach ($tasks as $task) {
      $response['data'][] = array(
        'name' => $task['description'],
        'uuid' => $task['uuid'],
      );
    }
    $this->app->render(200, $response);
  }

  public function getTask($uuid) {
    $task = json_decode($this->taskwarrior->loadTask($uuid, array(), true), true);
    $response['data'][] = $task;
    $this->app->render(200, $response);
  }
}
