<?php

namespace VdubDev\CaptchaHandler\Domain\AntiSpam\Puzzle;

use Intervention\Image\ImageManager;
use Symfony\Component\HttpFoundation\Response;
use VdubDev\CaptchaHandler\Domain\AntiSpam\ChallengeGeneratorInterface;

class ChallengeGenerator implements ChallengeGeneratorInterface
{
    private const MIN_IMAGE_NUMBER = 1;
    private const MAX_IMAGE_NUMBER = 3;

    public function __construct(
        private readonly PuzzleChallenge $puzzleChallenge,
        private readonly string $prefixImagePath,
        private readonly string $puzzlePath,
        private readonly int $pieceWidth,
    ) {
    }

    public function generate(string $key): Response
    {
        $position = $this->puzzleChallenge->getSolution($key);

        if (!$position) {
            return new Response(null, 404);
        }

        [$xCoordinate, $yCoordinate] = $position;
        $backgroundPath = $this->prefixImagePath . rand(self::MIN_IMAGE_NUMBER, self::MAX_IMAGE_NUMBER) . '.png';

        $piecePath = $this->puzzlePath;

        $manager = new ImageManager(['driver' => 'gd']);
        $image = $manager->make($backgroundPath);
        $piece = $manager->make($piecePath);
        $hole = clone $piece;

        $piece
            ->insert($image, 'top-left', -$xCoordinate, -$yCoordinate)
            ->mask($hole, true)
        ;

        $image
            ->resizeCanvas(
                $this->pieceWidth,
                0,
                'left',
                true,
                'rgba(0,0,0,0)'
            )
            ->insert($piece, 'top-right')
            ->insert($hole->opacity(60), 'top-left', $xCoordinate, $yCoordinate)
        ;

        /** @var Response $response */
        $response = $image->response('png');

        return $response;
    }
}
