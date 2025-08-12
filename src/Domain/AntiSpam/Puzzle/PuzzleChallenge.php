<?php

namespace VdubDev\CaptchaHandler\Domain\AntiSpam\Puzzle;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use VdubDev\CaptchaHandler\Domain\AntiSpam\ChallengeInterface;

class PuzzleChallenge implements ChallengeInterface
{
    private const SESSION_NAME = 'puzzles';
    private const REQUIRED_SOLUTION_PARTS = 2;

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly int $imageWidth,
        private readonly int $imageHeight,
        private readonly int $puzzleWidth,
        private readonly int $puzzleHeight,
        private readonly int $precision,
    ) {
    }

    public function generateChallengeKey(): string
    {
        $session = $this->getSession();
        $puzzleKey = $this->generatePuzzleKey();
        $x = mt_rand(0, $this->imageWidth - $this->puzzleWidth);
        $y = mt_rand(0, $this->imageHeight - $this->puzzleHeight);

        /**
         * @var array<int, array{key: string, solution: int[]}> $puzzles
         */
        $puzzles = $session->get(self::SESSION_NAME, []);
        $puzzles[] = ['key' => $puzzleKey, 'solution' => [$x, $y]];

        $session->set(self::SESSION_NAME, array_slice($puzzles, -10));

        return $puzzleKey;
    }

    public function verify(string $key, string $answer): bool
    {
        $expected = $this->getSolution($key);

        if (!$expected) {
            return false;
        }

        $got = $this->stringToPosition($answer);

        $session = $this->getSession();

        /**
         * @var array<int, array{key: string, solution: int[]}> $puzzles
         */
        $puzzles = $session->get(self::SESSION_NAME);

        $session->set(self::SESSION_NAME, array_filter($puzzles, fn (array $puzzle) => $puzzle['key'] !== $key));

        return (abs($expected[0] - $got[0]) <= $this->precision) && (abs($expected[1] - $got[1]) <= $this->precision);
    }

    /**
     * @return int[]|null
     */
    public function getSolution(string $key): ?array
    {
        /**
         * @var array<int, array{key: string, solution: int[]}> $puzzles
         */
        $puzzles = $this->getSession()->get(self::SESSION_NAME, []);

        foreach ($puzzles as $puzzle) {
            if ($puzzle['key'] !== $key) {
                continue;
            }

            return $puzzle['solution'];
        }

        return null;
    }

    /**
     * @return int[]
     */
    public function stringToPosition(string $s): array
    {
        $parts = explode('-', $s, 2);

        if (count($parts) !== self::REQUIRED_SOLUTION_PARTS) {
            return [-1, -1];
        }

        foreach ($parts as $part) {
            if (!is_numeric($part) || (string) (int) $part !== $part) {
                return [-1, -1];
            }
        }

        return [intval($parts[0]), intval($parts[1])];
    }

    private function getSession(): SessionInterface
    {
        /**
         * @var Request $session
         */
        $session = $this->requestStack->getMainRequest();

        return $session->getSession();
    }

    private function generatePuzzleKey(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(16)), '+/', '-_'), '=');
    }
}
