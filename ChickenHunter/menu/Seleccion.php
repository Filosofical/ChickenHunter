<?php
require'../Back/DB_conn.php';
session_start();

$userId=$_SESSION['user_id'];
$userName=$_SESSION['user_name'];
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
    <link rel="Icon" href="../media/logo.jpg">
</head>
<body>
<div class="contenedorPrin">
<header>
    <button onclick="window.location.href='index.php'"><h1>Chicken Hunter</h1></button> 
    <h6> <?php echo (isset($_SESSION['user_id'])) ? htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') : 'Inicia sesión' ?> </h6>
</header>
<main>
<div class="MapSelector">
    <div class="mapa">
        <img src="facil.png" alt="">
        <h1>Difficultad facil</h1>
    </div>
    <div class="mapa">
        <img src="MidDiff.png" alt="">
        <h1>Difficultad Media</h1>
    </div>
    <div class="mapa">
        <img src="HardDiff.png" alt="">
        <h1>Difficultad Dificil</h1>
    </div>
</div>

<div class="diffCont">
    <div class="wrapper">
        <div class="option">
          <input checked="" value="1" name="dificultad" type="radio" class="input" />
          <div class="btn">
            <span class="span">Facil</span>
          </div>
        </div>
        <div class="option">
          <input value="2" name="dificultad" type="radio" class="input" />
          <div class="btn">
            <span class="span">Medio</span>
          </div>
        </div>
        <div class="option">
          <input value="3" name="dificultad" type="radio" class="input" />
          <div class="btn">
            <span class="span">Dificil</span>
          </div>
        </div>
      </div>
    </div>
    <br>
    <div class="botones">
<button id="jugarBtn">Jugar</button>
    <button id="jugarBtnMult">Multi-Jugador</button>
    <button onclick="location.href='index.php'">Volver</button>
</div>
    </main>

    <footer>
        <p>©Chicken Hunter - Todos los Derechos Reservados</p>
    </footer>

</div>

<script src="dificultad.js"></script>
</body>
</html>