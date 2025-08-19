<?php

namespace VdubDev\CaptchaHandler\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use VdubDev\CaptchaHandler\Domain\AntiSpam\ChallengeInterface;
use VdubDev\CaptchaHandler\Validator\Challenge;

class CaptchaType extends AbstractType
{
    public function __construct(
        private readonly ChallengeInterface $challenge,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly int $imageWidth,
        private readonly int $imageHeight,
        private readonly int $puzzleWidth,
        private readonly int $puzzleHeight,
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'constraints' => [
                new NotBlank(),
                new Challenge(),
            ],
            'route' => 'captcha',
        ]);

        parent::configureOptions($resolver);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('challenge', HiddenType::class, [
                'attr' => [
                    'label' => false,
                    'class' => 'captcha_challenge',
                ],
                'data' => $this->challenge->generateChallengeKey(),
            ])
            ->add('answer', HiddenType::class, [
                'attr' => [
                    'class' => 'captcha_answer',
                ],
            ])
        ;

        parent::buildForm($builder, $options);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $key = $this->challenge->generateChallengeKey();
        /** @var string $route */
        $route = $options['route'];

        $view->vars['attr'] = [
            'width' => $this->imageWidth,
            'height' => $this->imageHeight,
            'piece_width' => $this->puzzleWidth,
            'piece_height' => $this->puzzleHeight,
            'src' => $this->urlGenerator->generate($route, ['challenge' => $key]),
        ];

        $view->vars['challenge'] = $key;

        parent::buildView($view, $form, $options);
    }
}
