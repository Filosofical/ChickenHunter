<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda</title>
</head>
<body>
    <h1>Resultados de Búsqueda</h1>
    <div class="resultados">
    <?php
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='resultado'>";
            if ($row['Tipo'] === 'producto') {
                echo "<h2>Producto: " . $row['Nombre'] . "</h2>";
                echo "<p>Precio: $" . $row['Precio'] . "</p>";
            } elseif ($row['Tipo'] === 'usuario') {
                echo "<h2>Usuario: " . $row['Nombre'] . "</h2>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No se encontraron resultados.</p>";
    }
    ?>
</div>

</body>
</html>
