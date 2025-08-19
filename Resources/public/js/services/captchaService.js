export const captchaService = {
    clamp(n, min, max) {
        return Math.min(Math.max(n, min), max);
    },

    randomBetween(min, max) {
        return Math.floor(Math.random() * (max - min + 1));
    },

    checkAnswer(challenge, answer) {
        return fetch(`/_captcha/check?challenge=${encodeURIComponent(challenge)}&answer=${encodeURIComponent(answer)}`)
            .then(response => response.json());
    }
};
