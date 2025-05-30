// Activar dropdown de perfil
document.addEventListener("DOMContentLoaded", function () {
  const profileIcon = document.getElementById("profileIcon");
  const dropdownMenu = document.getElementById("dropdownMenu");

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

  // Botones de cantidad (+ y -)
  const qtyButtons = document.querySelectorAll(".qty-btn");
  qtyButtons.forEach(btn => {
    btn.addEventListener("click", function () {
      const input = this.parentElement.querySelector(".cantidad");
      let val = parseInt(input.value);
      if (this.textContent === "-" && val > 0) val--;
      else if (this.textContent === "+") val++;
      input.value = val;
    });
  });
});
