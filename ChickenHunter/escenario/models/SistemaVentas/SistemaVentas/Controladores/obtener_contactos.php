<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'No hay ID de usuario en la sesión.']);
    exit;
}

$id_usuario = $_SESSION['user_id'];

$query = "SELECT c.id_contacto, u.Nombre_Completo, u.Foto_Perfil
          FROM contactos c
          INNER JOIN usuario u ON c.id_contacto_usuario = u.ID_Usuario
          WHERE c.id_usuario = ? AND c.estado = 'activo'";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $contactos = [];
    while ($row = $result->fetch_assoc()) {
        $contactos[] = $row;
    }
    echo json_encode($contactos);
} else {
    echo json_encode(['error' => 'No se encontraron contactos activos.']);
}

$stmt->close();
$conn->close();

?>
