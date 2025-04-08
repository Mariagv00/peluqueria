document.addEventListener('DOMContentLoaded', function () {
    const profileIcon = document.getElementById('profileIcon');
    const dropdownMenu = document.getElementById('dropdownMenu');
    const focusImage = document.querySelector('.focus-image');
  
    // Toggle profile menu
    if (profileIcon && dropdownMenu) {
      profileIcon.addEventListener('click', function (e) {
        e.stopPropagation();
        dropdownMenu.classList.toggle('show');
      });
  
      window.addEventListener('click', function (e) {
        if (!e.target.closest('.profile-menu')) {
          dropdownMenu.classList.remove('show');
        }
      });
    }
  
    // Focus effect on scroll
    function handleFocusImage() {
      if (!focusImage) return;
  
      const rect = focusImage.getBoundingClientRect();
      const windowHeight = window.innerHeight;
  
      // Check if the image is roughly in the middle of the viewport
      if (rect.top < windowHeight / 2 && rect.bottom > windowHeight / 2) {
        focusImage.classList.add('focused');
      } else {
        focusImage.classList.remove('focused');
      }
    }
  
    window.addEventListener('scroll', handleFocusImage);
    window.addEventListener('resize', handleFocusImage);
    handleFocusImage(); // Trigger once on load
  });
  
  
  