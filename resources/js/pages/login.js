document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    const submitBtn = loginForm?.querySelector('button[type="submit"]');

    if (loginForm && submitBtn) {
        loginForm.addEventListener('submit', () => {
            // Previne duplo clique e adiciona um efeito sútil de loading
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            const btnText = submitBtn.querySelector('span:first-child');
            if (btnText) {
                btnText.textContent = 'Authenticating...';
            }
        });
    }
});
