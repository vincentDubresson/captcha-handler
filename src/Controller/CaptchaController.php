<?php

namespace VdubDev\CaptchaHandler\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use VdubDev\CaptchaHandler\Domain\AntiSpam\ChallengeInterface;
use VdubDev\CaptchaHandler\Domain\AntiSpam\Puzzle\ChallengeGenerator;

class CaptchaController extends AbstractController
{
    #[Route('/_captcha', name: 'captcha')]
    public function captcha(Request $request, ChallengeGenerator $challengeGenerator): Response
    {
        return $challengeGenerator->generate((string) $request->query->get('challenge', ''));
    }

    #[Route('/_captcha/check', name: 'captcha_check', options: ['expose' => true])]
    public function captchaCheck(Request $request, ChallengeInterface $challenge): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => $challenge->verify(
                    (string) $request->query->get('challenge', ''),
                    (string) $request->query->get('answer', '')
                ),
            ],
            Response::HTTP_OK);
    }
}
