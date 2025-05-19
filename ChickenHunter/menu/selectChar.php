<?php
require'../Back/DB_conn.php';
session_start();



// Verifica si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header('Location: Sesion.php');
    exit();
}else{
    $userId=$_SESSION['user_id'];
$userName=$_SESSION['user_name'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selecciona Un Modo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sigmar&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style2.css">
</head>
<body>
<div class="contenedorPrin">
<header>
    <button onclick="window.location.href='index.php'"><h1>Chicken Hunter</h1></button>
    <h6> <?php echo (isset($_SESSION['user_id'])) ? htmlspecialchars($_SESSION['user_name']) : 'Inicia sesión'; ?> </h6>
</header>
<main>
<div class="SelecChar">
    <h2>SELECCIONA UN PERSONAJE</h2><br>
    <div class="personajes">
<div class="select">
    <img src="granjero.png" alt=""></img>
    <button onclick="selectCharacter('Granjero')" >Granjero</button>
</div>
<div class="select">
    <img src="Perrito.png" alt=""></img>
    <button onclick="selectCharacter('Perro')">Perro</button>
</div>
</div>
</div>
    </main>

    <footer>
        <p>©Chicken Hunter - Todos los Derechos Reservados</p>
    </footer>

</div>

<script>
    function selectCharacter(character) {
        localStorage.setItem('selectedCharacter', character);
        window.location.href = 'Seleccion.php';  // Redirige a la pantalla del juego
    }
    </script>
</body>
</html>
