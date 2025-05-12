<?php
// Iniciamos la sesión
session_start();

// Eliminar las cookies de sesión
setcookie('user_name', '', time() - 3600, "/"); // Elimina la cookie 'user_name'
setcookie('user_id', '', time() - 3600, "/");   // Elimina la cookie 'user_id'

// Destruir la sesión
session_destroy();

// Redirigir al usuario al login o a la página principal
header('Location: Login.php'); // O la página que consideres
exit();
?>
