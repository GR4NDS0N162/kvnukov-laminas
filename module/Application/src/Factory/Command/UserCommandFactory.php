<?php

namespace Application\Factory\Command;

use Application\Model\Command\UserCommand;
use Application\Model\Repository\EmailRepositoryInterface;
use Application\Model\Repository\UserRepositoryInterface;
use Interop\Container\ContainerInterface;
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class UserCommandFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null)
    {
        return new UserCommand(
            $container->get(AdapterInterface::class),
            $container->get(EmailRepositoryInterface::class),
            $container->get(UserRepositoryInterface::class),
        );
    }
}