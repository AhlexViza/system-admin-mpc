<?php include('header.php'); ?>
<style>
    /* Estilos generales para el cuerpo */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f7fa;
    margin: 0;
    padding: 0;
    color: #333;
}

/* Estilos para el contenedor principal de áreas */
.areas {
    max-width: 1200px;
    margin: 50px auto;
    padding: 20px;
    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

/* Estilos para el título */
.titulo_areas_deportivas {
    text-align: center;
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 30px;
}

/* Estilos para la barra de búsqueda */
.search-bar {
    width: 100%;
    padding: 12px 20px;
    margin-bottom: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
    color: #333;
    outline: none;
}

/* Estilos para las tarjetas de áreas deportivas */
.card {
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
    border-radius: 10px;
    overflow: hidden;
    transition: transform 0.3s ease-in-out;
    border: 1px solid #ecf0f1;
}

.card:hover {
    transform: scale(1.05);
}

.card img {
    width: 100%;
    height: auto;
    display: block;
    border-bottom: 1px solid #ecf0f1;
}

/* Estilos para el nombre de la cancha */
.area-name {
    font-size: 1.8rem;
    font-weight: bold;
    color: #34495e;
    padding: 15px;
    text-align: center;
}

/* Estilos para los botones de acción */
.view-btn1, .view-btn2 {
    background-color: #3498db;
    color: white;
    font-size: 1rem;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin: 10px;
    width: 100%;
}

.view-btn1:hover, .view-btn2:hover {
    background-color: #2980b9;
}

.view-btn2 {
    background-color: #2ecc71;
}

.view-btn2:hover {
    background-color: #27ae60;
}

/* Estilos para los contenedores internos */
.card div {
    padding: 15px;
    text-align: center;
}

/* Respuesta para pantallas pequeñas (dispositivos móviles) */
@media (max-width: 768px) {
    .areas {
        padding: 10px;
    }

    .card {
        margin-bottom: 20px;
    }

    .card img {
        height: 200px;
        object-fit: cover;
    }

    .view-btn1, .view-btn2 {
        font-size: 0.9rem;
        padding: 8px 15px;
    }
}

</style>
<div class="areas">
    <div class="areas_deportivas">
        <h1 class="titulo_areas_deportivas">Áreas Deportivas</h1>
        <div class="card"> 
            <input type="text" id="searchBar" placeholder="Buscar área deportiva..." class="search-bar">
        </div>

        <?php
        $conexion = new mysqli("localhost", "root", "", "sports_reservation");
        $mostrar = "SELECT id, name, location, capacity, phone, image, created_at, price_per_hour FROM courts";
        $resultado = mysqli_query($conexion, $mostrar);
        ?>

        <?php
        if ($resultado){
            while($row = $resultado->fetch_array()){
                ?>
                <div class="card">
                    <span name="areaname" class="area-name"><?php echo $row['name'];?></span>
                    
                    <!-- Mostrar la imagen -->
                    <?php if ($row['image']) { ?>
                        <img src="frontend/assets/<?php echo htmlspecialchars($row['image']); ?>" alt="Imagen de la cancha" class="area-image">
                    <?php } else { ?>
                        <!-- <img src="frontend/assets/images/cancha_interior.jpg" alt="Imagen predeterminada" class="area-image"> -->
                    <?php } ?>
                    
                    <div>
                    <form action="esp_areas_deportivas.php" method="get">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button class="view-btn1" type="submit">Ver</button>
                </form>
                <form action="frontend/" method="post">
                    <input type="hidden" name="courtName" value="<?php echo $row['name']; ?>">
                    <button class="view-btn2" type="submit">Reservar</button>
                </form>
                    </div>
                </div>
            <?php
            }
        }
        ?>
        
    </div>
</div>
</body>
</html>
