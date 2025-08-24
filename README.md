
# 🛡️ vdub-dev/captcha-handler

A **lightweight PHP library** to generate and handle **custom CAPTCHAs**.  
Built on top of **Symfony components**, with **YAML configuration support** for easy customization (e.g. default image paths).  
Includes **JavaScript, CSS, and image assets** for seamless frontend integration.

---

## 🚀 Installation

Add the following lines to your `composer.json`:

```json
{
  "minimum-stability": "dev",
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/vincentDubresson/captcha-handler.git"
    }
  ],
  "require": {
    "vdub-dev/captcha-handler": "^1.0"
  }
}
```

Then run:

```bash
composer update vdub-dev/captcha-handler:^1.0
```

Create a file `config/routes/captcha_handler.yaml` :

```yaml
captcha_handler:
  resource: '../../vendor/vdub-dev/captcha-handler/Resources/config/routing/captcha.php'
  type: php
```

then run:

```bash
php bin/console c:cl
```

---

## 📂 Assets

Assets should be installed automatically in:

```
public/bundles/captchahandler
```

### If not, do the following:

1. Make sure the bundle is enabled in `config/bundles.php`:

```php
<?php

return [
    VdubDev\CaptchaHandler\CaptchaHandlerBundle::class => ['all' => true],
];
```

2. Re-install the assets manually:

```bash
php bin/console assets:install
```

---

## ✅ Features

- Lightweight and easy to integrate
- **Symfony bundle** compatible
- Flexible **YAML configuration**
- Includes **frontend assets** (**JS, CSS, images**)

---


## 🎨 Frontend integration (CSS & JS)

The package provides ready-to-use **CSS** and **JavaScript** assets.  
Depending on your setup, you can include them in two ways:

---

### 🔹 Option 1: With AssetMapper

After running:

```bash
php bin/console assets:install
```

the assets will be available in:

```
public/bundles/captchahandler/css/captcha.css
public/bundles/captchahandler/js/captcha.js
```

#### AssetMapper (Symfony 6.3+)

Reference the bundle's files directly in your `app.js`.  
Example:

```js
// assets/app.js
import '/bundles/captchahandler/js/captcha.js';
```

---

### With AND Without AssetMapper

If you don’t use a build tool, include the assets directly in your Twig templates:

```twig
{# base.html.twig #}

{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('bundles/captchahandler/css/captcha.css') }}">
{% endblock %}

{% block javascripts %}
    {{ parent() }}
{% endblock %}
```

This will load the assets directly from the `public/` directory.

---


## 🔑 Backend Integration

You can integrate the captcha either via the **Symfony Form component** or more traditionally via a **controller and Twig include**.

---

### 🔹 Option 1: Symfony Form Integration

#### 1. Create a form type

```php
<?php

namespace App\Form;

use VdubDev\CaptchaHandler\Form\Type\CaptchaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ExampleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('captcha', CaptchaType::class, [
                'mapped' => false,
            ])
        ;
    }
}
```

#### 2. Render in Twig

```twig
{{ form_row(form.captcha) }}
```

#### 3. Enable the form theme in `config/packages/twig.yaml`

```yaml
twig:
    form_themes:
        - '@CaptchaHandler/form/captcha.html.twig'
```

---

### 🔹 Option 2: Traditional Controller Integration

#### 1. Inject the challenge service in your controller

```php
use VdubDev\CaptchaHandler\Domain\AntiSpam\ChallengeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExampleController
{
    #[Route(path: '/example', name: 'app_example')]
    public function example(ChallengeInterface $challengeInterface): Response
    {
        return $this->render('example/example.html.twig', [
            'challenge' => $challengeInterface->generateChallengeKey(),
        ]);
    }
}
```

#### 2. Render in Twig

```twig
{% include '@CaptchaHandler/captcha.html.twig' %}
```

---

### ⚖️ Which option to choose?

- **Form integration** → recommended if your captcha is part of an existing Symfony form (login, registration, contact form…).
- **Controller integration** → useful if you need more control or want to render the captcha outside of a form.  