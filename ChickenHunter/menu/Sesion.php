<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INICIA SESION</title>
    <link rel="stylesheet" href="style1.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sigmar&display=swap" rel="stylesheet">
<link rel="Icon" href="../media/logo.jpg">
</head>
<body>
    <div class="contenedorPrin">
        <header> <h1>Chicken Hunter</h1>
        </header>

        <main>

<div class="IS">
  
    <form action="../Back/LogIn.php" id="inSes" method="post">  
        <h2>INICIO DE SESION</h2>
        <div class="input-container">
            <label for="nomUs">Player User</label>
<input class="input" type="text" name="nomUs" id="nomUs" placeholder="Nombre de Usuario"><br>
        </div>
        <div class="input-container">
            <label for="Psw">Contraseña</label>
<input class="input" type="password" name="Psw" id="Psw" placeholder="Contraseña"><br>
        </div>
<button type="submit">Ingresar</button>
<br>
<a href="Registro.php">Registrate ahora!</a>
    </form>
</div>

        </main>

        <footer>
            <P>Pablo Garcia Garza</P>
            <p>Daniel Flores Santacruz</p>
        </footer>
    </div>
</body>
</html>