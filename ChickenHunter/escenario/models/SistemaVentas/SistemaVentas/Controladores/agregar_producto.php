<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/Login.php");
    exit();
}

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$datab = "sistemadeventas";

$connection = mysqli_connect($host, $user, $pass, $datab);

if (!$connection) {
    die("No se ha podido conectar a la base de datos: " . mysqli_error($connection));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_lista = $_POST['id_lista'];
    $id_producto = $_POST['id_producto'];

    // Validar que la lista pertenece al usuario logueado
    $id_usuario = $_SESSION['user_id'];
    $sql_validar_lista = "SELECT id_lista FROM listas WHERE id_lista = ? AND id_usuario = ?";
    $stmt_validar = $connection->prepare($sql_validar_lista);
    $stmt_validar->bind_param("ii", $id_lista, $id_usuario);
    $stmt_validar->execute();
    $result_validar = $stmt_validar->get_result();

    if ($result_validar->num_rows === 0) {
        echo "Error: La lista seleccionada no pertenece al usuario.";
        exit();
    }

    // Insertar el producto en la lista
    $sql_agregar_producto = "INSERT INTO lista_productos (id_lista, id_producto) VALUES (?, ?)";
    $stmt_agregar = $connection->prepare($sql_agregar_producto);
    $stmt_agregar->bind_param("ii", $id_lista, $id_producto);

    if ($stmt_agregar->execute()) {
        echo "Producto agregado a la lista exitosamente.";
        header("Location: ../Contenido/Listas.php");
    } else {
        echo "Error al agregar el producto a la lista: " . $connection->error;
    }

    $stmt_agregar->close();
    $stmt_validar->close();
}

$connection->close();
?>

