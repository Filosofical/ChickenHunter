let isPaused = false;
let gameTime = 120; // Tiempo inicial del juego en segundos (ej. 2 minutos)
let timerInterval;

const trapCooldown = 5000; // 3 segundos de cooldown para la trampa

const Dificultad = localStorage.getItem('selectedDifficulty') || 1; // Dificultad seleccionada por el usuario
const multiString = localStorage.getItem('multiplayer');
const Multi = multiString === 'true';
console.log('multi=',Multi);
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
    localStorage.removeItem('selectedDifficulty');
    localStorage.removeItem('multiplayer');
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
    punt=punt*(gameTime/40);} //disminuir el divisor en cada dificultad
    punt=Math.trunc(punt);
    showNotification(`¡Has atrapado todas las gallinas! Tu puntaje es: ${punt}`);
  if(!Multi){
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


}else{
    if(idUsuario!==null){

        // Guardar el puntaje en la base de datos
        fetch('../Back/multiplayer.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                idUsuario: idUsuario,
                idUsuario2: idUsuario,
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
   localStorage.removeItem('selectedDifficulty');
    localStorage.removeItem('multiplayer');
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
   if (player) { // Jugador 1
        player.position.set(Multi ? -2 : 0, 0, 0); // Posición inicial J1 (ajustada si es multi)
        // Podrías resetear la rotación aquí si es necesario
    }
    if (Multi && player2) { // Jugador 2
        player2.position.set(2, 0, 0); // Posición inicial J2
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
camera.position.set(0, Multi ? 10 : 5, Multi ? 15 : 10); // Más alejada en multijugador

camera.lookAt(0, 0, 0);

// Renderizador
const renderer = new THREE.WebGLRenderer();
renderer.setSize(window.innerWidth, window.innerHeight);
document.body.appendChild(renderer.domElement);

// Textura del escenario 
const textureLoader = new THREE.TextureLoader();
const TexturaPasto = textureLoader.load('grass.jpg');
TexturaPasto.wrapS = THREE.RepeatWrapping;
TexturaPasto.wrapT = THREE.RepeatWrapping;
TexturaPasto.repeat.set(10, 10);

// Tamaño de nuestro escenario y límites
const groundSize = 200; // Usado para el plano
const fenceLimits = { // Define los límites donde el jugador puede moverse  modificar en dificil
    minX: -40, // Ajusta estos valores según el tamaño de tu cerca visible
    maxX: 30,
    minZ: -20,
    maxZ: 24};
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

let player2;
let mixer2;
let actions2 = {};
let currentAction2 = null;
let playerModelNameP2 = 'Perro'; // Asumimos que J2 es el perro en multijugador
let isMoving2 = false;
let isRunning2 = false;
let isCatching2 = false;
if(!Multi){
const selectedCharacter = localStorage.getItem('selectedCharacter') || 'Granjero';

 if (selectedCharacter === 'Granjero') {
    let playerModelNameP1 = 'Granjero';
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
         defeat: mixer.clipAction(findAnimation(animations, "Defeat"))
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
   let playerModelNameP1 = 'Perro';
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
     Object.values(actions).forEach(action => {
            action.enabled = true;
            action.setEffectiveTimeScale(1);
            action.setEffectiveWeight(1);
        });
        currentAction = actions.idle;
        if(currentAction) currentAction.play();
        console.log('Modelo Perro y animaciones cargados');
    }, undefined, (error) => console.error("Error al cargar el modelo Perro:", error));
}
}else{

 let playerModelNameP1 = 'Granjero'
 loader.load('../escenario/models/granjero.glb', (gltf) => {
        player = gltf.scene;
        player.scale.set(2, 2, 2);
        player.position.set(0, 0, 0); // Asegúrate que la Y sea correcta para estar sobre el suelo
        scene.add(player);
        mixer = new THREE.AnimationMixer(player);
        const animations = gltf.animations;
        actions = {
            idle: mixer.clipAction(findAnimation(animations, "Idle")),
            walk: mixer.clipAction(findAnimation(animations, "Walk")),
            run: mixer.clipAction(findAnimation(animations, "Run")),
            victory: mixer.clipAction(findAnimation(animations, "Victory")),
            defeat: mixer.clipAction(findAnimation(animations, "Defeat"))
        };
        Object.values(actions).forEach(action => {
            action.enabled = true;
            action.setEffectiveTimeScale(1);
            action.setEffectiveWeight(1);
        });
        currentAction = actions.idle;
        if(currentAction) currentAction.play();
        console.log('Modelo Granjero y animaciones cargados');
    }, undefined, (error) => console.error("Error al cargar el modelo Granjero:", error));

 loader.load('../escenario/models/dog.glb', (gltf) => {
        player2 = gltf.scene;
        player2.scale.set(0.2, 0.2, 0.2);
        player2.position.set(10, 0, 0); // Ajusta la Y si es necesario
        scene.add(player2);
        mixer2 = new THREE.AnimationMixer(player2);
        const animations2 = gltf.animations;
        actions2 = { // Asegúrate que los nombres de animación coincidan con tu modelo de perro
            idle: mixer2.clipAction(findAnimation(animations2, "Idle1")), // o el nombre correcto
            walk: mixer2.clipAction(findAnimation(animations2, "WalkCycle")),
            run: mixer2.clipAction(findAnimation(animations2, "RunCycle")),
            victory: mixer2.clipAction(findAnimation(animations2, "SitScratchEar")),
            defeat: mixer2.clipAction(findAnimation(animations2, "LayDown"))
        };
        Object.values(actions2).forEach(action2 => {
            action2.enabled = true;
            action2.setEffectiveTimeScale(1);
            action2.setEffectiveWeight(1);
        });
        currentAction2 = actions2.idle;
        if(currentAction2) currentAction2.play();
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
function transitionToAnimationP2(newAction, duration, loop = true) {
    if (!actions2 || Object.keys(actions2).length === 0 || !newAction) return;
    if (currentAction2 === newAction && newAction.isRunning()) return;
    const previousAction = currentAction2;
    currentAction2 = newAction;
    if (previousAction && previousAction !== currentAction2) previousAction.fadeOut(duration);
    currentAction2.reset().setEffectiveTimeScale(1).setEffectiveWeight(1).fadeIn(duration).play();
    if (!loop) { currentAction2.clampWhenFinished = true; currentAction2.loop = THREE.LoopOnce; }
    else { currentAction2.loop = THREE.LoopRepeat; }
}
// Función para actualizar la animación según el estado
// Función para actualizar la animación según el estado
function updateAnimationState() { // Renombrada para claridad
    if (isGameOver || isPaused) return; // No actualizar animaciones si el juego terminó o está pausado

    let newActionKey = 'idle'; // Por defecto, 'idle'


    if (isMoving) {
        newActionKey = isRunning ? 'run' : 'walk';
    }

    if (actions[newActionKey] && currentAction !== actions[newActionKey]) {
        transitionToAnimation(actions[newActionKey], 0.2);
    }
}
function updateAnimationStateP2() {
    if (isGameOver || isPaused) return;
    let newActionKey = 'idle';
 
    if (isMoving2) newActionKey = isRunning2 ? 'run' : 'walk';
    if (actions2[newActionKey] && currentAction2 !== actions2[newActionKey]) {
        transitionToAnimationP2(actions2[newActionKey], 0.2);
    }
}
// Manejador para cuando la animación de atrapar termina


const chickens = [];
const chickenSpeed = 1.8; // Puedes ajustar esta velocidad
const chickenFleeSpeed = 3; // Velocidad cuando huyen  modificar en cada diff
const chickenDetectionRadius = 8; // Radio en el que la gallina detecta al jugador
const chickenWanderTimeMin = 2000; // Milisegundos mínimos para deambular en una dirección
const chickenWanderTimeMax = 5000; // Milisegundos máximos
const chickenCount = 7;  //modificar en cada diff
const targetCatchCount = 15;

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
const numTraps = 20; // Define cuántas trampas quieres modificar en cada diff
const trapDamageTime = 15;


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

// Cerca de madera
let entrance;

loader.load('../escenario/models/cercaMed.glb', (gltf) => {
    entrance = gltf.scene;
    entrance.scale.set(3, 2, 3); 
    entrance.position.set(0, 0, 0); 
    scene.add(entrance);
    console.log('Modelo de cerca cargado ');
}, undefined, (error) => {
    console.error("Error al cargar el modelo de cerca:", error);
});

let PowerSpeed= 1.3;
let rayo;
let PowerTime= 20;
 let reloj;
loader.load('../escenario/models/rayo.glb', (gltf) => {
    // NO uses const rayo = gltf.scene; USA:
    rayo = gltf.scene; // Asigna a la variable global 'rayo'
    if (rayo) { // Siempre verifica si la carga fue exitosa
        rayo.scale.set(3.0, 3.0, 3.0);
        const margin = 15; // Un pequeño margen para que no aparezcan pegadas a la cerca. Ajusta si es necesario.
        const randomX = Math.random() * (fenceLimits.maxX - margin - (fenceLimits.minX + margin)) + (fenceLimits.minX + margin);
        const randomZ = Math.random() * (fenceLimits.maxZ - margin - (fenceLimits.minZ + margin)) + (fenceLimits.minZ + margin);
        const rayoPosition = new THREE.Vector3(randomX, 0, randomZ); // Altura 1 puede estar bien
        rayo.position.copy(rayoPosition);
        scene.add(rayo); // ¡IMPORTANTE! Añadir el modelo a la escena
        console.log('Modelo de rayo cargado');
    } else {
        console.error("Error: gltf.scene para rayo es undefined");
    }
}, undefined, (error) => console.error("Error al cargar el modelo de rayo:", error));

loader.load('../escenario/models/clock.glb', (gltf) => {
    // NO uses const reloj = gltf.scene; USA:
    reloj = gltf.scene; // Asigna a la variable global 'reloj'
    if (reloj) {
        reloj.scale.set(0.2, 0.2, 0.2);
        const margin = 15; // Un pequeño margen para que no aparezcan pegadas a la cerca. Ajusta si es necesario.
        const randomX = Math.random() * (fenceLimits.maxX - margin - (fenceLimits.minX + margin)) + (fenceLimits.minX + margin);
        const randomZ = Math.random() * (fenceLimits.maxZ - margin - (fenceLimits.minZ + margin)) + (fenceLimits.minZ + margin);
        const relojPosition = new THREE.Vector3(randomX, 1, randomZ); // Altura 1
        reloj.position.copy(relojPosition);
        scene.add(reloj); // ¡IMPORTANTE! Añadir el modelo a la escena
        console.log('Modelo de reloj cargado');
    } else {
        console.error("Error: gltf.scene para reloj es undefined");
    }
}, undefined, (error) => console.error("Error al cargar el modelo de reloj:", error));

// Trees
let trees;

loader.load('../escenario/models/treesMed.glb', (gltf) => {
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

// Jugador 2 (Teclas de Flecha + Control Derecho/Shift Derecho si es posible o alguna otra tecla)
let playerSpeed2 = 0.1; // Velocidad base P2
let moveForward2 = false;
let moveBackward2 = false;
let moveLeft2 = false;
let moveRight2 = false;
// isRunning2 ya está definido para P2

document.addEventListener('keydown', (event) => {
    if (isGameOver) return;

    // Controles Jugador 1 (WASD)
    if (event.key === 'w' || event.key === 'W') moveForward = true;
    if (event.key === 's' || event.key === 'S') moveBackward = true;
    if (event.key === 'a' || event.key === 'A') moveLeft = true;
    if (event.key === 'd' || event.key === 'D') moveRight = true;
    if (event.key === 'Shift') isRunning = true; // Shift izquierdo para P1

    // Controles Jugador 2 (Teclas de Flecha)
    if (Multi) {
        if (event.key === 'ArrowUp') moveForward2 = true;
        if (event.key === 'ArrowDown') moveBackward2 = true;
        if (event.key === 'ArrowLeft') moveLeft2 = true;
        if (event.key === 'ArrowRight') moveRight2 = true;
        if (event.key === ' ' ) isRunning2 = true; // Control Derecho o Num0 para correr P2 (ejemplo)
    }

    // Pausa (común para ambos)
    if (event.key === 'Escape' || event.key === 'p' || event.key === 'P') {
        isPaused = !isPaused;
        if (isPaused) showPauseMenu();
        else hidePauseMenu();
    }
});

document.addEventListener('keyup', (event) => {
    if (isGameOver) return;

    // Controles Jugador 1
    if (event.key === 'w' || event.key === 'W') moveForward = false;
    if (event.key === 's' || event.key === 'S') moveBackward = false;
    if (event.key === 'a' || event.key === 'A') moveLeft = false;
    if (event.key === 'd' || event.key === 'D') moveRight = false;
    if (event.key === 'Shift') isRunning = false;

    // Controles Jugador 2
    if (Multi) {
        if (event.key === 'ArrowUp') moveForward2 = false;
        if (event.key === 'ArrowDown') moveBackward2 = false;
        if (event.key === 'ArrowLeft') moveLeft2 = false;
        if (event.key === 'ArrowRight') moveRight2 = false;
        if (event.key === ' ' ) isRunning2 = false;
    }
});
// --- FIN CONTROLES ---
// Contador de gallinas atrapadas
let caughtChickens = 0;


// Inicializar temporizador al cargar el juego
updateTimerDisplay();
startTimer();

let particleSystems = []; 



// --- FUNCIÓN PARA CREAR UN SISTEMA DE PARTÍCULAS DE HUMO ---
function createSmokeParticles(particleCount = 50, position = new THREE.Vector3(0, 0, 0)) {
    const particlesGeometry = new THREE.BufferGeometry();
    const vertices = [];
    const velocities = []; // Para almacenar la velocidad de cada partícula
    const lifespans = [];  // Para almacenar el tiempo de vida de cada partícula

    const textureLoaderParticles = new THREE.TextureLoader();
    const smokeTexture = textureLoaderParticles.load('smoke.png'); // Asegúrate que esta ruta sea correcta

    const particlesMaterial = new THREE.PointsMaterial({
        size: 0.5, // Tamaño inicial de las partículas, puedes ajustarlo
        map: smokeTexture,
        blending: THREE.NormalBlending, // Prueba también AdditiveBlending
        transparent: true,
        depthWrite: false, // Importante para la transparencia correcta a menudo
        opacity: 0.7, // Opacidad inicial
         color: 0x888888 // Puedes teñir el humo si quieres (ej. gris)
    });

    for (let i = 0; i < particleCount; i++) {
        // Posición inicial alrededor del punto de origen
        const x = position.x + (Math.random() - 0.5) * 0.5; // Pequeña dispersión inicial
        const y = position.y + (Math.random() - 0.5) * 0.2;
        const z = position.z + (Math.random() - 0.5) * 0.5;
        vertices.push(x, y, z);

        // Velocidad inicial (principalmente hacia arriba y dispersándose)
        velocities.push(
            (Math.random() - 0.5) * 0.01, // Velocidad X
            Math.random() * 0.01 + 0.01,   // Velocidad Y (hacia arriba)
            (Math.random() - 0.5) * 0.01  // Velocidad Z
        );

        // Tiempo de vida (en segundos)
        lifespans.push(Math.random() * 1.5 + 0.5); // Entre 0.5 y 2 segundos
    }

    particlesGeometry.setAttribute('position', new THREE.Float32BufferAttribute(vertices, 3));
    
    const smokeSystem = new THREE.Points(particlesGeometry, particlesMaterial);
    smokeSystem.userData = {
        velocities: velocities,
        lifespans: lifespans,
        initialLifespans: [...lifespans], // Copia para resetear
        initialPositions: [...vertices],  // Copia para resetear
        particleCount: particleCount,
        origin: position.clone(),
        age: 0, // Edad del sistema de partículas, para desvanecerlo globalmente si es un "puf"
        maxAge: 2.0 // Cuánto tiempo dura el efecto de "puf" de humo
    };

    scene.add(smokeSystem);
    particleSystems.push(smokeSystem); // Añadir a nuestra lista para actualizar
    return smokeSystem;
}

// Animación
function animate() {
    requestAnimationFrame(animate);
    const delta = clock.getDelta();
    const currentTimeForTraps = Date.now(); // Renombrado para evitar conflicto con 'currentTime' de IA gallinas

    // Actualizar mixers
    if (mixer) mixer.update(delta);
    if (Multi && mixer2) mixer2.update(delta); // Actualizar mixer de P2

    bearTraps.forEach(trap => { if (trap.mixer) trap.mixer.update(delta); });

    if (isGameOver) { renderer.render(scene, camera); return; }

    if (!isPaused) {
        // --- LÓGICA JUGADOR 1 ---
        if (player) {
            const wasMovingP1 = isMoving;
            isMoving = moveForward || moveBackward || moveLeft || moveRight;
            if ((wasMovingP1 !== isMoving ) ) { // Solo P1 usa isCatching para animacion de catch
                 updateAnimationState();
            }

            const currentSpeedP1 = (isRunning ? playerSpeed * 2 : playerSpeed);
            if (moveForward) player.position.z -= currentSpeedP1;
            if (moveBackward) player.position.z += currentSpeedP1;
            if (moveLeft) player.position.x -= currentSpeedP1;
            if (moveRight) player.position.x += currentSpeedP1;

            player.position.x = Math.max(fenceLimits.minX, Math.min(fenceLimits.maxX, player.position.x));
            player.position.z = Math.max(fenceLimits.minZ, Math.min(fenceLimits.maxZ, player.position.z));

            // Rotación P1
            if (moveBackward && moveRight) player.rotation.y = Math.PI / 4;
            else if (moveBackward && moveLeft) player.rotation.y = -Math.PI / 4;
            else if (moveForward && moveRight) player.rotation.y = 3 * Math.PI / 4;
            else if (moveForward && moveLeft) player.rotation.y = -3 * Math.PI / 4;
            else if (moveBackward) player.rotation.y = 0;
            else if (moveForward) player.rotation.y = Math.PI;
            else if (moveRight) player.rotation.y = Math.PI / 2;
            else if (moveLeft) player.rotation.y = -Math.PI / 2;

            if (rayo && rayo.visible && player.position.distanceTo(rayo.position) < 2.0) { // Verifica que rayo exista y sea visible
        playerSpeed = playerSpeed * PowerSpeed;
        rayo.visible = false; // Oculta el rayo
        showNotification("¡Velocidad aumentada!", 2000);
        // Podrías querer removerlo de la escena y del array de power-ups si tienes uno
        // O programar que reaparezca después de un tiempo.
    }

    // Colisión con reloj
    if (reloj && reloj.visible && player.position.distanceTo(reloj.position) < 1.5) { // Verifica que reloj exista y sea visible
        gameTime += PowerTime;
         if (gameTime > 120) gameTime = 120; // Opcional: Limitar el tiempo máximo
        updateTimerDisplay();
        reloj.visible = false; // Oculta el reloj
        showNotification(`+${PowerTime} segundos!`, 2000);
    }

    // Lógica similar para player2 si es multijugador
    if (Multi && player2) {
        if (rayo && rayo.visible && player2.position.distanceTo(rayo.position) < 2.0) {
            playerSpeed2 = playerSpeed2 * PowerSpeed; // Asumiendo PowerSpeed afecta a ambos o tienes playerSpeed2PowerSpeed
            rayo.visible = false;
            showNotification("¡Jugador 2 obtuvo velocidad aumentada!", 2000);
        }
        if (reloj && reloj.visible && player2.position.distanceTo(reloj.position) < 1.5) {
            gameTime += PowerTime;
             if (gameTime > 120) gameTime = 120;
            updateTimerDisplay();
            reloj.visible = false;
            showNotification(`¡Jugador 2 obtuvo +${PowerTime} segundos!`, 2000);
        }
    }

            // Colisión P1 con trampas
            bearTraps.forEach(trap => {
                if (trap.model && player.position.distanceTo(trap.position) < 1.5) {
                    if (currentTimeForTraps - trap.lastActivationTime > trapCooldown) {
                        if (trap.action && !trap.action.isRunning()) {
                            // ... (lógica de activar trampa y penalización, igual que antes) ...
                            // Asegúrate que el gameTime y la notificación sean comunes.
                            trap.action.reset().play();
                            gameTime -= trapDamageTime;
                            if (gameTime < 0) gameTime = 0;
                            updateTimerDisplay();
                            createSmokeParticles(50, trap.position.clone().add(new THREE.Vector3(0, 0.5, 0)));
                            showNotification(`¡Cuidado! Jugador 1 pisó una trampa. Pierden ${trapDamageTime} seg.`);
                            trap.lastActivationTime = currentTimeForTraps;
                            if (gameTime <= 0 && !isGameOver) gameOver("¡Se acabó el tiempo por una trampa!");
                        }
                    }
                }
            });
        }

        // --- LÓGICA JUGADOR 2 (Si es Multijugador) ---
        if (Multi && player2) {
            const wasMovingP2 = isMoving2;
            isMoving2 = moveForward2 || moveBackward2 || moveLeft2 || moveRight2;
            // El perro no tiene animación de 'isCatching' por ahora
            if (wasMovingP2 !== isMoving2) {
                updateAnimationStateP2();
            }

            const currentSpeedP2 = (isRunning2 ? playerSpeed2 * 2 : playerSpeed2);
            if (moveForward2) player2.position.z -= currentSpeedP2;
            if (moveBackward2) player2.position.z += currentSpeedP2;
            if (moveLeft2) player2.position.x -= currentSpeedP2;
            if (moveRight2) player2.position.x += currentSpeedP2;

            player2.position.x = Math.max(fenceLimits.minX, Math.min(fenceLimits.maxX, player2.position.x));
            player2.position.z = Math.max(fenceLimits.minZ, Math.min(fenceLimits.maxZ, player2.position.z));
            
            // Rotación P2
            if (moveBackward2 && moveRight2) player2.rotation.y = Math.PI / 4;
            else if (moveBackward2 && moveLeft2) player2.rotation.y = -Math.PI / 4;
            else if (moveForward2 && moveRight2) player2.rotation.y = 3 * Math.PI / 4;
            else if (moveForward2 && moveLeft2) player2.rotation.y = -3 * Math.PI / 4;
            else if (moveBackward2) player2.rotation.y = 0;
            else if (moveForward2) player2.rotation.y = Math.PI;
            else if (moveRight2) player2.rotation.y = Math.PI / 2;
            else if (moveLeft2) player2.rotation.y = -Math.PI / 2;

            // Colisión P2 con trampas
            bearTraps.forEach(trap => {
                if (trap.model && player2.position.distanceTo(trap.position) < 1.5) {
                    if (currentTimeForTraps - trap.lastActivationTime > trapCooldown) {
                        if (trap.action && !trap.action.isRunning()) {
                            trap.action.reset().play();
                            gameTime -= trapDamageTime;
                            if (gameTime < 0) gameTime = 0;
                            updateTimerDisplay();
                            createSmokeParticles(50, trap.position.clone().add(new THREE.Vector3(0, 0.5, 0)));
                            showNotification(`¡Cuidado! Jugador 2 pisó una trampa. Pierden ${trapDamageTime} seg.`);
                            trap.lastActivationTime = currentTimeForTraps;
                            if (gameTime <= 0 && !isGameOver) gameOver("¡Se acabó el tiempo por una trampa!");
                        }
                    }
                }
            });
        }

        // --- CÁMARA MULTIJUGADOR (Ejemplo simple: punto medio o más alejada) ---
        if (Multi && player && player2) {
            const midPoint = new THREE.Vector3().addVectors(player.position, player2.position).multiplyScalar(0.5);
            camera.position.set(midPoint.x, camera.position.y, midPoint.z + 15); // Ajusta el '15' y 'camera.position.y'
            camera.lookAt(midPoint);
        } else if (player) { // Cámara un solo jugador
            camera.position.x = player.position.x;
            camera.position.z = player.position.z + 10;
            camera.position.y = player.position.y + 5;
            camera.lookAt(player.position);
        }


        // --- LÓGICA DE GALLINAS (IA y Captura) ---
        const currentTimeIA = Date.now(); // Para la IA de gallinas
        chickens.forEach((chicken) => {
            if (chicken) {
                const chickenData = chicken.userData;
                let distanceToPlayer1 = Infinity;
                let distanceToPlayer2 = Infinity;
                let fleeFrom = null; // De qué jugador huir

                if (player) distanceToPlayer1 = chicken.position.distanceTo(player.position);
                if (Multi && player2) distanceToPlayer2 = chicken.position.distanceTo(player2.position);

                // La gallina huye del jugador más cercano si está dentro del radio
                if (distanceToPlayer1 < chickenDetectionRadius && distanceToPlayer1 <= distanceToPlayer2) {
                    chickenData.state = 'fleeing';
                    fleeFrom = player.position;
                } else if (Multi && player2 && distanceToPlayer2 < chickenDetectionRadius && distanceToPlayer2 < distanceToPlayer1) {
                    chickenData.state = 'fleeing';
                    fleeFrom = player2.position;
                } else {
                    chickenData.state = 'wandering';
                }
                // ... (resto de la lógica de movimiento de gallina usando 'fleeFrom' si state es 'fleeing') ...
                // La pegué de tu código, verifica la condición 'fleeFrom'
                let moveDirection = new THREE.Vector3();
                let currentChickenSpeed = chickenData.state === 'fleeing' ? chickenFleeSpeed : chickenSpeed;

                if (chickenData.state === 'fleeing' && fleeFrom) {
                    moveDirection.subVectors(chicken.position, fleeFrom).normalize();
                } else { // wandering
                    if (currentTimeIA - chickenData.lastWanderUpdateTime > chickenData.timeToNextWander) {
                        chickenData.wanderDirection.set((Math.random() - 0.5) * 2, 0, (Math.random() - 0.5) * 2).normalize();
                        chickenData.timeToNextWander = Math.random() * (chickenWanderTimeMax - chickenWanderTimeMin) + chickenWanderTimeMin;
                        chickenData.lastWanderUpdateTime = currentTimeIA;
                    }
                    moveDirection.copy(chickenData.wanderDirection);
                }
                chicken.position.x += moveDirection.x * currentChickenSpeed * delta;
                chicken.position.z += moveDirection.z * currentChickenSpeed * delta;
                pointChickenInDirection(chicken, moveDirection);
                chicken.position.x = Math.max(fenceLimits.minX + 1, Math.min(fenceLimits.maxX - 1, chicken.position.x));
                chicken.position.z = Math.max(fenceLimits.minZ + 1, Math.min(fenceLimits.maxZ - 1, chicken.position.z));


                // Detección de colisión para atrapar (ambos jugadores)
                if (player && !isCatching && chicken.position.distanceTo(player.position) < 1.5) {
                     // Perro (P1 en single player) o Granjero sin animación de catch configurada
                        handleChickenCaught(chicken, player); // Pasar el jugador que atrapó
                    
                } else if (Multi && player2 && /*!isCatching2 (si P2 tuviera animacion de catch) &&*/ chicken.position.distanceTo(player2.position) < 1.5) {
                    // El perro (P2) atrapa instantáneamente
                    handleChickenCaught(chicken, player2); // Pasar el jugador que atrapó
                }
            }
        });

        // Lógica de atrapar gallina
        function handleChickenCaught(chicken, captor) { // 'captor' es el jugador que la atrapó
            chicken.position.set(
                Math.random() * (fenceLimits.maxX - fenceLimits.minX) + fenceLimits.minX,
                0.5,
                Math.random() * (fenceLimits.maxZ - fenceLimits.minZ) + fenceLimits.minZ
            );
            caughtChickens++; // Contador común
            counterElement.textContent = `Gallinas atrapadas: ${caughtChickens}`;

            // Lógica de victoria común
            if (caughtChickens >= targetCatchCount && !isGameOver) {
                gameOver("¡Han atrapado todas las gallinas!");
                // Podrías reproducir animación de victoria para AMBOS jugadores si están definidos
                if (actions.victory && player === captor) transitionToAnimation(actions.victory, 0.2, false);
                if (Multi && actions2.victory && player2 === captor) transitionToAnimationP2(actions2.victory, 0.2, false);
            }
        }

        for (let i = particleSystems.length - 1; i >= 0; i--) {
        const system = particleSystems[i];
        const positions = system.geometry.attributes.position.array;
        const velocities = system.userData.velocities;
        const lifespans = system.userData.lifespans;
        const particleCount = system.userData.particleCount;
        let allParticlesDead = true;

        system.userData.age += delta; // Envejecer el sistema de "puf"

        for (let j = 0; j < particleCount; j++) {
            lifespans[j] -= delta; // Reducir tiempo de vida

            if (lifespans[j] > 0) {
                allParticlesDead = false;
                // Actualizar posición
                positions[j * 3] += velocities[j * 3] * (1 + system.userData.age); // Dispersión aumenta con la edad del sistema
                positions[j * 3 + 1] += velocities[j * 3 + 1]; // Movimiento Y (ascendente)
                positions[j * 3 + 2] += velocities[j * 3 + 2] * (1 + system.userData.age);

                // Simular algo de "fricción" o desaceleración (opcional)
                // velocities[j * 3 + 1] *= 0.98; // Desaceleración en Y

            } else {
                // Partícula "muerta", podrías resetearla aquí para un flujo continuo,
                // o simplemente dejarla así si es un efecto de una sola vez por sistema.
                // Para un "puf" que desaparece, no la reseteamos individualmente.
            }
        }

        system.geometry.attributes.position.needsUpdate = true;

        // Hacer que todo el sistema de "puf" se desvanezca
        if (system.userData.age < system.userData.maxAge) {
            system.material.opacity = Math.max(0, 0.7 * (1 - (system.userData.age / system.userData.maxAge)));
        } else {
            system.material.opacity = 0; // Asegurar que desaparezca
        }
        

        // Si todas las partículas están muertas o el sistema es muy viejo, eliminarlo
        if (system.material.opacity <= 0 && system.userData.age >= system.userData.maxAge) {
            scene.remove(system);
            system.geometry.dispose();
            system.material.dispose();
            particleSystems.splice(i, 1); // Eliminar del array
            console.log("Sistema de humo eliminado");
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