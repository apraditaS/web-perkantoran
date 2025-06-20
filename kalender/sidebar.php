<style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap");

    :root {
        --base-clr: #6e1414;
        /* Maroon */
        --line-clr: #bfa181;
        /* Beige Medium */
        --hover-clr: #8b1a1a;
        /* Darker Maroon */
        --text-clr: #fff7ed;
        /* Light Beige for text */
        --accent-clr: #d7bea8;
        /* Soft Beige */
        --secondary-text-clr: #eadbc8;
    }

    * {
        margin: 0;
        padding: 0;
    }

    html {
        font-family: Poppins, "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        line-height: 1.5rem;
    }

    body {
        min-height: 100vh;
        min-height: 100dvh;
        display: grid;
        grid-template-columns: auto 1fr;
    }

    #sidebar {
        box-sizing: border-box;
        height: 100vh;
        width: 250px;
        padding: 5px 1em;
        background-color: var(--base-clr);
        border-right: 1px solid var(--line-clr);

        position: sticky;
        top: 0;
        align-self: start;
        transition: 300ms ease-in-out;
        overflow: hidden;
        text-wrap: nowrap;
    }

    /* --- gaya untuk semua item sidebar (link & tombol) --- */
    .sidebar-link {
        display: flex;
        align-items: center;
        gap: 0.75em;
        padding: 0.75em 1em;
        color: #e8eaed;
        text-decoration: none;
        cursor: pointer;
        /* agar tombol juga kursor pointer */
        background: none;
        /* hilangkan background default button */
        border: none;
        /* hilangkan border default button */
        width: 100%;
        /* supaya menutupi full width li */
        font: inherit;
        /* warisi font dari sidebar */
    }

    /* hover / active state */
    .sidebar-link:hover,
    .sidebar-link.active {
        background-color: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
    }

    /* khusus ikon di logout (bisa dihapus jika sudah sesuai) */
    .back-link svg {
        flex-shrink: 0;
        width: 24px;
        height: 24px;
    }

    #sidebar.close {
        padding: 5px;
        width: 60px;
    }

    #sidebar ul {
        list-style: none;
    }

    #sidebar>ul>li:first-child {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 16px;

        .logo {
            font-weight: 600;
        }
    }

    #sidebar ul li.active a {
        color: var(--accent-clr);

        svg {
            fill: var(--accent-clr);
        }
    }

    .logo-img {
        height: 40px;
        /* sesuaikan tinggi logo */
        width: auto;
        margin-top: 9px;
        margin-right: 3px;
        user-select: none;
    }

    /* Sidebar Profile section */
    .sidebar-profile {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 10px 30px;
        gap: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        margin-top: auto;
    }

    .admin-text {
        color: #e8eaed;
        font-weight: 600;
        font-size: 14px;
        user-select: none;
        margin-right: 100px;
    }

    .profile-pic {
        width: 30px;
        height: 30px;
        margin-right: -15px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e8eaed;
        user-select: none;
    }

    #sidebar a,
    #sidebar .dropdown-btn,
    #sidebar .logo {
        border-radius: 0.5em;
        padding: 0.85em;
        text-decoration: none;
        color: var(--text-clr);
        display: flex;
        align-items: center;
        gap: 1em;
    }

    .dropdown-btn {
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        font: inherit;
        cursor: pointer;
    }

    #sidebar svg {
        flex-shrink: 0;
        fill: var(--text-clr);
    }

    #sidebar a span,
    #sidebar .dropdown-btn span {
        flex-grow: 1;
    }

    #sidebar a:hover,
    #sidebar .dropdown-btn:hover {
        background-color: var(--hover-clr);
    }

    #sidebar .sub-menu {
        display: grid;
        grid-template-rows: 0fr;
        transition: 300ms ease-in-out;

        >div {
            overflow: hidden;
        }
    }

    #sidebar .sub-menu.show {
        grid-template-rows: 1fr;
    }

    .dropdown-btn svg {
        transition: 200ms ease;
    }

    .rotate svg:last-child {
        rotate: 180deg;
    }

    #sidebar .sub-menu a {
        padding-left: 2em;
    }

    #toggle-btn {
        margin-left: auto;
        padding: 1em;
        border: none;
        border-radius: 0.5em;
        background: none;
        cursor: pointer;

        svg {
            transition: rotate 150ms ease;
        }
    }

    #toggle-btn:hover {
        background-color: var(--hover-clr);
    }
</style>
<nav id="sidebar">
    <ul>
        <li>
            <img src="../images/logo-smk.png" class="logo-img" />

            <button onclick="toggleSidebar()" id="toggle-btn">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e8eaed">
                    <path
                        d="m313-480 155 156q11 11 11.5 27.5T468-268q-11 11-28 11t-28-11L228-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T468-692q11 11 11 28t-11 28L313-480Zm264 0 155 156q11 11 11.5 27.5T732-268q-11 11-28 11t-28-11L492-452q-6-6-8.5-13t-2.5-15q0-8 2.5-15t8.5-13l184-184q11-11 27.5-11.5T732-692q11 11 11 28t-11 28L577-480Z" />
                </svg>
            </button>
        </li>

        <li>
            <div class="sidebar-profile">
                <span class="admin-text">admin</span>
                <img src="../images/admin-pict.png" alt="Admin Profile" class="profile-pic" />
            </div>
        </li>

        <li class="active">
            <a href="../admin/admin_page.php">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e8eaed">
                    <path
                        d="M520-640v-160q0-17 11.5-28.5T560-840h240q17 0 28.5 11.5T840-800v160q0 17-11.5 28.5T800-600H560q-17 0-28.5-11.5T520-640ZM120-480v-320q0-17 11.5-28.5T160-840h240q17 0 28.5 11.5T440-800v320q0 17-11.5 28.5T400-440H160q-17 0-28.5-11.5T120-480Zm400 320v-320q0-17 11.5-28.5T560-520h240q17 0 28.5 11.5T840-480v320q0 17-11.5 28.5T800-120H560q-17 0-28.5-11.5T520-160Zm-400 0v-160q0-17 11.5-28.5T160-360h240q17 0 28.5 11.5T440-320v160q0 17-11.5 28.5T400-120H160q-17 0-28.5-11.5T120-160Zm80-360h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z" />
                </svg>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <button onclick="toggleSubMenu(this)" class="dropdown-btn">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e8eaed">
                    <path
                        d="M160-160q-33 0-56.5-23.5T80-240v-480q0-33 23.5-56.5T160-800h207q16 0 30.5 6t25.5 17l57 57h320q33 0 56.5 23.5T880-640v400q0 33-23.5 56.5T800-160H160Zm0-80h640v-400H447l-80-80H160v480Zm0 0v-480 480Zm400-160v40q0 17 11.5 28.5T600-320q17 0 28.5-11.5T640-360v-40h40q17 0 28.5-11.5T720-440q0-17-11.5-28.5T680-480h-40v-40q0-17-11.5-28.5T600-560q-17 0-28.5 11.5T560-520v40h-40q-17 0-28.5 11.5T480-440q0 17 11.5 28.5T520-400h40Z" />
                </svg>
                <span>Input</span>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e8eaed">
                    <path
                        d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z" />
                </svg>
            </button>
            <ul class="sub-menu">
                <div>
                    <li><a href="../surat_masuk/input_surat_masuk.php">Surat Masuk</a></li>
                    <li><a href="../surat_keluar/input_surat_keluar.php">Surat Keluar</a></li>
                    <li><a href="../buku_tamu/input_buku_tamu.php">Buku Tamu</a></li>
                </div>
            </ul>
        </li>

        <li>
            <button onclick="toggleSubMenu(this)" class="dropdown-btn">
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e8eaed">
                    <path
                        d="m221-313 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-228q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm0-320 142-142q12-12 28-11.5t28 12.5q11 12 11 28t-11 28L250-548q-12 12-28 12t-28-12l-86-86q-11-11-11-28t11-28q11-11 28-11t28 11l57 57Zm339 353q-17 0-28.5-11.5T520-320q0-17 11.5-28.5T560-360h280q17 0 28.5 11.5T880-320q0 17-11.5 28.5T840-280H560Zm0-320q-17 0-28.5-11.5T520-640q0-17 11.5-28.5T560-680h280q17 0 28.5 11.5T880-640q0 17-11.5 28.5T840-600H560Z" />
                </svg>
                <span>Data</span>
                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"
                    fill="#e8eaed">
                    <path
                        d="M480-361q-8 0-15-2.5t-13-8.5L268-556q-11-11-11-28t11-28q11-11 28-11t28 11l156 156 156-156q11-11 28-11t28 11q11 11 11 28t-11 28L508-372q-6 6-13 8.5t-15 2.5Z" />
                </svg>
            </button>
            <ul class="sub-menu">
                <div>
                    <li><a href="../surat_masuk/data_surat_masuk.php">Data Surat Masuk</a></li>
                    <li><a href="../surat_keluar/data_surat_keluar.php">Data Surat Keluar</a></li>
                    <li><a href="../buku_tamu/data_buku_tamu.php">Data Buku Tamu</a></li>
                </div>
            </ul>
        </li>

        <li>
            <a href="../admin/galeri_surat.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#e8eaed"
                    aria-hidden="true">
                    <path d="M10 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2h-8l-2-2z" />
                </svg>
                <span>Galeri File</span>
            </a>
        </li>

        <li>
            <a href="../kalender/kalender.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#e8eaed" viewBox="0 0 24 24">
                    <path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.1 0-2 .9-2 
        2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 
        2-2V6c0-1.1-.9-2-2-2zm0 16H5V9h14v11zm0-13H5V6h14v1z" />
                </svg>
                <span>Kalender</span>
            </a>
        </li>
        <li>
            <a href="../admin/admin_page.php" class="sidebar-link back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="#e8eaed"
                    aria-hidden="true">
                    <path d="M16 13v-2H7V8l-5 4 5 4v-3h9zm3-10H5c-1.1 0-2 .9-2 2v4h2V5h14v14H5v-4H3v4c0 1.1.9 2 2 2h14
       c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2z" />
                </svg>
                <span>Kembali</span>
            </a>
        </li>

    </ul>

</nav>
<script>
    //Mengambil elemen tombol yang digunakan untuk membuka/menutup sidebar
    const toggleButton = document.getElementById("toggle-btn");
    const sidebar = document.getElementById("sidebar");

    //Menambahkan/menghapus kelas close pada sidebar → menyembunyikan atau menampilkan sidebar.
    function toggleSidebar() {
        sidebar.classList.toggle("close");
        toggleButton.classList.toggle("rotate");

        closeAllSubMenus(); //Menutup semua submenu yang sedang terbuka

        if (window.barChart) {
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
</script>