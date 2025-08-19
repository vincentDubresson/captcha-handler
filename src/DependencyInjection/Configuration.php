<?php

namespace VdubDev\CaptchaHandler\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('captcha_handler');

        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        // Assets
        $defaultImageNode = $rootNode->children()->arrayNode('assets');
        $defaultImageNode->addDefaultsIfNotSet();

        $defaultImageChildren = $defaultImageNode->children();
        $defaultImageChildren->scalarNode('prefix_image_path')->defaultValue('%kernel.project_dir%/public/bundles/captchahandler/images/default-captcha-picture-')->end();
        $defaultImageChildren->scalarNode('puzzle_path')->defaultValue('%kernel.project_dir%/public/bundles/captchahandler/images/default-puzzle-picture.png')->end();

        // Dimensions
        $dimensionsNode = $rootNode->children()->arrayNode('dimensions');
        $dimensionsNode->addDefaultsIfNotSet();

        $dimensionsChildren = $dimensionsNode->children();
        $dimensionsChildren->integerNode('image_width')->defaultValue(350)->end();
        $dimensionsChildren->integerNode('image_height')->defaultValue(200)->end();
        $dimensionsChildren->integerNode('puzzle_width')->defaultValue(80)->end();
        $dimensionsChildren->integerNode('puzzle_height')->defaultValue(50)->end();
        $dimensionsChildren->integerNode('precision')->defaultValue(2)->end();

        return $treeBuilder;
    }
}
