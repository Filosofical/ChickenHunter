<?php
//Conexión a la base de datos
$host = "localhost";
$user = "root";
$pass = "";

$connection = mysqli_connect($host, $user, $pass);

// Verificamos la conexión a la base de datos
if(!$connection) {
    die("No se ha podido conectar con el servidor: " . mysqli_error($connection));
} else {
    echo "Conectado al servidor exitosamente.<br>";
}

// Selección de la base de datos
$datab = "sistemadeventas";
$db = mysqli_select_db($connection, $datab);

if(!$db) {
    die("No se ha podido seleccionar la base de datos: " . mysqli_error($connection));
} else {
    echo "Base de datos seleccionada exitosamente.<br>";
}

//Obtenemos los datos del formulario y aplicamos seguridad
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreCompleto = mysqli_real_escape_string($connection, $_POST['nombre_completo']);
    $nombreUsuario = mysqli_real_escape_string($connection, $_POST['nombre_usuario']);
    $email = mysqli_real_escape_string($connection, $_POST['email_usuario']);
    $contraseña = password_hash($_POST['contraseña_usuario'], PASSWORD_DEFAULT);
    $fechaNacimiento = $_POST['fecha_usuario'];
    $sexo = $_POST['sexo_usuario'];
    $imagen = mysqli_real_escape_string($connection, $_POST['imgRuta']);
    $rol = $_POST['rol_usuario'];

    // Insertar el nuevo usuario
    $query = "INSERT INTO usuario (Nombre_Completo, Nombre_Usuario, Email, Contraseña, Fecha_Nacimiento, Sexo, Foto_Perfil, Rol_Usuario)
              VALUES ('$nombreCompleto', '$nombreUsuario', '$email', '$contraseña', '$fechaNacimiento', '$sexo', '$imagen', '$rol')";

    if (mysqli_query($connection, $query)) {
        echo "Usuario registrado exitosamente.";
        header('Location: ../Login/Login.php');
        exit();
    } else {
        echo "Error al registrar: " . mysqli_error($connection);
    }
}

// Cerramos la conexión
mysqli_close($connection);
?>


