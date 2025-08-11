<?php

namespace VdubDev\CaptchaHandler\Domain\AntiSpam;

interface ChallengeInterface
{
    public function generateKey(): string;

    public function verify(string $key, string $answer): bool;

    public function getAnswer(string $key): mixed;
}
