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
});

