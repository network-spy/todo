<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;

/**
 * Class HealthCheckerController
 * @package App\Controller
 */
class HealthCheckerController extends FOSRestController
{
    /**
     * @Get(path="/")
     * @return JsonResponse
     */
    public function getAction()
    {
        return new JsonResponse(
            'alive'
        );
    }
}
