// Dropdown del perfil
const profileIcon = document.getElementById('profileIcon');
const dropdownMenu = document.getElementById('dropdownMenu');

profileIcon.addEventListener('click', () => {
    dropdownMenu.classList.toggle('show');
});

window.addEventListener('click', (e) => {
    if (!profileIcon.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.classList.remove('show');
    }
});
