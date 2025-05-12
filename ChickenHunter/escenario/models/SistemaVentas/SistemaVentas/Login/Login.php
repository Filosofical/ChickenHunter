<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/093074d40c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="Login.css">
    <title>Login</title>
</head>

<body>

    <header>
        <h1 class="logo">FCFM Marketplace</h1>
    </header>

    <div class="fondo">
    
    <div class="contenedor-form login">
    <h2>Iniciar Sesion</h2>

    <!-- Definimos a que ruta y por medio de que método envíaremos la información del login -->
    <form action="../Controladores/login.php" method="post"> 

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-user"></i></span>
            <input type="user" name="user_name" required>
            <label for="user_name">Usuario</label>
        </div>

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="password_user" required>
            <label for="password_user">Contraseña</label>
        </div>

        <div class="recordar">
            <label for="#"><input type="checkbox">Recordar Sesión</label>
        </div>

        <button type="submit" class="btn">Iniciar Sesión</button>

        <div class="registrar">
            <p>¿No tienes cuenta? <a href="Registro.php" class="registrar-link">Registrarse</a></p>
        </div>
    </form>
    </div>
    </div>

    <script src="app.js"></script> 
    <script src="validaciones.js"></script> 

</body>
</html>