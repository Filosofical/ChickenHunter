document.querySelectorAll('.menu-button').forEach(button => {
    button.addEventListener('click', () => {
        document.getElementById('chickenSound').play();
    });
});