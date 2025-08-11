<?php

namespace VdubDev\CaptchaHandler\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class CaptchaHandlerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('captcha_handler.default_image.image_path', $config['default_image']['image_path']);
        $container->setParameter('captcha_handler.default_image.puzzle_path', $config['default_image']['puzzle_path']);

        $container->setParameter('captcha_handler.dimensions.image_width', $config['dimensions']['image_width']);
        $container->setParameter('captcha_handler.dimensions.image_height', $config['dimensions']['image_height']);
        $container->setParameter('captcha_handler.dimensions.puzzle_width', $config['dimensions']['puzzle_width']);
        $container->setParameter('captcha_handler.dimensions.puzzle_height', $config['dimensions']['puzzle_height']);
    }
}
