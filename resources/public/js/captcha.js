import { captchaService } from './services/captchaService.js';

class puzzleCaptcha extends HTMLElement {
    connectedCallback() {
        const width = parseInt(this.getAttribute('width'), 10);
        const height = parseInt(this.getAttribute('height'), 10);
        const pieceWidth = parseInt(this.getAttribute('piece-width'), 10);
        const pieceHeight = parseInt(this.getAttribute('piece-height'), 10);
        const maxX = width - pieceWidth;
        const maxY = height - pieceHeight;

        const input = this.querySelector('.captcha_answer');

        this.classList.add('captcha', 'captcha-waiting-interaction');
        this.style.setProperty('--image', `url(${this.getAttribute('src')})`);
        this.style.setProperty('--width', width + 'px');
        this.style.setProperty('--height', height + 'px');
        this.style.setProperty('--piece-width', pieceWidth + 'px');
        this.style.setProperty('--piece-height', pieceHeight + 'px');

        const piece = document.createElement('div');
        piece.classList.add('captcha-piece');
        this.appendChild(piece);

        let isDragging = false;
        let position = { x: captchaService.randomBetween(0, maxX), y: captchaService.randomBetween(0, maxY) };
        piece.style.setProperty('transform', `translate(${position.x}px, ${position.y}px)`);

        piece.addEventListener('pointerdown', () => {
            isDragging = true;
            document.body.style.setProperty('user-select', 'none');
            this.classList.remove('captcha-waiting-interaction');
            piece.classList.add('is-moving');

            window.addEventListener('pointerup', () => {
                isDragging = false;
                document.body.style.removeProperty('user-select');
                piece.classList.remove('is-moving');

                const challengeInput = document.querySelector('.captcha_challenge');
                const challenge = challengeInput.value;
                const answer = `${position.x}-${position.y}`;
                const overlay = document.querySelector('.captcha-valid');

                captchaService.checkAnswer(challenge, answer)
                    .then(data => {
                        if (data.success) overlay.classList.add('captcha-valid-confirmed');
                    })
                    .catch(err => console.error('Erreur AJAX:', err));
            }, { once: true });
        });

        this.addEventListener('pointermove', (e) => {
            if (!isDragging) return;

            position.x = captchaService.clamp(position.x + e.movementX, 0, maxX);
            position.y = captchaService.clamp(position.y + e.movementY, 0, maxY);

            piece.style.setProperty('transform', `translate(${position.x}px, ${position.y}px)`);
            input.value = `${position.x}-${position.y}`;
        });
    }
}

customElements.define('puzzle-captcha', puzzleCaptcha);
