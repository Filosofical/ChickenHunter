CREATE database Chicken_Hunter;
use Chicken_Hunter;
-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tabla de partidas o puntuaciones
CREATE TABLE partidas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    puntuacion INT,
    dificultad int, -- 1facil 2medio 3dificil
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla para datos del multijugador (si decides guardar info de cada partida)
CREATE TABLE partidas_multijugador (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jugador1_id INT,
    jugador2_id INT,
    puntuacion INT,
 dificultad int,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (jugador1_id) REFERENCES usuarios(id),
    FOREIGN KEY (jugador2_id) REFERENCES usuarios(id)
);
