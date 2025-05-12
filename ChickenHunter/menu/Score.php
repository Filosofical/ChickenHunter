<?php 
// Función reutilizable para obtener los mejores puntajes por dificultad
function obtenerMejoresPuntajes($conexion, $dificultad, $limite = 5) {
    $puntajes = [];
   
    $sql = "SELECT u.nombre_usuario, p.puntuacion
            FROM partidas p
            INNER JOIN usuarios u ON p.usuario_id = u.id
            WHERE p.dificultad = ?  
            ORDER BY p.puntuacion DESC
            LIMIT ?"; 

    // Usar sentencias preparadas para seguridad y eficiencia
    if ($stmt = $conexion->prepare($sql)) {
   
        $stmt->bind_param("ii", $dificultad, $limite);

      
        if ($stmt->execute()) {
            $resultado = $stmt->get_result();
      
            while ($fila = $resultado->fetch_assoc()) {
                $puntajes[] = $fila;
            }
        } else {
   
             error_log("Error al ejecutar la consulta: " . $stmt->error);
        }
    
        $stmt->close();
    } else {
         
         error_log("Error al preparar la consulta: " . $conexion->error);
    }
    return $puntajes;
}


require '../Back/DB_conn.php'; 

// 2. Iniciar sesión y verificar
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: Sesion.php');
    exit();
}
$userId = $_SESSION['user_id']; 
$userName = $_SESSION['user_name']; 


$puntajes_facil = obtenerMejoresPuntajes($conn, 1, 5);   // Dificultad 1 = Fácil
$puntajes_medio = obtenerMejoresPuntajes($conn, 2, 5);   // Dificultad 2 = Medio
$puntajes_dificil = obtenerMejoresPuntajes($conn, 3, 5);  // Dificultad 3 = Difícil

// 4. Cerrar la conexión a la BD después de obtener todos los datos
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Puntuaciones</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sigmar&display=swap" rel="stylesheet">
</head>
<body>
    <div class="contenedorPrin">
        <header>
            <h1>Chicken Hunter</h1>
            <h6><?php echo htmlspecialchars($userName); ?></h6>

        </header>

        <main>
           <div class="IS">

        <h2>PUNTUACIONES Fácil</h2>
        <table>
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Usuario</th>
                    <th>Puntaje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($puntajes_facil)): ?>
                    <tr><td colspan="3">No hay puntuaciones registradas todavía.</td></tr>
                <?php else: ?>
                    <?php foreach ($puntajes_facil as $indice => $puntaje): ?>
                        <tr>
                            <td><?php echo $indice + 1; ?></td> <td><?php echo htmlspecialchars($puntaje['nombre_usuario']); ?></td> <td><?php echo htmlspecialchars($puntaje['puntuacion']); ?></td> </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h2>PUNTUACIONES Medio</h2>
        <table>
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Usuario</th>
                    <th>Puntaje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($puntajes_medio)): ?>
                    <tr><td colspan="3">No hay puntuaciones registradas todavía.</td></tr>
                <?php else: ?>
                    <?php foreach ($puntajes_medio as $indice => $puntaje): ?>
                        <tr>
                            <td><?php echo $indice + 1; ?></td>
                            <td><?php echo htmlspecialchars($puntaje['nombre_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($puntaje['puntuacion']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h2>PUNTUACIONES Difícil</h2>
        <table>
            <thead>
                <tr>
                    <th>Ranking</th>
                    <th>Usuario</th>
                    <th>Puntaje</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($puntajes_dificil)): ?>
                    <tr><td colspan="3">No hay puntuaciones registradas todavía.</td></tr>
                <?php else: ?>
                    <?php foreach ($puntajes_dificil as $indice => $puntaje): ?>
                        <tr>
                            <td><?php echo $indice + 1; ?></td>
                            <td><?php echo htmlspecialchars($puntaje['nombre_usuario']); ?></td>
                            <td><?php echo htmlspecialchars($puntaje['puntuacion']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <button class="menu-button" id="playButton" onclick="location.href='../menu/index.php'">Volver</button>
        </div> 
        </main>

        <footer>
            <p>Pablo Garcia Garza</p>
            <p>Daniel Flores Santacruz</p>
        </footer>
    </div>
</body>
</html>