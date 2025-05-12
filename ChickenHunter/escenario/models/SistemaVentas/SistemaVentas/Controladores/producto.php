<?php
header('Content-Type: application/json'); // Enviar respuesta JSON

$host = "localhost";
$user = "root";
$pass = "";
$connection = mysqli_connect($host, $user, $pass, "sistemadeventas");

if (!$connection) {
    echo json_encode(['success' => false, 'message' => 'No se pudo conectar con la base de datos']);
    exit;
}

session_start();
$idUsuario = $_SESSION['user_id'] ?? null;
if (!$idUsuario) {
    echo json_encode(['success' => false, 'message' => 'Usuario no autenticado']);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreProducto = $_POST['nombre_producto'];
    $descripcionProducto = $_POST['descripción_producto'];
    $categoriaProducto = $_POST['categoria_producto'];
    $tipoVentaProducto = $_POST['cotizar_vender'];
    $precioProducto = $_POST['precio_producto'];
    $cantidadProducto = $_POST['cantidad_producto'];

    $imagenProducto = $_FILES['imgRutaProducto']['name'];
    $imagenTemp = $_FILES['imgRutaProducto']['tmp_name'];
    move_uploaded_file($imagenTemp, "../uploads/images/" . $imagenProducto);

    $videoProducto = $_FILES['videoRutaProducto']['name'];
    $videoTemp = $_FILES['videoRutaProducto']['tmp_name'];
    move_uploaded_file($videoTemp, "../uploads/videos/" . $videoProducto);

    $instruccion_SQL = "INSERT INTO producto 
        (Nombre_Producto, Descripcion_Producto, ID_Categoria, Tipo_Venta, Precio_Producto, Cantidad_Producto, Foto_Producto, Video_Producto, ID_Usuario)
        VALUES ('$nombreProducto', '$descripcionProducto', '$categoriaProducto', '$tipoVentaProducto', '$precioProducto', '$cantidadProducto', '$imagenProducto', '$videoProducto', '$idUsuario')";

    if (mysqli_query($connection, $instruccion_SQL)) {
        header("Location: ../Contenido/Productos.php");
        exit();
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($connection)]);
        exit();
    }
}

mysqli_close($connection);
?>
