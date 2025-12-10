document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("registerForm");
  const passwordInput = document.getElementById("password");
  const passwordHelp = document.getElementById("passwordHelp");

  // Mostrar mensaje del sessionStorage si existe
  const mensaje = sessionStorage.getItem("mensajeRegistro");
  const tipo = sessionStorage.getItem("tipoMensaje");

  if (mensaje) {
    const banner = document.createElement("div");
    banner.textContent = mensaje;
    banner.style.position = "fixed";
    banner.style.top = "20px";
    banner.style.left = "50%";
    banner.style.transform = "translateX(-50%)";
    banner.style.padding = "12px 24px";
    banner.style.borderRadius = "8px";
    banner.style.fontWeight = "bold";
    banner.style.zIndex = "9999";
    banner.style.boxShadow = "0 0 10px rgba(0,0,0,0.2)";
    banner.style.transition = "opacity 0.5s ease";

    if (tipo === "exito") {
      banner.style.backgroundColor = "#d4edda";
      banner.style.color = "#155724";
      banner.style.border = "1px solid #c3e6cb";
    } else {
      banner.style.backgroundColor = "#f8d7da";
      banner.style.color = "#721c24";
      banner.style.border = "1px solid #f5c6cb";
    }

    document.body.appendChild(banner);

    setTimeout(() => {
      banner.style.opacity = "0";
      setTimeout(() => banner.remove(), 500);
      sessionStorage.removeItem("mensajeRegistro");
      sessionStorage.removeItem("tipoMensaje");
    }, 5000);
  }

  // Validar contraseña
  form.addEventListener("submit", function (e) {
    const password = passwordInput.value;
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

    if (!regex.test(password)) {
      e.preventDefault();
      passwordHelp.style.display = "block";

      sessionStorage.setItem(
        "mensajeRegistro",
        "❌ La contraseña debe tener mínimo 8 caracteres, 1 mayúscula, 1 minúscula y 1 número."
      );
      sessionStorage.setItem("tipoMensaje", "error");

      setTimeout(() => {
        passwordHelp.style.display = "none";
      }, 5000);
    }
  });
});
