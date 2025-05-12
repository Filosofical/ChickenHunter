// agregar_contacto.php
<?php
session_start();
$data = json_decode(file_get_contents('php://input'), true);

$userId = $data['userId'];
$sellerId = $data['sellerId'];

// Conectar a la base de datos
$connection = mysqli_connect("localhost", "root", "", "sistemadeventas");
if (!$connection) {
    die("No se pudo conectar con la base de datos: " . mysqli_error($connection));
}

// Verificar si ya existe el contacto
$query = "SELECT * FROM contactos WHERE id_usuario = $userId AND id_contacto_usuario = $sellerId";
$result = mysqli_query($connection, $query);

if (mysqli_num_rows($result) == 0) {
    // Insertar nuevo contacto
    $insertQuery = "INSERT INTO contactos (id_usuario, id_contacto_usuario, estado) VALUES ($userId, $sellerId, 'pendiente')";
    if (mysqli_query($connection, $insertQuery)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al agregar contacto']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Este contacto ya existe']);
}

mysqli_close($connection);
?>
