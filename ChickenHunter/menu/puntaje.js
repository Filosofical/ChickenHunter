document.addEventListener('DOMContentLoaded', function() {
    const radioButtons = document.querySelectorAll('input[type="radio"][name="radio"]');

    const scoreFacil = document.querySelector('.score .facil');
    const scoreMedio = document.querySelector('.score .medio');
    const scoreDificil = document.querySelector('.score .dificil');

    function updateScoreDisplay() {
        // Primero, oculta todas las secciones
        if (scoreFacil) scoreFacil.style.display = 'none';
        if (scoreMedio) scoreMedio.style.display = 'none';
        if (scoreDificil) scoreDificil.style.display = 'none';

        let selectedValue = null;
        radioButtons.forEach(function(radio) {
            if (radio.checked) {
                selectedValue = radio.value;
            }
        });

        if (selectedValue === 'facil' && scoreFacil) {
            scoreFacil.style.display = 'block'; // 
        } else if (selectedValue === 'medio' && scoreMedio) {
            scoreMedio.style.display = 'block'; // 
        } else if (selectedValue === 'dificil' && scoreDificil) {
            scoreDificil.style.display = 'block'; // O 'table'
        }
    }

    radioButtons.forEach(function(radio) {
        radio.addEventListener('change', updateScoreDisplay);
    });

    updateScoreDisplay();
});