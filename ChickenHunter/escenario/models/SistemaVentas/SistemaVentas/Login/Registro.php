<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/093074d40c.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="registro.css">
    <title>Registro</title>
</head>

<body>

    <header>
        <h1 class="logo">FCFM Marketplace</h1>
    </header>

    <div class="fondo">

         <!-- Registro -->
    <div class="contenedor-form registrar">
        <h2>Registrarse</h2>

        <!-- Definimos a que ruta y por medio de que método envíaremos la información del registro -->
         <form action="../Controladores/ingreso.php" method="post">

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-signature"></i></span>
            <input type="text" name="nombre_completo" required>
            <label for="nombre_completo">Nombre completo</label>
        </div>

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-user"></i></span>
            <input type="text" name="nombre_usuario" required>
            <label for="nombre_usuario">Nombre de Usuario</label>
        </div>

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-envelope"></i></span>
            <input type="email" name="email_usuario" required>
            <label for="email_usuario">Email</label>
        </div>

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-lock"></i></span>
            <input type="password" name="contraseña_usuario" required>
            <label for="contraseña_usuario">Contraseña</label>
        </div>

        <div id="error-contraseña" style="color: red; display: none;">
             La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.
        </div>

        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-cake-candles"></i></span>
            <input type="date" name="fecha_usuario" required>
            <label for="fecha_usuario">Fecha de nacimiento</label>
        </div>
        
        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-genderless"></i></span>
            <select name="sexo_usuario" id="sexo_usuario" required>
            <option value="Masculino">Masculino</option>
            <option value="Femenino">Femenino</option>
            </select>
            <label for="sexo_usuario">Sexo</label>
        </div>


        <div class="contenedor-input">
            <span class="icono"><i class="fa-solid fa-user-group"></i></span>
            <select name="rol_usuario" id="rol_usuario" required>
            <option value="Administrador">Administrador</option>
            <option value="Vendedor">Vendedor</option>
            <option value="Comprador">Comprador</option>
            </select>
            <label for="rol_usuario">Rol de usuario</label>
        </div>

        <div class="contenedor-input">
             <span class="icono"><i class="fa-solid fa-image"></i></span>
             <label for="imgRuta">Foto de Perfil:</label>  
             <br>
             <img id="imgPerfil" src="#" alt="Vista previa de la imagen" style="display: none; width: 100px;">
             <br>
             <input class="inputImgPerfil" type="file" id="imgRuta" name="imgRuta" accept="image/*" onchange="previewImage()">
        </div>
        <br>
        <br>
        <div class="recordar">
            <label for="#"><input type="checkbox">Acepto los términos y condiciones</label>
        </div>

        <button type="submit" class="btn">Registrarme</button>
        
        <div class="registrar">
            <p>¿Ya tienes una cuenta? <a href="Login.php" class="login-link">Iniciar Sesión</a></p>
        </div>
        </form>
    </div>
    </div>

    <script src="app.js"></script> 
    <script src="validaciones.js"></script> 

</body>
</html>