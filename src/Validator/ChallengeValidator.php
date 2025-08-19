<?php

namespace VdubDev\CaptchaHandler\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use VdubDev\CaptchaHandler\Domain\AntiSpam\ChallengeInterface;

final class ChallengeValidator extends ConstraintValidator
{
    public function __construct(private readonly ChallengeInterface $challengeInterface)
    {
    }

    /**
     * @param ?array{challenge: string, answer: string} $value
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Challenge) {
            return;
        }

        if (!is_array($value)) {
            return;
        }

        if (!$this->challengeInterface->verify($value['challenge'], $value['answer'])) {
            $this->context->buildViolation($constraint->message)
                ->addViolation()
            ;
        }
    }
}
