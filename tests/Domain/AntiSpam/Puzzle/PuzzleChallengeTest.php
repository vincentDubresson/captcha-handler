<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use VdubDev\CaptchaHandler\Domain\AntiSpam\Puzzle\PuzzleChallenge;

class PuzzleChallengeTest extends TestCase
{
    private PuzzleChallenge $challenge;
    private $sessionData = [];

    protected function setUp(): void
    {
        // Mock session
        $session = $this->createMock(SessionInterface::class);

        // Simuler get et set sur session avec un tableau interne
        $session->method('get')
            ->willReturnCallback(fn($key, $default = null) => $this->sessionData[$key] ?? $default);

        $session->method('set')
            ->willReturnCallback(function ($key, $value) {
                $this->sessionData[$key] = $value;
            });

        // Mock Request qui renvoie la session mockée
        $request = $this->createMock(Request::class);
        $request->method('getSession')->willReturn($session);

        // Mock RequestStack qui renvoie la Request mockée
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMainRequest')->willReturn($request);

        // Instancie PuzzleChallenge avec mocks et paramètres de test
        $this->challenge = new PuzzleChallenge(
            $requestStack,
            350,
            200,
            80,
            50,
            5
        );
    }

    public function testGenerateChallengeKeyStoresPuzzle(): void
    {
        $key = $this->challenge->generateChallengeKey();

        $this->assertIsString($key);

        $puzzles = $this->sessionData['puzzles'] ?? [];

        $this->assertNotEmpty($puzzles);
        $this->assertEquals($key, $puzzles[array_key_last($puzzles)]['key']);
        $solution = $puzzles[array_key_last($puzzles)]['solution'];

        $this->assertCount(2, $solution);
        $this->assertGreaterThanOrEqual(0, $solution[0]);
        $this->assertGreaterThanOrEqual(0, $solution[1]);
        $this->assertLessThanOrEqual(350 - 80, $solution[0]);
        $this->assertLessThanOrEqual(200 - 50, $solution[1]);
    }

    public function testGetSolutionReturnsCorrectCoordinates(): void
    {
        // Préparer un puzzle en session
        $expectedSolution = [42, 84];
        $key = 'testkey123';
        $this->sessionData['puzzles'] = [['key' => $key, 'solution' => $expectedSolution]];

        $solution = $this->challenge->getSolution($key);
        $this->assertSame($expectedSolution, $solution);

        // Clé absente
        $this->assertNull($this->challenge->getSolution('nokey'));
    }

    public function testVerifyReturnsTrueForValidAnswer(): void
    {
        $key = 'validkey';
        $solution = [100, 50];
        $this->sessionData['puzzles'] = [['key' => $key, 'solution' => $solution]];

        // réponse parfaite
        $answer = '100-50';
        $this->assertTrue($this->challenge->verify($key, $answer));

        $this->sessionData['puzzles'] = [['key' => $key, 'solution' => $solution]];

        // réponse dans la précision (5 pixels)
        $answer = '104-47';
        $this->assertTrue($this->challenge->verify($key, $answer));
    }

    public function testVerifyReturnsFalseForInvalidAnswer(): void
    {
        $key = 'validkey';
        $solution = [100, 50];
        $this->sessionData['puzzles'] = [['key' => $key, 'solution' => $solution]];

        // réponse hors précision
        $answer = '120-50';
        $this->assertFalse($this->challenge->verify($key, $answer));

        // clé inconnue
        $this->assertFalse($this->challenge->verify('nokey', '100-50'));
    }

    public function testStringToPositionParsesCorrectly(): void
    {
        $method = new \ReflectionMethod(PuzzleChallenge::class, 'stringToPosition');
        $method->setAccessible(true);

        $result = $method->invokeArgs($this->challenge, ['12-34']);
        $this->assertSame([12, 34], $result);

        $result = $method->invokeArgs($this->challenge, ['-1-0']);
        $this->assertSame([-1, -1], $result);

        // format invalide
        $result = $method->invokeArgs($this->challenge, ['invalid']);
        $this->assertSame([-1, -1], $result);

        $result = $method->invokeArgs($this->challenge, ['12-abc']);
        $this->assertSame([-1, -1], $result);
    }
}
