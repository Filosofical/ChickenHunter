<?php

// Conexión a la base de datos
require 'DB_conn.php';
session_start(); // Iniciar la sesión para manejar la autenticación
// Verificamos si se ha enviado el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Obtenemos los datos del formulario de inicio de sesión
    $usuario = mysqli_real_escape_string($conn, $_POST['nomUs']);
    $password = trim($_POST['Psw']);

    // Consulta para buscar el usuario por nombre de usuario o correo electrónico
    $query = "SELECT * FROM usuarios WHERE nombre_usuario = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "s", $usuario);
mysqli_stmt_execute($stmt);

// 2. Obtener resultados
$resultado = mysqli_stmt_get_result($stmt);


    if (mysqli_num_rows($resultado) > 0) {
        $row = mysqli_fetch_assoc($resultado);
    
        if (password_verify($password, $row['contrasena'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['nombre_usuario'];
          
        
                header('Location: ../menu/index.php');
                exit(); 
        }
         else {
            header('Location:../menu/Sesion.php?error=password');
          exit(); // Salimos del script después de redirigir
        }
    } else {
        // Usuario o correo no encontrado
        header('Location: ../menu/Sesion.php?error=user_not_found');
        exit(); // Salimos del script después de redirigir
    }
}

// Cerramos la conexión
mysqli_close($conn);
?>
