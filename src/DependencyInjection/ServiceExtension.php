<?php

declare(strict_types=1);

namespace Clearfacts\Bundle\CodestyleBundle\DependencyInjection;

use Clearfacts\Bundle\CodestyleBundle\Command\SetupGitHooksCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ServiceExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $setupGitHooksCommandDefinition = new Definition(SetupGitHooksCommand::class);
        $setupGitHooksCommandDefinition->addTag('console.command');

        $container->addDefinitions([
            $setupGitHooksCommandDefinition,
        ]);
    }
}
