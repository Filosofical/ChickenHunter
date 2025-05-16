let isPaused = false;
let gameTime = 120; // Tiempo inicial del juego en segundos (ej. 2 minutos)
let timerInterval;

const trapCooldown = 5000; // 3 segundos de cooldown para la trampa

const Dificultad = localStorage.getItem('selectedDifficulty') || 1; // Dificultad seleccionada por el usuario

const idUsuario=document.getElementById('userid').value;
let punt=0;
// Escena
const scene = new THREE.Scene();
scene.background = new THREE.Color(0x01050D); // Cielo nocturno

// Menu pausa
function showPauseMenu() {
    document.getElementById('pauseMenu').style.display = 'block';
}

function hidePauseMenu() {
    document.getElementById('pauseMenu').style.display = 'none';
}

function resumeGame() {
    isPaused = false;
    hidePauseMenu();
}

function quitGame() {
    window.location.href = '../menu/index.php';
}

// Elementos del DOM
const counterElement = document.getElementById('counter');
const timerElement = document.getElementById('timer'); // Referencia al elemento del temporizador
const notificationElement = document.getElementById('notificacion'); // Referencia al elemento de notificación

function showNotification(message, duration = 2000) {
    if (!notificationElement) return;

    notificationElement.textContent = message;
    notificationElement.style.display = 'block'; // Hacerlo visible en el layout
    setTimeout(() => { // Pequeño delay para asegurar que display:block se aplique antes de la transición de opacidad
        notificationElement.style.opacity = '1';
    }, 10);

    // Ocultar después de la duración especificada
    setTimeout(() => {
        hideNotification();
    }, duration);
}

// Función para ocultar la notificación con fade-out
function hideNotification() {
    if (!notificationElement) return;

    notificationElement.style.opacity = '0';
    // Esperar a que la transición de opacidad termine antes de hacer display:none
    setTimeout(() => {
        notificationElement.style.display = 'none';
    }, 500); // Este tiempo debe coincidir con la duración de la transición en CSS (0.5s)
}
// Menu pausa
function showPauseMenu() {
    document.getElementById('pauseMenu').style.display = 'block';
    // Pausar el temporizador
    if (timerInterval) clearInterval(timerInterval);
}

function hidePauseMenu() {
    document.getElementById('pauseMenu').style.display = 'none';
    // Reanudar el temporizador
    if (!isGameOver) { // Solo reanudar si el juego no ha terminado
        startTimer();
    }
}

function resumeGame() {
    isPaused = false;
    hidePauseMenu();
}

function quitGame() {
    // Aquí podrías añadir lógica para guardar la partida si es necesario
    window.location.href = '../menu/index.php';
}

// Estado del juego
let isGameOver = false;

function gameOver(message) {
    isGameOver = true;
    isPaused = true; // Pausa el juego
    clearInterval(timerInterval); // Detiene el temporizador
    // Muestra un mensaje de fin de juego (puedes personalizar esto más)
    const gameOverMessage = document.createElement('div');
    gameOverMessage.id = 'gameOverMessage';
    gameOverMessage.innerHTML = `<h1>${message}</h1><button onclick="restartGame()">Jugar de Nuevo</button><button onclick="quitGame()">Salir</button>`;
    gameOverMessage.style.position = 'absolute';
    gameOverMessage.style.top = '50%';
    gameOverMessage.style.left = '50%';
    gameOverMessage.style.transform = 'translate(-50%, -50%)';
    gameOverMessage.style.backgroundColor = 'rgba(255, 255, 255, 0.8)';
    gameOverMessage.style.color = 'white';
    gameOverMessage.style.padding = '20px';
    gameOverMessage.style.textAlign = 'center';
    gameOverMessage.style.borderRadius = '10px';
    gameOverMessage.style.fontFamily = "'Sigmar', cursive";
    document.body.appendChild(gameOverMessage);

    // Detener animaciones del jugador si están activas
    if (currentAction) {
        currentAction.stop(); // O fadeOut si prefieres
    }
 if (caughtChickens==targetCatchCount){
    punt=caughtChickens*200;
 }else{
    punt=caughtChickens*80;
 }
  punt=punt*Dificultad;
  if (gameTime !== 0){
    punt=punt*(gameTime/20); }//disminuir el divisor en cada dificultad
    punt=Math.trunc(punt);
    showNotification(`¡Has atrapado todas las gallinas! Tu puntaje es: ${punt}`);
    if(idUsuario!==null){

        // Guardar el puntaje en la base de datos
        fetch('../Back/partida.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                idUsuario: idUsuario,
                dificultad: Dificultad,
                puntaje: punt
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Puntaje guardado:', data);
        })
        .catch(error => {
            console.error('Error al guardar el puntaje:', error);
        });
    }

}

function restartGame() {
    // Eliminar mensaje de fin de juego
    const gameOverMessage = document.getElementById('gameOverMessage');
    if (gameOverMessage) {
        gameOverMessage.remove();
    }

    // Reiniciar variables del juego
    isPaused = false;
    isGameOver = false;
    gameTime = 120; // Reinicia el tiempo
    caughtChickens = 0;
    counterElement.textContent = `Gallinas atrapadas: ${caughtChickens}`;
    updateTimerDisplay();

    // Reposicionar jugador (opcional, o a una posición inicial)
    if (player) {
        player.position.set(0, 1, 0); // Ajusta la altura según tu modelo
    }

    // Reposicionar gallinas
    chickens.forEach(chicken => {
        if(chicken) chicken.position.set(Math.random() * 40 - 20, 0.5, Math.random() * 40 - 20);
    });
    
   bearTraps.forEach(trap => {
        if (trap.action) {
            trap.action.stop();
            trap.action.reset();
        }
        if (trap.mixer) {
            trap.mixer.update(0); // Aplicar el estado reseteado
        }
        trap.lastActivationTime = 0; // Reiniciar cooldown individual
    });

    // Ocultar menú de pausa si está visible
    hidePauseMenu();
    // Reanudar el bucle de animación y el temporizador
    startTimer();
    // No necesitas llamar a animate() de nuevo, ya está corriendo.
    // Asegúrate de que las actualizaciones de estado se manejen bien en animate()
}


// Temporizador
function updateTimerDisplay() {
    const minutes = Math.floor(gameTime / 60);
    const seconds = gameTime % 60;
    timerElement.textContent = `Tiempo: ${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
}

function startTimer() {
    if (timerInterval) clearInterval(timerInterval); // Limpia el intervalo anterior si existe
    timerInterval = setInterval(() => {
        if (!isPaused && !isGameOver) {
            gameTime--;
            updateTimerDisplay();
            if (gameTime <= 0) {
                clearInterval(timerInterval);
                gameOver("¡Se acabó el tiempo!");
                if (actions.defeat) { // Mostrar animación de derrota
                    transitionToAnimation(actions.defeat, 0.2, false);
                }
            }
        }
    }, 1000);
}


// Cámara
const camera = new THREE.PerspectiveCamera(80, window.innerWidth / window.innerHeight, 0.1, 1000);
camera.position.set(0, 5, 10);
camera.lookAt(0, 0, 0);

// Renderizador
const renderer = new THREE.WebGLRenderer();
renderer.setSize(window.innerWidth, window.innerHeight);
document.body.appendChild(renderer.domElement);

// Textura del escenario 
const textureLoader = new THREE.TextureLoader();
const TexturaPasto = textureLoader.load('LAVA_TEXT.png');
TexturaPasto.wrapS = THREE.RepeatWrapping;
TexturaPasto.wrapT = THREE.RepeatWrapping;
TexturaPasto.repeat.set(10, 10);

// Tamaño de nuestro escenario y límites
const groundSize = 200; // Usado para el plano
const fenceLimits = { // Define los límites donde el jugador puede moverse  modificar en dificil
    minX: -40, // Ajusta estos valores según el tamaño de tu cerca visible
    maxX: 40,
    minZ: -40,
    maxZ: 40};
const groundGeometry = new THREE.PlaneGeometry(groundSize, groundSize);
const groundMaterial = new THREE.MeshStandardMaterial({ map: TexturaPasto });
const ground = new THREE.Mesh(groundGeometry, groundMaterial);
ground.rotation.x = -Math.PI / 2;
scene.add(ground);

// Luz ambiental
const ambientLight = new THREE.AmbientLight(0x000000, 0.5);
scene.add(ambientLight);

// Luz direccional (simula el sol)
const directionalLight = new THREE.DirectionalLight(0xffffff, 0.5);
directionalLight.position.set(10, 10, 10);
scene.add(directionalLight);

// Variables para animaciones
let mixer;
let actions = {};
let currentAction = null;
let isMoving = false;
let isRunning = false;
let isCatching = false;
// let isDefeated = false;
// let isVictorious = false;
const clock = new THREE.Clock();

// Jugador
const loader = new THREE.GLTFLoader();
let player;
const selectedCharacter = localStorage.getItem('selectedCharacter') || 'Granjero';

 if (selectedCharacter === 'Granjero') {
  
 loader.load('../escenario/models/granjero.glb', (gltf) => {
     player = gltf.scene;
     player.scale.set(2, 2, 2);
     player.position.set(0, 0, 0);
     scene.add(player);
    
     // Mezclador de animaciones
     mixer = new THREE.AnimationMixer(player);
    
     // Se obtienen todas las animaciones del modelo
     const animations = gltf.animations;
    
     // Animaciones básicas
     actions = {
         idle: mixer.clipAction(findAnimation(animations, "Idle")),
         walk: mixer.clipAction(findAnimation(animations, "Walk")),
         run: mixer.clipAction(findAnimation(animations, "Run")),
         victory: mixer.clipAction(findAnimation(animations, "Victory")),
         defeat: mixer.clipAction(findAnimation(animations, "Defeat")),
         catch: mixer.clipAction(findAnimation(animations, "Catch"))
     };
    
     // Configurar las acciones
     Object.values(actions).forEach(action => {
         action.enabled = true;
         action.setEffectiveTimeScale(1);
         action.setEffectiveWeight(1);
     });
    
     // Iniciar con animación Idle1
      currentAction = actions.idle;
        if(currentAction) currentAction.play();
        console.log('Modelo Granjero y animaciones cargados');
    }, undefined, (error) => console.error("Error al cargar el modelo Granjero:", error));


 } else if (selectedCharacter === 'Perro') {
    loader.load('../escenario/models/dog.glb', (gltf) => {
        player = gltf.scene;
        player.scale.set(.2, .2, .2);
        player.position.set(0, 0, 0);
        scene.add(player);
        
        // Mezclador de animaciones
        mixer = new THREE.AnimationMixer(player);
        
        // Se obtienen todas las animaciones del modelo
        const animations = gltf.animations;
        
        // Animaciones básicas
        actions = {
            idle: mixer.clipAction(findAnimation(animations, "Idle1")),
            walk: mixer.clipAction(findAnimation(animations, "WalkCycle")),
            run: mixer.clipAction(findAnimation(animations, "RunCycle")),
            victory: mixer.clipAction(findAnimation(animations, "SitScratchEar")),
            defeat: mixer.clipAction(findAnimation(animations, "LayDown"))
        };
        
        // Configurar las acciones
        Object.values(actions).forEach(action => {
            action.enabled = true;
            action.setEffectiveTimeScale(1);
            action.setEffectiveWeight(1);
        });
        
        // Iniciar con animación Idle1
        currentAction = actions.idle;
        if(currentAction) currentAction.play();
        console.log('Modelo Perro y animaciones cargados');
    }, undefined, (error) => console.error("Error al cargar el modelo Perro:", error));

}


// Función auxiliar para encontrar animaciones por nombre
function findAnimation(animations, name) {
    if (!animations) return null;
    const animation = animations.find(anim => anim.name.toLowerCase() === name.toLowerCase());
    if (!animation) console.warn(`Animación no encontrada: ${name}`);
    return animation;
}

// Función para transicionar a una animación
function transitionToAnimation(newAction, duration, loop = true) {
    if (currentAction === newAction && newAction.isRunning()) return;
    if (!newAction) {
        console.warn("Intento de transicionar a una acción nula");
        return;
    }

    const previousAction = currentAction;
    currentAction = newAction;

    if (previousAction && previousAction !== currentAction) {
        previousAction.fadeOut(duration);
    }

    currentAction
        .reset()
        .setEffectiveTimeScale(1)
        .setEffectiveWeight(1)
        .fadeIn(duration)
        .play();

    if (!loop) {
        currentAction.clampWhenFinished = true;
        currentAction.loop = THREE.LoopOnce;
    } else {
        currentAction.loop = THREE.LoopRepeat;
    }
}
// Función para actualizar la animación según el estado
// Función para actualizar la animación según el estado
function updateAnimationState() { // Renombrada para claridad
    if (isGameOver || isPaused) return; // No actualizar animaciones si el juego terminó o está pausado

    let newActionKey = 'idle'; // Por defecto, 'idle'

    if (isCatching && actions.catch) { // Prioridad a la animación de atrapar
        transitionToAnimation(actions.catch, 0.2, false); // No repetir la animación de atrapar
        // Después de que termine 'catch', volver a idle/walk/run
        mixer.addEventListener('finished', handleCatchAnimationFinished);
        return; // Salir para no sobrescribir la acción de atrapar inmediatamente
    }
    
    if (isMoving) {
        newActionKey = isRunning ? 'run' : 'walk';
    }

    if (actions[newActionKey] && currentAction !== actions[newActionKey]) {
        transitionToAnimation(actions[newActionKey], 0.2);
    }
}

// Manejador para cuando la animación de atrapar termina
function handleCatchAnimationFinished(event) {
    if (event.action === actions.catch) {
        isCatching = false; // Restablecer el estado
        mixer.removeEventListener('finished', handleCatchAnimationFinished); // Limpiar el listener
        updateAnimationState(); // Volver al estado de movimiento normal
    }
}


const chickens = [];
const chickenSpeed = 2; // Puedes ajustar esta velocidad
const chickenFleeSpeed = 3.5; // Velocidad cuando huyen  modificar en cada diff
const chickenDetectionRadius = 8; // Radio en el que la gallina detecta al jugador
const chickenWanderTimeMin = 2000; // Milisegundos mínimos para deambular en una dirección
const chickenWanderTimeMax = 5000; // Milisegundos máximos
const chickenCount = 10;  //modificar en cada diff
const targetCatchCount = 20;

// Función para que la gallina mire hacia su dirección de movimiento
function pointChickenInDirection(chicken, direction) {
    if (direction.lengthSq() > 0.001) { // Solo rotar si hay una dirección significativa
        const angle = Math.atan2(direction.x, direction.z);
        chicken.rotation.y = angle;
    }
}

for (let i = 0; i < chickenCount; i++) {
    loader.load('../escenario/models/chicken.glb', (gltf) => {
        const singleChicken = gltf.scene;
        singleChicken.scale.set(0.0100, 0.0100, 0.0100);
        // singleChicken.rotation.set(0, Math.PI, 0); // La rotación se manejará dinámicamente
        singleChicken.position.set(
            Math.random() * (fenceLimits.maxX - fenceLimits.minX) + fenceLimits.minX,
            0.5,
            Math.random() * (fenceLimits.maxZ - fenceLimits.minZ) + fenceLimits.minZ
        );

        // Propiedades para la IA de la gallina
        singleChicken.userData = {
            state: 'wandering', // 'wandering' o 'fleeing'
            wanderDirection: new THREE.Vector3((Math.random() - 0.5) * 2, 0, (Math.random() - 0.5) * 2).normalize(),
            timeToNextWander: Math.random() * (chickenWanderTimeMax - chickenWanderTimeMin) + chickenWanderTimeMin,
            lastWanderUpdateTime: Date.now()
        };
        pointChickenInDirection(singleChicken, singleChicken.userData.wanderDirection);


        scene.add(singleChicken);
        chickens.push(singleChicken);
        console.log('Modelo de gallina cargado con IA básica');
    }, undefined, (error) => console.error("Error al cargar el modelo de gallina:", error));
}

// Trampa de Oso
const bearTraps = []; // Array para almacenar todas las trampas
const numTraps = 30; // Define cuántas trampas quieres modificar en cada diff
const trapDamageTime = 20;


for (let i = 0; i < numTraps; i++) {
    loader.load('../escenario/models/trap.glb', (gltf) => {
        const trapModel = gltf.scene;
        trapModel.scale.set(1.0, 1.0, 1.0);
        
        // --- Generar posición aleatoria para la trampa ---
        const margin = 5; // Un pequeño margen para que no aparezcan pegadas a la cerca. Ajusta si es necesario.
        const randomX = Math.random() * (fenceLimits.maxX - margin - (fenceLimits.minX + margin)) + (fenceLimits.minX + margin);
        const randomZ = Math.random() * (fenceLimits.maxZ - margin - (fenceLimits.minZ + margin)) + (fenceLimits.minZ + margin);
        const trapHeight = 0; // Altura Y. Ajusta esto si el punto de origen de tu modelo de trampa no está en su base.

        const trapPosition = new THREE.Vector3(randomX, trapHeight, randomZ);
        trapModel.position.copy(trapPosition);
 

        scene.add(trapModel);

        const mixer = new THREE.AnimationMixer(trapModel);
        // Asegúrate que el nombre "trap" coincida con tu animación en el GLB
        const action = mixer.clipAction(findAnimation(gltf.animations, "Take 001")); 
        if (action) {
            action.setLoop(THREE.LoopOnce);
            action.clampWhenFinished = false;
        } else {
            console.warn(`Animación 'trap' no encontrada para una trampa en ${trapPosition.x.toFixed(2)}, ${trapPosition.z.toFixed(2)}`);
        }

        bearTraps.push({
            model: trapModel,
            mixer: mixer,
            action: action,
            position: trapPosition.clone(), // Importante guardar la posición generada
            lastActivationTime: 0,
            id: `trap_${i}`
        });
        console.log(`Modelo de trampa ${i} cargado aleatoriamente en X:${trapPosition.x.toFixed(2)}, Z:${trapPosition.z.toFixed(2)}`);
    }, undefined, (error) => console.error("Error al cargar un modelo de trampa:", error));
}



// Trees
let trees;

loader.load('../escenario/models/hellM.glb', (gltf) => {
    trees = gltf.scene;
    trees.scale.set(3, 2, 3); 
    trees.position.set(0, 0, 0); 
    scene.add(trees);
    console.log('Modelo cargado exitosamente');
}, undefined, (error) => {
    console.error("Error al cargar el modelo:", error);
});


// Movimiento del jugador
let playerSpeed = 0.1;
let moveForward = false;
let moveBackward = false;
let moveLeft = false;
let moveRight = false;

// Control del teclado
document.addEventListener('keydown', (event) => {
    if (isGameOver) return;  

    if (event.key === 'w' || event.key === 'ArrowUp') moveForward = true;
    if (event.key === 's' || event.key === 'ArrowDown') moveBackward = true;
    if (event.key === 'a' || event.key === 'ArrowLeft') moveLeft = true;
    if (event.key === 'd' || event.key === 'ArrowRight') moveRight = true;
    if (event.key === 'Shift') {
        isRunning = true;
        updateAnimation();
    }
    if (event.key === 'Escape' || event.key === 'p') {
        isPaused = !isPaused; 
        if (isPaused) {
            showPauseMenu();
        } else {
            hidePauseMenu(); 
        }
    }
});

document.addEventListener('keyup', (event) => {
      if (isGameOver) return;

    if (event.key === 'w' || event.key === 'ArrowUp') moveForward = false;
    if (event.key === 's' || event.key === 'ArrowDown') moveBackward = false;
    if (event.key === 'a' || event.key === 'ArrowLeft') moveLeft = false;
    if (event.key === 'd' || event.key === 'ArrowRight') moveRight = false;
    if (event.key === 'Shift') {
        isRunning = false;
        updateAnimation();
    }
});

// Contador de gallinas atrapadas
let caughtChickens = 0;


// Inicializar temporizador al cargar el juego
updateTimerDisplay();
startTimer();


// Animación
function animate() {
    requestAnimationFrame(animate);
    const delta = clock.getDelta();
    const currentTime = Date.now(); // Para la IA de la gallina

    // Actualizar mezclador de animaciones del jugador
    if (mixer) {
        mixer.update(delta);
    }

    // Actualizar mezcladores de todas las trampas
    bearTraps.forEach(trap => {
        if (trap.mixer) {
            trap.mixer.update(delta);
        }
    });

    if (isGameOver) { // Si el juego ha terminado, solo renderizar y salir
        renderer.render(scene, camera);
        return;
    }

    if (!isPaused) {
        const previousPosition = player ? player.position.clone() : null;

        // Actualizar estado de movimiento
        const wasMoving = isMoving;
        isMoving = moveForward || moveBackward || moveLeft || moveRight;

        // Si el estado de movimiento cambió o la animación de atrapar no está activa, actualizar animación
        if ((wasMoving !== isMoving || !isCatching)) {
            updateAnimationState();
        }

        // Movimiento del jugador
        if (player) {
            const currentSpeed = (isRunning ? playerSpeed * 2 : playerSpeed);
            if (moveForward) player.position.z -= currentSpeed;
            if (moveBackward) player.position.z += currentSpeed;
            if (moveLeft) player.position.x -= currentSpeed;
            if (moveRight) player.position.x += currentSpeed;

            // Colisiones con la cerca (límites del escenario)
            player.position.x = Math.max(fenceLimits.minX, Math.min(fenceLimits.maxX, player.position.x));
            player.position.z = Math.max(fenceLimits.minZ, Math.min(fenceLimits.maxZ, player.position.z));


            // Rotar el modelo según la dirección (sin cambios)
            if (moveBackward && moveRight) player.rotation.y = Math.PI / 4;
            else if (moveBackward && moveLeft) player.rotation.y = -Math.PI / 4;
            else if (moveForward && moveRight) player.rotation.y = 3 * Math.PI / 4;
            else if (moveForward && moveLeft) player.rotation.y = -3 * Math.PI / 4;
            else if (moveBackward) player.rotation.y = 0;
            else if (moveForward) player.rotation.y = Math.PI;
            else if (moveRight) player.rotation.y = Math.PI / 2;
            else if (moveLeft) player.rotation.y = -Math.PI / 2;

            // Actualizar la posición de la cámara para seguir al jugador
            camera.position.x = player.position.x;
            camera.position.z = player.position.z + 10; // Distancia detrás del jugador
            camera.position.y = player.position.y + 5;  // Altura de la cámara
            camera.lookAt(player.position);

 bearTraps.forEach(trap => {
                if (trap.model && player.position.distanceTo(trap.position) < 1.5) { // Ajusta el 1.5
                    if (currentTime - trap.lastActivationTime > trapCooldown) {
                        if (trap.action && !trap.action.isRunning()) {
                            console.log(`Trampa ${trap.id || ''} activada!`);
                            trap.action.reset().play();

                            gameTime -= trapDamageTime;
                            if (gameTime < 0) gameTime = 0;
                            updateTimerDisplay();
                            showNotification(`¡Cuidado! Te atrapó una trampa. Pierdes ${trapDamageTime} segundos.`);
                            
                            trap.lastActivationTime = currentTime; // Actualiza el cooldown para ESTA trampa

                            if (gameTime <= 0 && !isGameOver) {
                                gameOver("¡Te atrapó una trampa y se acabó el tiempo!");
                                if (actions.defeat) { // Mostrar animación de derrota
                                    transitionToAnimation(actions.defeat, 0.2, false);
                                }
                            }
                        }
                    }
                }
            });
         
        }
 chickens.forEach((chicken) => {
            if (chicken && player) {
                const chickenData = chicken.userData;
                const distanceToPlayer = chicken.position.distanceTo(player.position);
                let moveDirection = new THREE.Vector3();
                let currentSpeed = chickenSpeed;

                if (distanceToPlayer < chickenDetectionRadius) {
                    chickenData.state = 'fleeing';
                } else {
                    chickenData.state = 'wandering';
                }

                if (chickenData.state === 'fleeing') {
                    currentSpeed = chickenFleeSpeed;
                    // Vector del jugador a la gallina, para huir en esa dirección
                    moveDirection.subVectors(chicken.position, player.position).normalize();
                    moveDirection.y = 0; // No queremos que vuelen o se hundan
                } else { // 'wandering'
                    currentSpeed = chickenSpeed;
                    if (currentTime - chickenData.lastWanderUpdateTime > chickenData.timeToNextWander) {
                        // Cambiar dirección de deambulación
                        chickenData.wanderDirection.set((Math.random() - 0.5) * 2, 0, (Math.random() - 0.5) * 2).normalize();
                        chickenData.timeToNextWander = Math.random() * (chickenWanderTimeMax - chickenWanderTimeMin) + chickenWanderTimeMin;
                        chickenData.lastWanderUpdateTime = currentTime;
                    }
                    moveDirection.copy(chickenData.wanderDirection);
                }

                // Mover la gallina
                chicken.position.x += moveDirection.x * currentSpeed * delta;
                chicken.position.z += moveDirection.z * currentSpeed * delta;

                // Rotar la gallina para que mire en la dirección de movimiento
                pointChickenInDirection(chicken, moveDirection);

                // Mantener gallinas dentro de los límites
                chicken.position.x = Math.max(fenceLimits.minX + 1, Math.min(fenceLimits.maxX - 1, chicken.position.x));
                chicken.position.z = Math.max(fenceLimits.minZ + 1, Math.min(fenceLimits.maxZ - 1, chicken.position.z));

                // Detección de colisión con el jugador para atraparla (sin cambios en esta parte)
                if (!isCatching && chicken.position.distanceTo(player.position) < 1.5) {
                     if (actions.catch && selectedCharacter === 'Perro') {
                        isCatching = true;
                        updateAnimationState();
                    } else {
                        handleChickenCaught(chicken); 
                    }
                }
            }
        });
        

        // Lógica de atrapar gallina (separada para ser llamada por animación o directamente)
        function handleChickenCaught(chicken) {
            chicken.position.set(Math.random() * (fenceLimits.maxX - fenceLimits.minX) + fenceLimits.minX, 0.5, Math.random() * (fenceLimits.maxZ - fenceLimits.minZ) + fenceLimits.minZ);
            caughtChickens++;
            counterElement.textContent = `Gallinas atrapadas: ${caughtChickens}`;
        
            if (!isCatching && selectedCharacter === 'Perro' && actions.catch) {
                // Si no se estaba ya en la animación de atrapar (porque fue un catch instantáneo para el perro por ejemplo)
                // y el granjero tiene animación de catch, se podría forzar un pequeño gesto o sonido.
                // Pero como la lógica de `isCatching` ya maneja la animación, esta parte podría ser redundante
                // o necesitar un ajuste si quieres un feedback visual/auditivo incluso sin la animación completa.
                 if (actions.catch) { // Mostrar animación de victoria
                    transitionToAnimation(actions.catch, 0.2, false);
                }
            }
        
            // Lógica de victoria
            if (caughtChickens >= targetCatchCount && !isGameOver) {
                gameOver("¡Has atrapado todas las gallinas!");
                if (actions.victory) { // Mostrar animación de victoria
                    transitionToAnimation(actions.victory, 0.2, false);
                }
            }
        }

        // Si isCatching es true y la animación es la de atrapar y el personaje es Granjero
        if (isCatching && currentAction === actions.catch && selectedCharacter === 'Perro') {
            // Hacemos la lógica de "atrapar" (mover gallina, contar) cuando la animación está a punto de terminar
            // o en un punto clave. Para simplificar, podrías atarlo al evento 'finished'
            // o si la animación "Catch" ya implica visualmente el atrape, puedes hacer la lógica
            // justo antes de que termine.
            // Por ahora, la lógica de atrapar la gallina (moverla y contarla) la he puesto en
            // un listener 'finished' para la animación de "catch".
            // Buscamos la gallina más cercana para aplicar el efecto de "atrape"
            // Esta es una forma simple, podrías querer tener una referencia a la gallina específica
            // que disparó la colisión.
            if (!mixer.listeners_['finished']) { // Asegurarse de no añadir múltiples listeners
                 mixer.addEventListener('finished', (e) => {
                    if (e.action === actions.catch) {
                        // Encuentra la gallina más cercana que disparó la colisión
                        // Esto es una simplificación; idealmente, tendrías la gallina específica.
                        let caughtChickenInstance = null;
                        for(let ch of chickens){
                            if(player && ch.position.distanceTo(player.position) < 2.0){ // un poco más de rango para asegurar
                                caughtChickenInstance = ch;
                                break;
                            }
                        }
                        if(caughtChickenInstance){
                            handleChickenCaught(caughtChickenInstance);
                        }
                        isCatching = false; // Importante resetear
                        // mixer.removeEventListener('finished', this); // No se puede usar 'this' así directamente
                        updateAnimationState(); // Volver a idle/walk/run
                    }
                });
            }
        }


    } // Fin de if(!isPaused)

    renderer.render(scene, camera);
}

animate();

// Ajuste del tamaño de la pantalla
window.addEventListener('resize', () => {
    renderer.setSize(window.innerWidth, window.innerHeight);
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
});