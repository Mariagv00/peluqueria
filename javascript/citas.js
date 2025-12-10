document.addEventListener('DOMContentLoaded', function () {
    const contenedor = document.getElementById('lista-citas');

    function mostrarMensaje(texto, tipo = 'exito') {
        const mensaje = document.createElement('div');
        mensaje.textContent = texto;
        mensaje.className = `mensaje ${tipo}`;
        document.querySelector('.citas-container').prepend(mensaje);

        setTimeout(() => mensaje.remove(), 5000);
    }

    function cargarCitas() {
        fetch("../connection/controller.php?api=citas")
            .then(response => response.json())
            .then(citas => {
                contenedor.innerHTML = ""; // Limpia el contenido anterior

                if (!citas.length) {
                    contenedor.innerHTML = "<p>No tienes citas registradas.</p>";
                    return;
                }

                const tabla = document.createElement('table');
                tabla.innerHTML = `
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Servicio</th>
                            <th>Estado</th>
                            <th>Notas</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${citas.map(cita => `
                            <tr>
                                <td>${cita.fecha}</td>
                                <td>${cita.hora}</td>
                                <td>${cita.servicio}</td>
                                <td>${cita.estado}</td>
                                <td>${cita.notas}</td>
                                <td>
                                    <form class="cancel-form">
                                        <input type="hidden" name="accion" value="cancelar_cita">
                                        <input type="hidden" name="fecha" value="${cita.fecha}">
                                        <input type="hidden" name="hora" value="${cita.hora}">
                                        <button type="submit">Cancelar</button>
                                    </form>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                `;
                contenedor.appendChild(tabla);

                // Asignar eventos a cada formulario de cancelación
                document.querySelectorAll('.cancel-form').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        const formData = new FormData(form);

                        fetch('../connection/controller.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => res.text())
                        .then(() => {
                            mostrarMensaje("Cita cancelada correctamente.");
                            cargarCitas(); // Recargar citas
                        });
                    });
                });
            });
    }

    cargarCitas();

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
});
