<?php include('header.php'); ?>

<?php
// Asegúrate de incluir la conexión a la base de datos

// Obtener el ID de la cancha desde la URL
$id_cancha = $_GET['id'];

// Consultar la base de datos para obtener los detalles de la cancha
$conexion = new mysqli("localhost", "root", "", "sports_reservation");
$con = "SELECT * FROM courts WHERE id = $id_cancha";
$resultado = mysqli_query($conexion, $con);

// Mostrar los detalles de la cancha
if ($resultado && $row = $resultado->fetch_array()) {
    ?>
    <h1><?php echo $row['name']; ?></h1>
    
    <?php if ($row['image']) { ?>
        <img src="frontend/assets/<?php echo htmlspecialchars($row['image']); ?>" alt="Imagen de la cancha" class="area-image">
    <?php } ?>
    
    <p>Descripción: <?php echo $row['description']; ?></p>
    <p>Ubicación: <?php echo $row['location']; ?></p>
    <p>Capacidad: <?php echo $row['capacity']; ?> personas</p>
    <p>Telefono: <?php echo $row['phone']; ?></p>
    <!-- Otros detalles que tengas en tu base de datos -->

    <form action="frontend/"><button class="view-btn2">Reservar</button></form>
    
<?php
} else {
    echo "No se encontraron detalles para esta cancha.";
}
?>
