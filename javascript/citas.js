document.addEventListener('DOMContentLoaded', function () {
    const contenedor = document.getElementById('lista-citas');

    fetch("../connection/controller.php?api=citas")
        .then(response => response.json())
        .then(citas => {
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
                                <form method="POST" action="../connection/controller.php" onsubmit="return confirm('¿Cancelar esta cita?');">
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
        });

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
