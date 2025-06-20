
//Mengambil elemen tombol yang digunakan untuk membuka/menutup sidebar
const toggleButton = document.getElementById("toggle-btn");
const sidebar = document.getElementById("sidebar");

//Menambahkan/menghapus kelas close pada sidebar → menyembunyikan atau menampilkan sidebar.
function toggleSidebar() {
  sidebar.classList.toggle("close");
  toggleButton.classList.toggle("rotate");

  closeAllSubMenus(); //Menutup semua submenu yang sedang terbuka

  if(window.barChart) {
    window.barChart.resize();
  }
}

//mengaktifkan tombol toogle
function toggleSubMenu(button) {
  if (!button.nextElementSibling.classList.contains("show")) {
    closeAllSubMenus();
  }

  button.nextElementSibling.classList.toggle("show");
  button.classList.toggle("rotate");

  if (sidebar.classList.contains("close")) {
    sidebar.classList.toggle("close");
    toggleButton.classList.toggle("rotate");
  }
}

function closeAllSubMenus() {
  Array.from(sidebar.getElementsByClassName("show")).forEach((ul) => {
    ul.classList.remove("show");
    ul.previousElementSibling.classList.remove("rotate");
  });
}


