
<?php
header('Content-Type: application/json'); // Es buena práctica indicar que la respuesta también será JSON
require 'DB_conn.php'; // Asegúrate que este archivo no tenga errores y establezca tu conexión ($conn)

// session_start(); // Descomenta si realmente necesitas iniciar sesión para esta operación específica.
                  // Si idUsuario1 viene del cliente y no de $_SESSION, quizás no sea necesario aquí.

// Leer el cuerpo de la solicitud JSON
$json_payload = file_get_contents('php://input');
// Decodificar el JSON en un array asociativo
$data = json_decode($json_payload, true);

$response = ['status' => 'error', 'message' => 'Datos inválidos o incompletos.'];

// Verificar si la decodificación fue exitosa y si $data no es null
if ($data !== null) {
    // Usar los nombres de clave que envías desde JavaScript
    $idUsuario1 = $data['idUsuario1'] ?? null;
    $idUsuario2 = $data['idUsuario2'] ?? null;
    $dificultad = $data['dificultad'] ?? null;
    $puntaje = $data['puntaje'] ?? null;

 
    if ($idUsuario1 !== null && $idUsuario2 !== null && $dificultad !== null && $puntaje !== 0) {
        

        $idUsuario1 = filter_var($idUsuario1, FILTER_VALIDATE_INT);
        $idUsuario2 = filter_var($idUsuario2, FILTER_VALIDATE_INT);
        $dificultad = filter_var($dificultad, FILTER_VALIDATE_INT);
        $puntaje = filter_var($puntaje, FILTER_VALIDATE_INT);

        if ($idUsuario1 === false ||$idUsuario2 === false || $dificultad === false || $puntaje === false) {
            $response = ['status' => 'error', 'message' => 'Tipos de datos inválidos para idUsuario1, dificultad o puntaje.'];
        } else {
         
            try {
         
                 $sql = "INSERT INTO partidas_multijugador (jugador1_id,jugador2_id, puntuacion, dificultad ) VALUES (?,?, ?, ?)";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("iii", $idUsuario1,$idUsuario2, $puntaje, $dificultad);
                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'Puntaje guardado correctamente.'];
                } else {
                    $response = ['status' => 'error', 'message' => 'Error al ejecutar la inserción: ' . $stmt->error];
                }
                $stmt->close();
            } else {
                $response = ['status' => 'error', 'message' => 'Error al preparar la consulta: ' . $conn->error];
            }
                // Cerrar la conexión después de usarla
            } catch (PDOException $e) {
                // En producción, no muestres $e->getMessage() directamente al usuario. Loggéalo.
                error_log("Error de base de datos: " . $e->getMessage()); // Loggear el error
                $response = ['status' => 'error', 'message' => 'Error de conexión o consulta a la base de datos.'];
            }
           
            
        }
    } else {
        $missing_fields = [];
        if ($idUsuario1 === null) $missing_fields[] = 'idUsuario1';
        if ($idUsuario2 === null) $missing_fields[] = 'idUsuario2';
        if ($dificultad === null) $missing_fields[] = 'dificultad';
        if ($puntaje === null) $missing_fields[] = 'puntaje';
        $response = ['status' => 'error', 'message' => 'Faltan campos requeridos: ' . implode(', ', $missing_fields), 'received_data' => $data];
    }
} else {
     $response = ['status' => 'error', 'message' => 'No se pudo decodificar el payload JSON o no se recibieron datos.'];
}

// Enviar la respuesta JSON de vuelta al cliente
echo json_encode($response); $conn->close();
?>