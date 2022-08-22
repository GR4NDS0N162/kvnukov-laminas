<?php

namespace Messenger\Factory;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Messenger\Controller\DialogListController;
use Messenger\Model\DialogRepositoryInterface;

class DialogListControllerFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new DialogListController(
            $container->get(DialogRepositoryInterface::class)
        );
    }
}