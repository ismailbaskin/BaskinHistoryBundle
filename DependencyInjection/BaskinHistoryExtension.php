<?php

namespace Baskin\HistoryBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BaskinHistoryExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter(
            'baskin.history.twig_extension.class',
            'Baskin\HistoryBundle\Service\Twig\HistoryExtension'
        );
        $container->setParameter(
            'baskin.history.reverter.class',
            'Baskin\HistoryBundle\Service\Reverter'
        );

        $container->register(
            'baskin.history.twig_extension',
            $container->getParameter('baskin.history.twig_extension.class')
        )
            ->addArgument(new Reference('doctrine'))
            ->addArgument(new Reference('twig'))
            ->addArgument(new Reference('stof_doctrine_extensions.listener.loggable'))
            ->addArgument($config['template'])
            ->addArgument($config['versionParameter'])
            ->addArgument($config['revert'])
            ->addTag('twig.extension');

        if ($config['revert']) {
            $container->register(
                'baskin.history.reverter',
                $container->getParameter('baskin.history.reverter.class')
            )
                ->addArgument(new Reference('doctrine'))
                ->addArgument(new Reference('request_stack'))
                ->addArgument(new Reference('stof_doctrine_extensions.listener.loggable'))
                ->addArgument($config['versionParameter']);

            $container->setAlias('reverter', 'baskin.history.reverter');

        }
    }
}
