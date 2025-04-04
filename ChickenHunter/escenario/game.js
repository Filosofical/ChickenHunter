let isPaused = false;

// Escena
const scene = new THREE.Scene();
scene.background = new THREE.Color(0x87CEEB); // Cielo azul

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
    window.location.href = '../menu/index.html';
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
const TexturaPasto = textureLoader.load('grass.jpg');
TexturaPasto.wrapS = THREE.RepeatWrapping;
TexturaPasto.wrapT = THREE.RepeatWrapping;
TexturaPasto.repeat.set(10, 10);

// Tamaño de nuestro escenario
const groundGeometry = new THREE.PlaneGeometry(200, 200);
const groundMaterial = new THREE.MeshStandardMaterial({ map: TexturaPasto });
const ground = new THREE.Mesh(groundGeometry, groundMaterial);
ground.rotation.x = -Math.PI / 2;
scene.add(ground);

// Luz ambiental
const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
scene.add(ambientLight);

// Luz direccional (simula el sol)
const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
directionalLight.position.set(10, 10, 10);
scene.add(directionalLight);

// Variables para animaciones
let mixer;
let actions = {};
let currentAction = null;
let isMoving = false;
let isRunning = false;
let isCatching = false;
let isDefeated = false;
let isVictorious = false;
const clock = new THREE.Clock();

// Jugador
const loader = new THREE.GLTFLoader();
let player;
const selectedCharacter = localStorage.getItem('selectedCharacter') || 'Granjero';

 if (selectedCharacter === 'Granjero') {
  
 loader.load('../escenario/models/granjero.glb', (gltf) => {
     player = gltf.scene;
     player.scale.set(2, 2, 2);
     player.position.set(0, 1, 0);
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
     currentAction.play();
    
     console.log('Modelo y animaciones cargados exitosamente');
 }, undefined, (error) => {
     console.error("Error al cargar el modelo:", error);
 });

 } else if (selectedCharacter === 'Perro') {
    loader.load('../escenario/models/dog.glb', (gltf) => {
        player = gltf.scene;
        player.scale.set(.2, .2, .2);
        player.position.set(0, 1, 0);
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
        currentAction.play();
        
        console.log('Modelo y animaciones cargados exitosamente');
    }, undefined, (error) => {
        console.error("Error al cargar el modelo:", error);
    });
}


// Función auxiliar para encontrar animaciones por nombre
function findAnimation(animations, name) {
    return animations.find(anim => anim.name === name);
}

// Función para actualizar la animación según el estado
function updateAnimation() {
    let newAction;
    
    if (isMoving) {
        newAction = isRunning ? actions.run : actions.walk;
    } else {
        newAction = actions.idle;
    }
    
    // Si la animación ya está reproduciéndose, no hacer nada
    if (newAction === currentAction) return;
    // Configurar transición entre animaciones
    currentAction.fadeOut(0.2);
    newAction.reset().fadeIn(0.2).play();
    
    currentAction = newAction;
}

// Gallina
let chicken;
const chickens = [];
const chickenSpeed = 1.0;
const chickenCount = 5;

for (let i = 0; i < chickenCount; i++) {
    loader.load('../escenario/models/chicken.glb', (gltf) => {
        chicken = gltf.scene;
        chicken.scale.set(0.0100, 0.0100, 0.0100);  
        chicken.rotation.set(0, 180, 0); 
        chicken.position.set(Math.random() * 40 - 20, 0.5, Math.random() * 40 - 20);
        scene.add(chicken);
        chickens.push(chicken);
        console.log('Modelo cargado exitosamente');
    }, undefined, (error) => {
        console.error("Error al cargar el modelo:", error);
    });
}

// Palo 
let pitchfork;

loader.load('../escenario/models/pichfork.glb', (gltf) => {
    pitchfork = gltf.scene;
    pitchfork.scale.set(2.0, 2.0, 2.0); 
    pitchfork.position.set(0, 2, 0); 
    scene.add(pitchfork);
    console.log('Modelo cargado exitosamente');
}, undefined, (error) => {
    console.error("Error al cargar el modelo:", error);
});

// Cerca de madera
let entrance;

loader.load('../escenario/models/cerca.glb', (gltf) => {
    entrance = gltf.scene;
    entrance.scale.set(3, 2, 3); 
    entrance.position.set(0, 0, 0); 
    scene.add(entrance);
    console.log('Modelo cargado exitosamente');
}, undefined, (error) => {
    console.error("Error al cargar el modelo:", error);
});

// Trees
let trees;

loader.load('../escenario/models/trees.glb', (gltf) => {
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
const counterElement = document.getElementById('counter');

// Animación
function animate() {
    requestAnimationFrame(animate);
    
    // Actualizar animaciones
    if (mixer) {
        const delta = clock.getDelta();
        mixer.update(delta);
    }
    
    if (!isPaused) {
        // Actualizar estado de movimiento
        const wasMoving = isMoving;
        isMoving = moveForward || moveBackward || moveLeft || moveRight;
        
        // Si el estado de movimiento cambió, actualizar animación
        if (wasMoving !== isMoving) {
            updateAnimation();
        }
        
        // Movimiento del jugador
        if (player) {
            if (moveForward) player.position.z -= isRunning ? playerSpeed * 2 : playerSpeed;
            if (moveBackward) player.position.z += isRunning ? playerSpeed * 2 : playerSpeed;
            if (moveLeft) player.position.x -= isRunning ? playerSpeed * 2 : playerSpeed;
            if (moveRight) player.position.x += isRunning ? playerSpeed * 2 : playerSpeed;
            
            // Rotar el modelo según la dirección
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
            camera.position.z = player.position.z + 10;
            camera.position.y = player.position.y + 5;
            camera.lookAt(player.position);
        }
        
        // Movimiento de las gallinas
        chickens.forEach((chicken) => {
            if (chicken) {
                // Movimiento aleatorio
                chicken.position.x += (Math.random() - 0.5) * chickenSpeed;
                chicken.position.z += (Math.random() - 0.5) * chickenSpeed;
                
                // Detección de colisión con el jugador
                if (player && chicken.position.distanceTo(player.position) < 1) {
                    // Gallina atrapada
                    isCatching = true;
                    updateAnimation();
                    setTimeout(() => {
                        isCatching = false;
                        updateAnimation();
                    }, 1000); // Duración de la animación de atrapar
        
                    chicken.position.set(Math.random() * 40 - 20, 0.5, Math.random() * 40 - 20);
                    caughtChickens++;
                    counterElement.textContent = `Gallinas atrapadas: ${caughtChickens}`;
                }
            }
        });
    }
    
    renderer.render(scene, camera);
}

animate();

// Ajuste del tamaño de la pantalla
window.addEventListener('resize', () => {
    renderer.setSize(window.innerWidth, window.innerHeight);
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
});