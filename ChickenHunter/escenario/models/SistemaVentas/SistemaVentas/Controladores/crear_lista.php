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

// Iniciar sesión para obtener el ID del usuario actual
session_start();

// Verificar si se accede mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $usuario_id = $_SESSION['user_id']; // Asegúrate de que 'user_id' está configurado al iniciar sesión
    $nombre_lista = mysqli_real_escape_string($connection, $_POST['nombre_lista']);
    $descripcion = mysqli_real_escape_string($connection, $_POST['descripcion']);

    // Insertar la nueva lista en la base de datos
    $query = "INSERT INTO listas (id_usuario, nombre_lista, descripcion) VALUES ('$usuario_id', '$nombre_lista', '$descripcion')";

    if (mysqli_query($connection, $query)) {
        // Redirigir al usuario a la página de listas con un mensaje de éxito
        header("Location: ../Contenido/Listas.php?mensaje=Lista creada correctamente");
        exit();
    } else {
        echo "Error al crear la lista: " . mysqli_error($connection);
    }
} else {
    echo "Método no permitido.";
}

// Cerrar conexión
mysqli_close($connection);
?>
