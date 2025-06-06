document.addEventListener("DOMContentLoaded", function () {
  const profileIcon = document.getElementById("profileIcon");
  const dropdownMenu = document.getElementById("dropdownMenu");
  const cartIcon = document.getElementById("cartIcon"); // ID corregido aquí
  const carrito = document.getElementById("carritoPanel"); // <-- este ID debe coincidir con HTML
  const productGrid = document.querySelector(".product-grid");

  const eliminarBtns = document.querySelectorAll(".eliminar");
  const vaciarBtn = document.getElementById("vaciar-btn");

  // Mostrar/Ocultar el carrito y ajustar el grid
  if (cartIcon && carrito && productGrid) {
    cartIcon.addEventListener("click", () => {
      carrito.classList.toggle("show");

      if (carrito.classList.contains("show")) {
        productGrid.classList.add("expand");
      } else {
        productGrid.classList.remove("expand");
      }
    });
  }

  // Dropdown del perfil
  if (profileIcon && dropdownMenu) {
    profileIcon.addEventListener("click", (e) => {
      e.stopPropagation();
      dropdownMenu.classList.toggle("show");
    });

    document.addEventListener("click", function (e) {
      if (!dropdownMenu.contains(e.target) && e.target !== profileIcon) {
        dropdownMenu.classList.remove("show");
      }
    });
  }

 
});