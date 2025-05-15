<?php
require '../Back/DB_conn.php';
session_start();
$user_id= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_name= isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chicken Hunter - Game</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/loaders/GLTFLoader.js"></script>
    <link rel="stylesheet" href="escenario.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sigmar&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  
</head>
<body>
    <div id="counter">Gallinas atrapadas: 0</div>
 
    <div id="timer">Tiempo: 0</div> 
    
    <div id="notificacion"></div>
    <div id="pauseMenu">
        <h1>Chicken Hunter</h1>
        <input type="text" id="userid" name="userid" value="<?php echo $user_id; ?>" hidden>
        <p>Pause - <?php echo htmlspecialchars($user_name);?></p>
        <button onclick="resumeGame()">Reanudar</button><br>
        <button onclick="quitGame()">Salir</button>
        <button onclick="location.href='/ChickenHunter/menu/Post.html'">
            <i class="fa fa-share-alt"></i>
        </button>
    </div>
   <script src="gameMid.js"></script>
</body>
</html>