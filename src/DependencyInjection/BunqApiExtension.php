<?php

namespace Bunq\ApiBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class BunqApiExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $rootDir         = realpath($container->getParameter('kernel.root_dir') . '/../');
        $storageLocation = $rootDir . '/' . $config['storage_location'];

        $container->setParameter('bunq_storage_location', $storageLocation);
        $container->setParameter('bunq_api_key', $config['key']);
        $container->setParameter('bunq_api_uri', $config['uri']);
        $container->setParameter('bunq_api_permitted_ips', $config['permitted_ips']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
    }
}
