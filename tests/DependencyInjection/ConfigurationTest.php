<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use VdubDev\CaptchaHandler\DependencyInjection\Configuration;

class ConfigurationTest extends TestCase
{
public function testDefaultValues()
    {
        $processor = new Processor();
        $configuration = new Configuration();

        // Passer un tableau avec un élément vide pour déclencher les valeurs par défaut
        $config = $processor->processConfiguration($configuration, [[]]);

        $this->assertSame('assets/images/default-captcha-picture.png', $config['default_image']['image_path']);
        $this->assertSame('assets/images/default-puzzle-picture.png', $config['default_image']['puzzle_path']);
        $this->assertSame(350, $config['dimensions']['image_width']);
        $this->assertSame(200, $config['dimensions']['image_height']);
        $this->assertSame(80, $config['dimensions']['puzzle_width']);
        $this->assertSame(50, $config['dimensions']['puzzle_height']);
    }

    public function testCustomValues()
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $customConfig = [
            [
                'default_image' => [
                    'image_path' => 'custom/image.png',
                    'puzzle_path' => 'custom/puzzle.png',
                ],
                'dimensions' => [
                    'image_width' => 500,
                    'image_height' => 300,
                    'puzzle_width' => 100,
                    'puzzle_height' => 70,
                ],
            ],
        ];

        $config = $processor->processConfiguration($configuration, $customConfig);

        $this->assertSame('custom/image.png', $config['default_image']['image_path']);
        $this->assertSame('custom/puzzle.png', $config['default_image']['puzzle_path']);
        $this->assertSame(500, $config['dimensions']['image_width']);
        $this->assertSame(300, $config['dimensions']['image_height']);
        $this->assertSame(100, $config['dimensions']['puzzle_width']);
        $this->assertSame(70, $config['dimensions']['puzzle_height']);
    }
}
