<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Peluquería - Inicio</title>
    <link rel="stylesheet" href="../css/index.css">
</head>

<body>

    <header>
        <nav>
            <div class="menu">
                <img src="../img/logo.png" alt="Logo" class="logo">
                <a href="index.php">INICIO</a>
                <a href="agenda.php">AGENDA</a>
                <a href="tienda.php">TIENDA</a>
            </div>
            <div class="profile-container">
                <img src="../img/perfil.png" alt="Perfil" class="profile-icon" id="profileIcon">
                <div class="dropdown" id="dropdownMenu">
                    <?php if (isset($_SESSION['id_usuario'])): ?>
                        <a href="citas.php">Mis citas</a>
                        <a href="logout.php">Cerrar Sesión</a>
                    <?php else: ?>
                        <a href="login.php">Iniciar Sesión</a>
                        <a href="register.php">Registrarse</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <!-- Sección de bienvenida -->
    <section class="description">
        <h1>Bienvenid@ a Beauty</h1>
        <p>Tu estilo, nuestra pasión. Reserva tu cita ahora y deja que nuestros profesionales transformen tu look.</p>
    </section>

    <main>
        <div class="imagenes">
            <div class="caja"><img src="../img/cortes/mechas.jpg" alt="mechas"></div>
            <div class="caja"><img src="../img/cortes/rizos.jpg" alt="corte"></div>
            <div class="caja"><img src="../img/cortes/corte.jpg" alt="corte"></div>
        </div>

        <!-- Quiénes Somos -->
        <section class="about-section">
            <h2>¿Quiénes Somos?</h2>
            <div class="about-container">
                <img src="../img/logo.png" alt="Logo redondo" class="about-logo">
                <p class="about-text">
                    En <strong>Beauty</strong> nos apasiona resaltar tu estilo con los mejores <strong>cortes de
                        pelo</strong>,
                    técnicas de <strong>mechas</strong> modernas, <strong>rizos</strong> definidos y
                    <strong>reflejos</strong> naturales.
                    Nuestro equipo profesional trabaja con dedicación para que cada cliente se sienta auténtico y
                    renovado.
                </p>
            </div>
        </section>

        <!-- Contacto Rápido -->
        <section class="contact-section">
            <div class="contact-box">
                <h2 class="contact-title">Contacto Rápido</h2>
                <div class="contact-left">
                    <p><strong>Información de contacto</strong></p>
                </div>
                <div class="contact-right">
                    <p>(+34) 123 456 789<br>beauty@gmail.com</p>
                </div>
            </div>
        </section>
    </main>

    <footer class="main-footer">
        <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
    </footer>


    <script src="../javascript/index.js" defer></script>


</body>

</html>