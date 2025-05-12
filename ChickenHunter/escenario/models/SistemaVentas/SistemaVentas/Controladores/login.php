<?php
// Iniciamos la sesión
session_start();

// Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "sistemadeventas";

$connection = mysqli_connect($host, $user, $pass, $dbname);

if (!$connection) {
    die("Error de conexión: " . mysqli_connect_error());
}

// Verificamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtenemos los datos del formulario de inicio de sesión
    $usuarioOEmail = mysqli_real_escape_string($connection, $_POST['user_name']);
    $password = $_POST['password_user'];

    // Consulta para buscar el usuario por nombre de usuario o correo electrónico
    $query = "SELECT * FROM usuario WHERE Nombre_Usuario = '$usuarioOEmail' OR Email = '$usuarioOEmail'";
    $resultado = mysqli_query($connection, $query);

    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
        // Verificamos la contraseña usando password_verify
        if (password_verify($password, $row['Contraseña'])) {
            $_SESSION['user_id'] = $row['ID_Usuario'];
            $_SESSION['user_name'] = $row['Nombre_Usuario'];
            $_SESSION['user_role'] = $row['Rol_Usuario']; // Guardamos el rol
        
            if ($row['Rol_Usuario'] === 'Administrador') {
                header('Location: ../Contenido/Solicitudes.php');
            } elseif ($row['Rol_Usuario'] === 'Vendedor') {
                header('Location: ../Contenido/Categorias.php');
            } else {
                header('Location: ../Contenido/Inicio.php');
            }
            exit();
        }
         else {
            // Contraseña incorrecta
            echo "La contraseña es incorrecta.";
        }
    } else {
        // Usuario o correo no encontrado
        echo "El nombre de usuario o correo electrónico no existe.";
    }
}

// Cerramos la conexión
mysqli_close($connection);
?>
