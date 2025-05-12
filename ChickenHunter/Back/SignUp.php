<?php
require 'DB_conn.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombreUsuario = mysqli_real_escape_string($conn, $_POST['nombre_usuario']);
    $contraseña = password_hash($_POST['contraseña_usuario'], PASSWORD_DEFAULT);


    $check_query = "SELECT id FROM Usuarios WHERE nombre_usuario = ? ";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "s", $nombreUsuario);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_store_result($check_stmt);
   

    if (mysqli_stmt_num_rows($check_stmt) > 0) {
         $error = "username_exists";
        header("Location: Registro.php?error=$error");
        exit();
    }
    


    // Insertar el nuevo usuario
     $query = "INSERT INTO usuarios 
          (nombre_usuario,contrasena) VALUES (?, ?)"; 
   

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param(
    $stmt, 
    "ss", 
    $nombreUsuario, 
    $contraseña
);


    if (mysqli_stmt_execute($stmt)) {
        header('Location: ../menu/Sesion.php?success=user_created');
        exit();
    } else {
        header('Location:../menu/Registro.php?error=user_not_created');
    }
}
// Cerramos la conexión
mysqli_close($conn);
?>