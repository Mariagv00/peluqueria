<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agenda</title>
  <link rel="stylesheet" href="../css/agenda.css">
  <link href="https://unpkg.com/fullcalendar@6.1.9/main.min.css" rel="stylesheet" type="text/css">
</head>
<body>

  <!-- Header -->
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

  <!-- Contenido -->
  <main class="main-content">
    <div class="agenda-container">

      <!-- Calendario -->
      <div class="calendar-box">
        <h2>Selecciona un día</h2>
        <div id="calendar"></div>
        <input type="hidden" id="fechaSeleccionadaInput" name="fecha">
      </div>

      <!-- Formulario -->
      <div class="form-box">
        <h3>CITAS:</h3>
        <form method="POST" action="../connection/controller.php" id="citaForm">
          <input type="hidden" name="accion" value="cita">

          <label><strong>Horas disponibles</strong></label>
          <select name="hora" id="hora" required>
            <option>9:00-10:00</option>
            <option>10:00-11:00</option>
            <option>11:00-12:00</option>
            <option>12:00-13:00</option>
            <option>13:00-14:00</option>
            <option>16:00-17:00</option>
            <option>17:00-18:00</option>
            <option>18:00-19:00</option>
          </select>

          <label><strong>Servicio</strong></label>
          <select name="servicio" id="servicio" required>
            <option value="">-- Selecciona un servicio --</option>
          </select>

          <label><strong>Descripción del servicio</strong></label>
          <textarea name="descripcion" id="descripcion" readonly placeholder="Descripción..." rows="3"></textarea>

          <label><strong>Notas</strong></label>
          <textarea name="notas" rows="4" placeholder="Escribe aquí..."></textarea>

          <input type="hidden" id="fechaSeleccionada" name="fecha">

          <div class="button-box">
            <button type="submit">Confirmar cita</button>
          </div>
        </form>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="main-footer">
    <p>&copy; 2025 Beauty. Todos los derechos reservados.</p>
  </footer>

  <!-- Scripts -->
  <script src="https://unpkg.com/fullcalendar@6.1.9/index.global.min.js"></script>
  <script src="../javascript/agenda.js"></script>
</body>
</html>
