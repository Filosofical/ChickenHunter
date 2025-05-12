<?php
// Inicia la sesión
session_start();

// Verifica que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No estás logueado']);
    exit();
}

// Verifica si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtén los datos enviados por AJAX
    $mensaje = $_POST['mensaje'];
    $idProducto = $_POST['id_producto'];
    $idUsuario = $_SESSION['user_id']; // El ID del comprador
    $idVendedor = $_POST['id_vendedor'];

    if (empty($mensaje)) {
        echo json_encode(['success' => false, 'message' => 'El mensaje no puede estar vacío']);
        exit();
    }

    // Conectar a la base de datos
    $conexion = mysqli_connect("localhost", "root", "", "sistemadeventas");

    if (!$conexion) {
        echo json_encode(['success' => false, 'message' => 'No se pudo conectar a la base de datos']);
        exit();
    }

    // Insertar el mensaje en la base de datos
    $query = "INSERT INTO mensajes (ID_Producto, ID_Usuario, ID_Vendedor, Mensaje, Estado) 
              VALUES ('$idProducto', '$idUsuario', '$idVendedor', '$mensaje', 'no leído')";
    
    if (mysqli_query($conexion, $query)) {
        echo json_encode(['success' => true, 'message' => 'Mensaje enviado']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al enviar el mensaje']);
    }

    mysqli_close($conexion);
} else {
    // Si el método no es POST, devuelve un error
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>
