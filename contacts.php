<?php
// Definir el destino del correo
$to = "contacto@tusitio.com"; // Cambia esto por tu correo

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $message = htmlspecialchars($_POST['message']);
    
    // Validar si los campos obligatorios están completos
    if (!empty($name) && !empty($email) && !empty($message)) {
        // Asunto del correo
        $subject = "Consulta desde la página de contacto";

        // Cuerpo del mensaje
        $body = "Nombre: $name\nCorreo: $email\nTeléfono: $phone\n\nMensaje:\n$message";

        // Enviar correo
        if (mail($to, $subject, $body)) {
            $feedback = "Gracias por contactarnos. Nos pondremos en contacto contigo pronto.";
        } else {
            $feedback = "Hubo un error al enviar tu mensaje. Por favor, intenta nuevamente.";
        }
    } else {
        $feedback = "Por favor, completa todos los campos obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Sistema de Reserva Deportiva</title>
    <!-- Incluir el CSS de Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="container my-5">
        <header class="text-center mb-4">
            <h1>Sistema de Reservas Deportivas</h1>
        </header>

        <h2 class="text-center mb-4">Contacto</h2>
        
        <?php
        // Mostrar el mensaje de feedback (si hay)
        if (isset($feedback)) {
            echo "<div class='alert alert-info' role='alert'>$feedback</div>";
        }
        ?>

        <!-- Formulario de contacto -->
        <form action="contacto.php" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Nombre Completo</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            
            <div class="mb-3">
                <label for="phone" class="form-label">Teléfono (opcional)</label>
                <input type="tel" class="form-control" id="phone" name="phone">
            </div>
            
            <div class="mb-3">
                <label for="message" class="form-label">Mensaje</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
        </form>

        <section class="mt-5">
            <h3>Información de Contacto</h3>
            <ul class="list-unstyled">
                <li><strong>Dirección:</strong> Calle Ejemplo, 123, Ciudad, País</li>
                <li><strong>Teléfono:</strong> +123 456 7890</li>
                <li><strong>Correo Electrónico:</strong> <a href="mailto:contacto@tusitio.com">contacto@tusitio.com</a></li>
                <li><strong>Horario de atención:</strong> Lunes a Viernes, de 9:00 AM a 6:00 PM</li>
            </ul>
        </section>

        <section class="mt-5">
            <h3>Síguenos en Redes Sociales</h3>
            <ul class="list-unstyled">
                <li><a href="https://facebook.com/tusitio" target="_blank">Facebook</a></li>
                <li><a href="https://twitter.com/tusitio" target="_blank">Twitter</a></li>
                <li><a href="https://instagram.com/tusitio" target="_blank">Instagram</a></li>
            </ul>
        </section>

        <section class="mt-5">
            <h3>Ubicación</h3>
            <!-- Incluir el mapa de Google (ejemplo) -->
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d12434567.89258103!2d-58.4436353!3d-34.5997101!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x95bcb1ed3a1b0a5d%3A0x658d7a20ba0f7e23!2sCalle+Ejemplo+123%2C+Ciudad%2C+Pa%C3%ADs!5e0!3m2!1ses!2sus!4v1603695244577!5m2!1ses!2sus" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
        </section>

        <section class="mt-5">
            <h3>Preguntas Frecuentes</h3>
            <ul>
                <li><a href="#faq1">¿Cómo puedo hacer una reserva?</a></li>
                <li><a href="#faq2">¿Puedo cancelar una reserva?</a></li>
                <li><a href="#faq3">¿Cuáles son los métodos de pago?</a></li>
            </ul>
        </section>
    </div>

    <footer class="text-center py-4 mt-5 bg-light">
        <p>&copy; 2025 Sistema de Reservas Deportivas</p>
    </footer>

    <!-- Incluir los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
