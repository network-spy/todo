<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class HealthCheckerController
 * @package App\Controller
 */
class HealthCheckerController extends AbstractController
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
