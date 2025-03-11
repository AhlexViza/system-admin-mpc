<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="./img/logo-canchis.png" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>municipalidad provincial canchis</title>
</head>
<body>
    <?php include('header.php'); ?>
        <!-- Contenedor principal -->
        <div class="video-container">
            <video autoplay muted loop id="video-background">
                <source src="./img/fondo-principal.mp4" type="video/mp4">
                <source src="video.webm" type="video/webm">
                <source src="video.ogv" type="video/ogg">
                Tu navegador no soporta el video de fondo.
            </video>
        </div>
    
        <!-- Contenido sobre el video -->
        <div class="content">
        <h1 class="greeting mt-3 text-center text-primary" id="greeting">¡Bienvenido, <?php echo htmlspecialchars($username); ?>!</h1>
            <h1>Reserva de canchas y areas deportivas para divertirte con amigo o la familia</h1>
            <a class="button-action" href="areas_deportivas.php">reserva ahora</a>
        </div>
</body>
</html>
