<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $data['userId'];

    $connection = mysqli_connect("localhost", "root", "", "sistemadeventas");

    if (!$connection) {
        echo json_encode(['success' => false, 'error' => 'No se pudo conectar con la base de datos']);
        exit();
    }

    $query = "SELECT u.ID_Usuario, u.Nombre_Usuario
              FROM contactos c
              INNER JOIN usuario u ON c.id_contacto_usuario = u.ID_Usuario
              WHERE c.id_usuario = $userId AND c.estado = 'activo'";

    $result = mysqli_query($connection, $query);
    $contacts = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $contacts[] = $row;
    }

    echo json_encode(['success' => true, 'contacts' => $contacts]);

    mysqli_close($connection);
}
?>
