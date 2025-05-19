// seleccionar_dificultad.js

document.getElementById('jugarBtn').addEventListener('click', function() {
    const selectedInput = document.querySelector('input[name="dificultad"]:checked');

    if (selectedInput) {
        const dificultadSeleccionada = selectedInput.value;
        let targetUrl = ''; 
        let dificultadNumero = 1; 

        // Determina la URL y el número de dificultad basado en la selección
        switch (dificultadSeleccionada) {
            case '1':
                // Asegúrate que esta ruta y nombre de archivo sean correctos
                targetUrl = '../escenario/escenario.php';
                dificultadNumero = 1;
                break;
            case '2':
                // Usando el nombre que mencionaste, verifica que sea exacto
                targetUrl = '../escenario/escenarioMid.php';
                dificultadNumero = 2;
                break;
            case '3': 
                 // Asegúrate que esta ruta y nombre de archivo sean correctos
                targetUrl = '../escenario/EsceneHard.php';
                dificultadNumero = 3;
                break;
            default:
            
                alert('Selección de dificultad inválida.');
                return; 
        }

   
        localStorage.setItem('selectedDifficulty', dificultadNumero);
        localStorage.setItem('multiplayer','false');
        console.log(`Dificultad ${dificultadNumero} guardada en localStorage.`);

        // Redirige al navegador a la URL del escenario correspondiente
        console.log('Redirigiendo a:', targetUrl);
        window.location.href = targetUrl;

    } else {
        alert('Por favor, selecciona un nivel de dificultad.');
    }
});

document.getElementById('jugarBtnMult').addEventListener('click', function() {
    const selectedInput = document.querySelector('input[name="dificultad"]:checked');

    if (selectedInput) {
        const dificultadSeleccionada = selectedInput.value;
        let targetUrl = ''; 
        let dificultadNumero = 1; 

        // Determina la URL y el número de dificultad basado en la selección
        switch (dificultadSeleccionada) {
            case '1':
                // Asegúrate que esta ruta y nombre de archivo sean correctos
                targetUrl = '../escenario/escenario.php';
                dificultadNumero = 1;
                break;
            case '2':
                // Usando el nombre que mencionaste, verifica que sea exacto
                targetUrl = '../escenario/escenarioMid.php';
                dificultadNumero = 2;
                break;
            case '3': 
                 // Asegúrate que esta ruta y nombre de archivo sean correctos
                targetUrl = '../escenario/EsceneHard.php';
                dificultadNumero = 3;
                break;
            default:
            
                alert('Selección de dificultad inválida.');
                return; 
        }

   
        localStorage.setItem('selectedDifficulty', dificultadNumero);
        localStorage.setItem('multiplayer', 'true'); // Guardar el modo multijugador
        console.log(`Dificultad ${dificultadNumero} guardada en localStorage.`);

        // Redirige al navegador a la URL del escenario correspondiente
        console.log('Redirigiendo a:', targetUrl);
        window.location.href = targetUrl;

    } else {
        alert('Por favor, selecciona un nivel de dificultad.');
    }
});