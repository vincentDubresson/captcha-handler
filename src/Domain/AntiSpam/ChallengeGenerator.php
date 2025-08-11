<?php

namespace VdubDev\CaptchaHandler\Domain\AntiSpam;

use Symfony\Component\HttpFoundation\Response;

interface ChallengeGenerator
{
    public function generate(string $key): Response;
}
