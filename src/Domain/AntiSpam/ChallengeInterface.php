<?php

namespace VdubDev\CaptchaHandler\Domain\AntiSpam;

interface ChallengeInterface
{
    public function generateChallengeKey(): string;

    public function verify(string $key, string $answer): bool;

    public function getSolution(string $key): mixed;
}
