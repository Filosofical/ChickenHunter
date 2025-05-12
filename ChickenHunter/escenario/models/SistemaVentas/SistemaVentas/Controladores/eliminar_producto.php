<?php
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
    $id_lista = intval($_POST['id_lista']);
    $id_producto = intval($_POST['id_producto']);

    // Eliminar el producto de la lista
    $query = "DELETE FROM lista_productos WHERE id_lista = ? AND id_producto = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("ii", $id_lista, $id_producto);

    if ($stmt->execute()) {
        echo "Producto eliminado correctamente.";
        header("Location: ../ver_lista.php?id_lista=$id_lista");
        exit();
    } else {
        echo "Error al eliminar producto: " . $stmt->error;
    }
}

$stmt->close();
$connection->close();
?>
