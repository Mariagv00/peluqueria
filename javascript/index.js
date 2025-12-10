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

// Carrusel circular (solo si existe en la página)
const track = document.querySelector('.carousel-track');
const prev = document.querySelector('.carousel-btn.prev');
const next = document.querySelector('.carousel-btn.next');
const images = document.querySelectorAll('.carousel-track img');

if (track && prev && next && images.length > 0) {
  let index = 0;
  const visibleSlides = 3;

  function updateCarousel() {
    const imageWidth = images[0].clientWidth + 10; // imagen + padding
    const maxIndex = images.length - visibleSlides;
    index = (index + images.length) % images.length; // circular index
    if (index > maxIndex) index = 0; // si supera el máximo, vuelve a 0
    track.style.transform = `translateX(-${index * imageWidth}px)`;
  }

  prev.addEventListener('click', () => {
    index--;
    if (index < 0) index = images.length - visibleSlides;
    updateCarousel();
  });

  next.addEventListener('click', () => {
    index++;
    updateCarousel();
  });

  window.addEventListener('resize', updateCarousel);
  updateCarousel();
}
