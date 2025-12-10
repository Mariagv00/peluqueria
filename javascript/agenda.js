document.addEventListener('DOMContentLoaded', function () {
  const fechaInput = document.getElementById('fechaSeleccionada');
  const servicioSelect = document.getElementById('servicio');
  const descripcionTextarea = document.getElementById('descripcion');
  const horaSelect = document.getElementById('hora');
  const form = document.getElementById('citaForm');

  // Crear contenedor de mensajes
  const mensajeDiv = document.createElement("div");
  mensajeDiv.classList.add("mensaje");
  form.parentNode.insertBefore(mensajeDiv, form);

  function mostrarMensaje(texto, tipo = "error") {
    mensajeDiv.textContent = texto;
    mensajeDiv.className = "mensaje " + tipo;
    mensajeDiv.style.display = "block";
    setTimeout(() => {
      mensajeDiv.style.display = "none";
    }, 5000);
  }

  // Mostrar mensaje guardado en sessionStorage (tras redirección)
  const mensajeGuardado = sessionStorage.getItem("mensajeCita");
  const tipoMensaje = sessionStorage.getItem("tipoMensaje");
  if (mensajeGuardado) {
    mostrarMensaje(mensajeGuardado, tipoMensaje || "exito");
    sessionStorage.removeItem("mensajeCita");
    sessionStorage.removeItem("tipoMensaje");

    if (mensajeGuardado.includes("Serás redirigido")) {
      setTimeout(() => {
        window.location.href = "agenda.php";
      }, 5000);
    }
  }

  // Restringir fechas pasadas y fines de semana
  const today = new Date().toISOString().split('T')[0];
  fechaInput.setAttribute("min", today);

  // Dropdown del perfil
  const profileIcon = document.getElementById("profileIcon");
  const dropdownMenu = document.getElementById("dropdownMenu");

  if (profileIcon && dropdownMenu) {
    profileIcon.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownMenu.classList.toggle("show");
    });

    document.addEventListener("click", function (e) {
      if (!dropdownMenu.contains(e.target)) {
        dropdownMenu.classList.remove("show");
      }
    });
  }

  // Cargar servicios
  fetch("../connection/controller.php?api=servicios")
    .then(res => res.json())
    .then(data => {
      servicioSelect.innerHTML = '<option value="">-- Selecciona un servicio --</option>';
      data.forEach(serv => {
        const option = document.createElement("option");
        option.value = serv.id_servicio;
        option.textContent = serv.nombre;
        servicioSelect.appendChild(option);
      });
    })
    .catch(err => {
      mostrarMensaje("Error al cargar servicios.", "error");
      console.error(err);
    });

  // Cargar descripción automáticamente
  servicioSelect.addEventListener("change", function () {
    const id = this.value;
    if (!id) {
      descripcionTextarea.value = "";
      return;
    }

    fetch(`../connection/controller.php?api=descripcion&id=${id}`)
      .then(res => res.text())
      .then(desc => {
        descripcionTextarea.value = desc;
      });
  });

  // Validación de día y disponibilidad
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const fecha = fechaInput.value;
    const hora = horaSelect.value;
    const servicio = servicioSelect.value;

    const selectedDate = new Date(fecha);
    const day = selectedDate.getDay(); // 0 domingo, 6 sábado

    if (day === 0 || day === 6) {
      mostrarMensaje("No se permiten citas los sábados ni domingos.", "error");
      return;
    }

    fetch(`../connection/controller.php?api=comprobar_cita&fecha=${fecha}&hora=${hora}&servicio=${servicio}`)
      .then(res => res.text())
      .then(resp => {
        if (resp === "ocupado") {
          mostrarMensaje("La hora ya está ocupada para ese servicio. Elige otra.", "error");
        } else {
          form.submit(); // Si está libre, enviar
        }
      })
      .catch(() => {
        mostrarMensaje("Error al comprobar disponibilidad.", "error");
      });
  });
});
