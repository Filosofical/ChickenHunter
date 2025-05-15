<?php
require'../Back/DB_conn.php';
session_start();
if(isset($_SESSION['user_id'])){
 $userId=$_SESSION['user_id'];
$userName=$_SESSION['user_name'];
}
else{
  $userId=null;
  $userName=null;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Chicken Hunter - Menú</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>
  <link href="https://fonts.googleapis.com/css2?family=Sigmar&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>

  <audio id="chickenSound" src="cacareo.mp3"></audio>

  <div class="menu-container">
    <div class="menu_identity">
      <h1>Chicken Hunter</h1>
      <h6> <?php echo (isset($_SESSION['user_id'])) ? htmlspecialchars($_SESSION['user_name'] ?? 'Usuario') : 'Inicia sesión' ?> </h6>
    </div>
    
    <div id="botones">
      <button class="menu-button" onclick="location.href='selectChar.php'">
        <i class="fa fa-play"></i>JUGAR
      </button><br>
      <?php if($userId === null){ ?>
      <button onclick="location.href='Sesion.php'" class="menu-button">INICIAR SESIÓN</button><br>
      <?php }?>
      <button onclick="location.href='Score.php'" class="menu-button">PUNTUACIONES</button><br>
      <button onclick="location.href='Post.html'" class="menu-button">PUBLICAR</button><br>
      <button onclick="window.location.href='../Back/LogOut.php';" class="menu-button">SALIR</button>
    </div>
  </div>
</body>
</html>

