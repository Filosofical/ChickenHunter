<?php
// Inicia la sesión
session_start();

// Verifica que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No estás logueado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtén los parámetros enviados
    $idProducto = $_GET['id_producto'];
    $idVendedor = $_GET['id_vendedor'];

    // Conectar a la base de datos
    $conexion = mysqli_connect("localhost", "root", "", "sistemadeventas");

    if (!$conexion) {
        echo json_encode(['success' => false, 'message' => 'No se pudo conectar a la base de datos']);
        exit();
    }

    // Consulta para obtener los mensajes
    $query = "SELECT m.Mensaje, m.Fecha, u.Nombre_Usuario
              FROM mensajes m
              INNER JOIN usuario u ON m.ID_Usuario = u.ID_Usuario
              WHERE m.ID_Producto = '$idProducto' AND m.ID_Vendedor = '$idVendedor'
              ORDER BY m.Fecha ASC";

    $result = mysqli_query($conexion, $query);

    $mensajes = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $mensajes[] = $row;
    }

    echo json_encode(['success' => true, 'mensajes' => $mensajes]);

    mysqli_close($conexion);
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
