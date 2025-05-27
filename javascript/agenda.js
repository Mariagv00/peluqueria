document.addEventListener('DOMContentLoaded', function () {
  const calendarEl = document.getElementById('calendar');
  const today = new Date();
  today.setHours(0, 0, 0, 0);

  const fechaInput = document.getElementById('fechaSeleccionada');
  const servicioSelect = document.getElementById('servicio');
  const descripcionTextarea = document.getElementById('descripcion');
  const horaSelect = document.getElementById('hora');
  const form = document.getElementById('citaForm');

  if (!calendarEl || typeof FullCalendar === 'undefined') return;

  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    locale: 'es',
    firstDay: 1,
    selectable: true,
    fixedWeekCount: false,
    showNonCurrentDates: false,
    validRange: {
      start: today.toISOString().split('T')[0]
    },
    dateClick: function (info) {
      const clickedDate = new Date(info.dateStr);
      const dayOfWeek = clickedDate.getDay();
      clickedDate.setHours(0, 0, 0, 0);

      if (clickedDate < today || dayOfWeek === 0 || dayOfWeek === 6) {
        alert("No puedes seleccionar sábados, domingos ni días anteriores a hoy.");
        return;
      }

      document.querySelectorAll('.fc-daygrid-day').forEach(d => d.classList.remove('fc-day-selected'));
      info.dayEl.classList.add('fc-day-selected');
      fechaInput.value = info.dateStr;
    }
  });

  calendar.render();

  // Dropdown perfil
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

  // Cargar servicios dinámicamente
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
    });

  // Mostrar descripción al seleccionar servicio
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

  // Validación previa al envío (evitar duplicados)
  form.addEventListener("submit", function (e) {
    e.preventDefault();

    const fecha = fechaInput.value;
    const hora = horaSelect.value;
    const servicio = servicioSelect.value;

    if (!fecha || !hora || !servicio) {
      alert("Todos los campos son obligatorios y debe seleccionarse una fecha válida.");
      return;
    }

    // Verificar disponibilidad antes de enviar
    fetch(`../connection/controller.php?api=comprobar_cita&fecha=${fecha}&hora=${hora}&servicio=${servicio}`)
      .then(res => res.text())
      .then(resp => {
        if (resp === "ocupado") {
          alert("La hora ya está ocupada para ese servicio. Elige otra.");
        } else {
          form.submit(); // todo correcto, ahora sí envía
        }
      })
      .catch(err => {
        alert("Error al comprobar disponibilidad.");
      });
  });
});
