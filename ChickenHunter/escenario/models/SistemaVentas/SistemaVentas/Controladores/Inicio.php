<?php
// Iniciamos la sesión
session_start();

// Verificamos si ya tenemos la sesión iniciada o las cookies
if (isset($_SESSION['user_id']) && isset($_SESSION['user_name'])) {
    // El usuario ya está logueado
    echo "Bienvenido, " . $_SESSION['user_name'] . ".";
} elseif (isset($_COOKIE['user_name']) && isset($_COOKIE['user_id'])) {
    // Si las cookies están presentes, restauramos la sesión
    $_SESSION['user_name'] = $_COOKIE['user_name'];
    $_SESSION['user_id'] = $_COOKIE['user_id'];

    echo "Bienvenido de nuevo, " . $_SESSION['user_name'] . ".";
    // Redirigir o hacer alguna otra acción si es necesario
} else {
    // El usuario no está logueado
    echo "Por favor, inicia sesión.";
}
?>
