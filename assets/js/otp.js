document.addEventListener('DOMContentLoaded', () => {
    const steps = document.querySelectorAll('.otp-step');
    const dots = document.querySelectorAll('.otp-progress-dot');
    const confirmBtn1 = document.getElementById('confirm-step1');
    const confirmBtn2 = document.getElementById('confirm-step2');
    const confirmBtn3 = document.getElementById('confirm-step3');
    const otpInputs = document.querySelectorAll('.otp-inputs input');

    function showStep(stepIndex) {
        steps.forEach((step, index) => {
            step.classList.toggle('active', index === stepIndex);
        });
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index <= stepIndex);
        });
        if (stepIndex === 1 && otpInputs.length > 0) {
            otpInputs[otpInputs.length - 1].focus();
            otpInputs[otpInputs.length - 1].select();
        }
    }

    otpInputs.forEach((input, index) => {
        input.addEventListener('input', (e) => {
            e.target.value = e.target.value.replace(/[^0-9]/g, '');
            if (e.target.value && index < otpInputs.length - 1) {
                otpInputs[index + 1].focus();
                otpInputs[index + 1].select();
            }
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && !e.target.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
    });

    confirmBtn1?.addEventListener('click', () => showStep(1));
    confirmBtn2?.addEventListener('click', () => showStep(2));
    confirmBtn3?.addEventListener('click', () => {
        window.location.href = '../index.php';
    });
});
