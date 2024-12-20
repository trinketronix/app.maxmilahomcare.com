// Handle flash messages auto-hide
document.addEventListener('DOMContentLoaded', function() {
    // Auto hide flash messages after 3 seconds
    const flashMessages = document.querySelectorAll('.alert');
    flashMessages.forEach(function(flash) {
        setTimeout(function() {
            flash.style.opacity = '0';
            setTimeout(function() {
                flash.remove();
            }, 300);
        }, 3000);
    });

    // Add password visibility toggle
    const passwordInputs = document.querySelectorAll('input[type="password"]');
    passwordInputs.forEach(function(input) {
        const toggleButton = document.createElement('button');
        toggleButton.type = 'button';
        toggleButton.className = 'btn btn-outline-secondary';
        toggleButton.innerHTML = '👁️';
        toggleButton.onclick = function() {
            input.type = input.type === 'password' ? 'text' : 'password';
        };
        input.parentNode.style.position = 'relative';
        toggleButton.style.position = 'absolute';
        toggleButton.style.right = '10px';
        toggleButton.style.top = '50%';
        toggleButton.style.transform = 'translateY(-50%)';
        input.parentNode.appendChild(toggleButton);
    });
}); 