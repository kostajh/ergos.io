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

  /**
   * Load tasks matching a certain status.
   *
   * @param string $status
   *
   * @return
   *  An array of data for a particular status containing the UUID of a task
   *  and its description.
   */
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

  /**
   * Return a single task.
   *
   * @param string $uuid
   *
   * @return
   *  An array of data for a particular task, or a 404 error if not found.
   */
  public function getTask($uuid) {
    $task = json_decode($this->taskwarrior->loadTask($uuid, array(), true), true);
    if (!$task) {
      $response['notifications'][] = sprintf('Could not load task "%s"', $uuid);
      $responseCode = '404';
    }
    else {
      $responseCode = '200';
      $response['data'] = $task;
    }
    $this->app->render($responseCode, $response);
  }
}
