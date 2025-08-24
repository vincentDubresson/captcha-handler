<?php

namespace VdubDev\CaptchaHandler\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class CaptchaHandlerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('captcha_handler.assets.prefix_image_path', $config['assets']['prefix_image_path']);
        $container->setParameter('captcha_handler.assets.puzzle_path', $config['assets']['puzzle_path']);

        $container->setParameter('captcha_handler.dimensions.image_width', $config['dimensions']['image_width']);
        $container->setParameter('captcha_handler.dimensions.image_height', $config['dimensions']['image_height']);
        $container->setParameter('captcha_handler.dimensions.puzzle_width', $config['dimensions']['puzzle_width']);
        $container->setParameter('captcha_handler.dimensions.puzzle_height', $config['dimensions']['puzzle_height']);
        $container->setParameter('captcha_handler.dimensions.precision', $config['dimensions']['precision']);

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../Resources/config')
        );

        $loader->load('services.yaml');
    }
}
