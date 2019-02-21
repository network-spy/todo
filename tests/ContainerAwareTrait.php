<?php

namespace App\Tests;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Trait ContainerAwareTrait
 * @package App\Tests
 */
trait ContainerAwareTrait
{
    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer(): ContainerInterface
    {
        $kernel = static::bootKernel();

        return $kernel->getContainer();
    }
}