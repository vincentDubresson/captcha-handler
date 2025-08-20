<?php

namespace VdubDev\CaptchaHandler;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CaptchaHandlerBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
