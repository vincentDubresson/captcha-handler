<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use VdubDev\CaptchaHandler\DependencyInjection\CaptchaHandlerExtension;

class CaptchaHandlerExtensionTest extends TestCase
{
    public function testParametersAreSet()
    {
        $container = new ContainerBuilder();
        $extension = new CaptchaHandlerExtension();

        $config = [[
            'assets' => [
                'image_path' => 'test/image.png',
                'puzzle_path' => 'test/puzzle.png',
            ],
            'dimensions' => [
                'image_width' => 400,
                'image_height' => 250,
                'puzzle_width' => 90,
                'puzzle_height' => 60,
                'precision' => 10,
            ],
        ]];

        $extension->load($config, $container);

        $this->assertSame('test/image.png', $container->getParameter('captcha_handler.assets.image_path'));
        $this->assertSame('test/puzzle.png', $container->getParameter('captcha_handler.assets.puzzle_path'));
        $this->assertSame(400, $container->getParameter('captcha_handler.dimensions.image_width'));
        $this->assertSame(250, $container->getParameter('captcha_handler.dimensions.image_height'));
        $this->assertSame(90, $container->getParameter('captcha_handler.dimensions.puzzle_width'));
        $this->assertSame(60, $container->getParameter('captcha_handler.dimensions.puzzle_height'));
        $this->assertSame(10, $container->getParameter('captcha_handler.dimensions.precision'));
    }
}
