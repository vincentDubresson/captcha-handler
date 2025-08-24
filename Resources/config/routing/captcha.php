<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use VdubDev\CaptchaHandler\Controller\CaptchaController;

return function (RoutingConfigurator $routes) {
    $routes->add('captcha', '/_captcha')
        ->controller([CaptchaController::class, 'captcha'])
    ;

    $routes->add('captcha_check', '/_captcha/check')
        ->controller([CaptchaController::class, 'captchaCheck'])
    ;
};
