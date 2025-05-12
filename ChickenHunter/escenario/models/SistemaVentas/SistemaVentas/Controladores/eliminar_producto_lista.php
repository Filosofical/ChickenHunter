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

if (isset($_GET['id_lista']) && isset($_GET['id_producto'])) {
    $id_lista = $_GET['id_lista'];
    $id_producto = $_GET['id_producto'];
    $id_usuario = $_SESSION['user_id'];

    // Verificar que la lista pertenece al usuario
    $sql_validar = "SELECT id_lista FROM listas WHERE id_lista = ? AND id_usuario = ?";
    $stmt_validar = $connection->prepare($sql_validar);
    $stmt_validar->bind_param("ii", $id_lista, $id_usuario);
    $stmt_validar->execute();
    $result_validar = $stmt_validar->get_result();

    if ($result_validar->num_rows > 0) {
        // Eliminar el producto de la lista
        $sql_eliminar = "DELETE FROM lista_productos WHERE id_lista = ? AND id_producto = ?";
        $stmt_eliminar = $connection->prepare($sql_eliminar);
        $stmt_eliminar->bind_param("ii", $id_lista, $id_producto);

        if ($stmt_eliminar->execute()) {
            header("Location: ../Contenido/Listas.php");
        } else {
            echo "Error al eliminar el producto: " . $connection->error;
        }
        $stmt_eliminar->close();
    } else {
        echo "Error: No tienes permiso para modificar esta lista.";
    }

    $stmt_validar->close();
} else {
    echo "Error: Datos incompletos.";
}

$connection->close();
?>
