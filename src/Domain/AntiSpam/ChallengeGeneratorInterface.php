<?php

namespace VdubDev\CaptchaHandler\Domain\AntiSpam;

use Symfony\Component\HttpFoundation\Response;

interface ChallengeGeneratorInterface
{
    public function generate(string $key): Response;
}
