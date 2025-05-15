document.addEventListener('DOMContentLoaded', function() {
    // Selecciona todos los radio buttons con el nombre 'radio'
    const radioButtons = document.querySelectorAll('input[type="radio"][name="radio"]');

    // Selecciona las secciones de puntuaciones
    const scoreFacil = document.querySelector('.score .facil');
    const scoreMedio = document.querySelector('.score .medio');
    const scoreDificil = document.querySelector('.score .dificil');

    // Función para actualizar qué sección de puntuaciones se muestra
    function updateScoreDisplay() {
        // Primero, oculta todas las secciones
        if (scoreFacil) scoreFacil.style.display = 'none';
        if (scoreMedio) scoreMedio.style.display = 'none';
        if (scoreDificil) scoreDificil.style.display = 'none';

        // Encuentra el radio button que está seleccionado
        let selectedValue = null;
        radioButtons.forEach(function(radio) {
            if (radio.checked) {
                selectedValue = radio.value;
            }
        });

        // Muestra la sección correspondiente al radio button seleccionado
        if (selectedValue === 'facil' && scoreFacil) {
            scoreFacil.style.display = 'block'; // O 'table' si es más apropiado para tu diseño
        } else if (selectedValue === 'medio' && scoreMedio) {
            scoreMedio.style.display = 'block'; // O 'table'
        } else if (selectedValue === 'dificil' && scoreDificil) {
            scoreDificil.style.display = 'block'; // O 'table'
        }
    }

    // Añade un 'event listener' a cada radio button para que llame a updateScoreDisplay cuando cambie
    radioButtons.forEach(function(radio) {
        radio.addEventListener('change', updateScoreDisplay);
    });

    // Llama a la función una vez al cargar la página para mostrar la sección correcta
    // (basado en el radio que esté 'checked' por defecto en el HTML)
    updateScoreDisplay();
});