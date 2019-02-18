<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;

/**
 * Class TaskController
 * @package App\Controller
 * @RouteResource("Task", pluralize=false)
 */
class TaskController extends FOSRestController implements ClassResourceInterface
{
    public function postAction()
    {

    }
}
