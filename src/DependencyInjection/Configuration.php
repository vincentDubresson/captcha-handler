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

        $children = $rootNode->children();

        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $defaultImageNode */
        $defaultImageNode = $children->arrayNode('assets');
        $defaultImageNode->addDefaultsIfNotSet();

        $defaultImageChildren = $defaultImageNode->children();
        $defaultImageChildren->scalarNode('image_path')->defaultValue('assets/images/default-captcha-picture.png')->end();
        $defaultImageChildren->scalarNode('puzzle_path')->defaultValue('assets/images/default-puzzle-picture.png')->end();
        $defaultImageChildren->end();
        $defaultImageNode->end();

        $dimensionsNode = $children->arrayNode('dimensions');
        $dimensionsNode->addDefaultsIfNotSet();

        $dimensionsChildren = $dimensionsNode->children();
        $dimensionsChildren->integerNode('image_width')->defaultValue(350)->end();
        $dimensionsChildren->integerNode('image_height')->defaultValue(200)->end();
        $dimensionsChildren->integerNode('puzzle_width')->defaultValue(80)->end();
        $dimensionsChildren->integerNode('puzzle_height')->defaultValue(50)->end();
        $dimensionsChildren->end();
        $dimensionsNode->end();

        $children->end();

        return $treeBuilder;
    }
}
