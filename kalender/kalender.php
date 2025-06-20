<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kalender Acara</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #7C2D2D 0%, #F5EBE0 100%);
            margin: 0;
            padding: 0;
            color: #4a2e2e;
        }

        header h1 {
            text-align: center;
            margin-top: 30px;
            font-size: 2.5em;
            color: #d1bca2;
            font-weight: 700;
        }

        .calendar-container {
            max-width: 900px;
            margin: 30px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            padding: 20px;
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .calendar-header h2 {
            font-size: 1.5em;
            color: #6e1414;
        }

        .calendar-header button {
            background-color: #6e1414;
            color: #fff;
            border: none;
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.3s;
        }

        .calendar-header button:hover {
            background-color: #8d1f1f;
        }

        #calendar {
            width: 100%;
            border-collapse: collapse;
        }

        #calendar th,
        #calendar td {
            border: 1px solid #e3dcd3;
            width: 14.28%;
            height: 100px;
            vertical-align: top;
            padding: 8px;
            position: relative;
            background-color: #fff;
            transition: background 0.2s;
        }

        #calendar th {
            height: 40px;
            background-color: #6e1414;
            color: #f0eae1;
            font-weight: 600;
        }

        #calendar td:hover {
            background-color: #fff6f0;
        }

        #calendar td.today {
            background-color: #fce7b2 !important;
            border: 2px solid #6e1414;
        }

        .event {
            background-color: #6e1414;
            color: #fff;
            font-size: 0.75em;
            margin-top: 6px;
            padding: 4px 6px;
            border-radius: 6px;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
            display: block;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
        }

        .modal-content h3 {
            margin-top: 0;
            color: #6e1414;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .modal-content {
            background: rgba(255, 248, 241, 0.75);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            margin: 60px auto;
            padding: 25px 30px;
            border-radius: 16px;
            max-width: 500px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
            color: #4a2e2e;
            animation: fadeInUp 0.4s ease-out;
        }

        /* Animasi munculnya modal */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Perbaikan spacing tombol */
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }


        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
        }


        input,
        select,
        textarea {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #d1bca2;
            border-radius: 6px;
            margin-top: 5px;
            background: #fff;
            color: #4a2e2e;
            font-size: 0.95em;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button {
            font-weight: 600;
        }

        button[type="submit"] {
            background-color: #6e1414;
            color: #fff;
            border: none;
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button[type="submit"]:hover {
            background-color: #8d1f1f;
        }

        button.cancel {
            background-color: #6e1414;
            color: #fff;
            border: none;
            padding: 10px 14px;
            border-radius: 6px;
            margin-top: 15px;
            cursor: pointer;
            transition: background 0.3s;
        }

        button.cancel:hover {
            background-color: #8d1f1f;
        }


        .event-list {
            margin: 10px 0;
            background: #fdf9f3;
            padding: 10px;
            border-radius: 6px;
            max-height: 200px;
            overflow-y: auto;
        }

        .event-item {
            display: flex;
            justify-content: space-between;
            border-bottom: 1px solid #e3dcd3;
            padding: 6px 0;
            font-size: 0.9em;
            align-items: center;
        }

        .event-item:last-child {
            border-bottom: none;
        }

        .event-time {
            font-weight: bold;
            color: #6e1414;
            width: 60px;
        }

        .event-type {
            font-style: italic;
            color: #9c6e6e;
            margin: 0 5px;
        }

        .delete-event {
            background: none;
            border: none;
            color: #b92d2d;
            font-weight: bold;
            font-size: 1.2em;
            cursor: pointer;
        }

        .delete-event:hover {
            color: red;
        }

        #notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #6e1414;
            color: #fff8f1;
            padding: 12px 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            font-weight: bold;
            z-index: 1000;
        }

        footer {
            color: maroon;
            padding: 12px 0;
            text-align: center;
            font-weight: 500;
            font-size: 14px;
            width: 100%;
        }

        /* .btn-kembali-container {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 999;
        }

        .btn-kembali {
            background-color: #6e1414;
            color: white;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        .btn-kembali i {
            margin-right: 8px;
        }

        .btn-kembali:hover {
            background-color: #541010;
        } */
    </style>
</head>

<body>

    <?php include 'sidebar.php'; ?>

    <!-- <script>
        history.pushState(null, null, location.href);

        window.onpopstate = function () {
            location.href = '../admin/admin_page.php';
        };
    </script>
    <div class="btn-kembali-container">
        <a href="/uas_web_kelas11_apradita/admin/admin_page.php" class="btn-kembali" title="Kembali ke Dashboard">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div> -->
    <div class="main-content">
        <header>
            <h1>Kalender Acara</h1>
        </header>

        <div class="calendar-container">
            <div class="calendar-header">
                <button onclick="changeMonth(-1)">Sebelumnya</button>
                <h2 id="monthYear"></h2>
                <button onclick="changeMonth(1)">Berikutnya</button>
            </div>
            <table id="calendar">
                <thead>
                    <tr>
                        <th>Min</th>
                        <th>Sen</th>
                        <th>Sel</th>
                        <th>Rab</th>
                        <th>Kam</th>
                        <th>Jum</th>
                        <th>Sab</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- Modal Form -->
        <div id="eventModal" class="modal" tabindex="-1" aria-hidden="true">
            <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
                <h3 id="modalTitle">Acara pada <span id="modalDate"></span></h3>

                <!-- Daftar Acara Hari Ini -->
                <div class="event-list" id="eventList"></div>

                <hr />

                <form id="eventForm">
                    <label for="eventDate">Tanggal:</label>
                    <input type="date" id="eventDate" required />

                    <label for="eventTime">Waktu:</label>
                    <input type="time" id="eventTime" required />

                    <label for="eventName">Nama Acara:</label>
                    <input type="text" id="eventName" required />

                    <label for="eventType">Jenis Acara:</label>
                    <select name="jenis_acara" id="eventType" required>
                        <option value="">Jenis Acara</option>
                        <option value="Ujian">Ujian</option>
                        <option value="Praktek">Praktek</option>
                        <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                        <option value="Rapat">Rapat Guru</option>
                        <option value="Workshop">Workshop</option>
                        <option value="Pentas Seni">Pentas Seni</option>
                        <option value="Kunjungan Industri">Kunjungan Industri</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>

                    <label for="eventDesc">Deskripsi:</label>
                    <textarea id="eventDesc" rows="3"></textarea>

                    <div class="actions">
                        <button type="submit">Tambah Acara</button>
                        <button type="button" class="cancel" onclick="closeModal()">Batal</button>
                    </div>
                </form>

            </div>
        </div>

        <div class="notification" id="notification" style="display:none;"></div>

        <script>
            function formatDateLocal(date) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            }

            const calendarBody = document.querySelector("#calendar tbody");
            const monthYear = document.getElementById("monthYear");
            const eventModal = document.getElementById("eventModal");
            const modalDateSpan = document.getElementById("modalDate");
            const eventList = document.getElementById("eventList");
            const eventForm = document.getElementById("eventForm");
            const eventDateInput = document.getElementById("eventDate");
            const eventTimeInput = document.getElementById("eventTime");
            const eventNameInput = document.getElementById("eventName");
            const eventTypeInput = document.getElementById("eventType");
            const eventDescInput = document.getElementById("eventDesc");
            const notification = document.getElementById("notification");

            let currentDate = new Date();

            // Fetch events for given year and month
            async function loadEvents(year, month) {
                try {
                    const res = await fetch(`crud.php?action=get&year=${year}&month=${String(month).padStart(2, '0')}`);
                    if (!res.ok) throw new Error('Gagal mengambil data acara');
                    const data = await res.json();
                    return data; // Expected to be an object keyed by ISO date strings
                } catch (error) {
                    console.error(error);
                    return {};
                }
            }

            // Add event via backend
            async function addEvent(eventData) {
                try {
                    const res = await fetch('crud.php?action=add', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(eventData)
                    });
                    if (!res.ok) throw new Error('Gagal menambah acara');
                    return await res.json();
                } catch (error) {
                    console.error(error);
                    return { success: false, message: error.message };
                }
            }

            // Delete event by ID via backend
            async function deleteEventById(id) {
                try {
                    const res = await fetch(`crud.php?action=delete&id=${id}`, { method: 'DELETE' });
                    if (!res.ok) throw new Error('Gagal menghapus acara');
                    return await res.json();
                } catch (error) {
                    console.error(error);
                    return { success: false, message: error.message };
                }
            }

            // Render calendar for the given date's month
            async function renderCalendar(date) {
                calendarBody.innerHTML = "";
                const year = date.getFullYear();
                const month = date.getMonth();

                // Get first day of the month (0=Sunday,...)
                const firstDay = new Date(year, month, 1).getDay();
                // Total days in the month
                const totalDays = new Date(year, month + 1, 0).getDate();

                monthYear.textContent = date.toLocaleDateString("id-ID", { month: "long", year: "numeric" });

                const events = await loadEvents(year, month + 1);

                let day = 1;
                for (let i = 0; i < 6; i++) {
                    const row = document.createElement("tr");
                    for (let j = 0; j < 7; j++) {
                        const cell = document.createElement("td");
                        if ((i === 0 && j < firstDay) || day > totalDays) {
                            cell.innerHTML = "";
                            cell.style.cursor = "default";
                        } else {
                            cell.innerHTML = `<strong>${day}</strong>`;
                            const isoDate = formatDateLocal(new Date(year, month, day));

                            // Highlight today
                            if (isoDate === formatDateLocal(new Date())) {
                                cell.classList.add("today");
                            }

                            // Show events on calendar
                            if (events[isoDate]) {
                                events[isoDate].forEach(ev => {
                                    const evDiv = document.createElement("div");
                                    evDiv.classList.add("event");
                                    evDiv.title = `${ev.waktu} - ${ev.nama_acara}`;
                                    evDiv.textContent = ev.nama_acara;


                                    ev.tanggal = isoDate;
                                    evDiv.onclick = (e) => {
                                        e.stopPropagation();
                                        openEditModal(ev);
                                    };

                                    cell.appendChild(evDiv);
                                });
                            }

                            cell.style.cursor = "pointer";
                            cell.onclick = () => openModal(isoDate);
                            day++;
                        }
                        row.appendChild(cell);
                    }
                    calendarBody.appendChild(row);
                }
            }

            function changeMonth(delta) {
                currentDate.setMonth(currentDate.getMonth() + delta);
                renderCalendar(currentDate);
            }

            function openModal(date) {
                if (!date || isNaN(new Date(date).getTime())) {
                    alert("Tanggal tidak valid. Gagal membuka modal.");
                    return;
                }
                eventModal.style.display = "block";
                modalDateSpan.textContent = new Date(date).toLocaleDateString("id-ID", {
                    weekday: "long",
                    year: "numeric",
                    month: "long",
                    day: "numeric"
                });
                eventDateInput.value = date;
                eventTimeInput.value = "";
                eventNameInput.value = "";
                eventTypeInput.value = "";
                eventDescInput.value = "";

                renderEventList(date);
            }

            function closeModal() {
                eventModal.style.display = "none";
            }

            // Render events list for selected date
            async function renderEventList(date) {
                eventList.innerHTML = "";
                try {
                    const res = await fetch(`crud.php?action=getByDate&date=${date}`);
                    if (!res.ok) throw new Error("Gagal mengambil data acara");
                    const events = await res.json();
                    if (!events || events.length === 0) {
                        eventList.innerHTML = "<p>Tidak ada acara.</p>";
                        return;
                    }
                    events.forEach(ev => {
                        const div = document.createElement("div");
                        div.className = "event-item";
                        div.title = ev.deskripsi || "";

                        const timeSpan = document.createElement("span");
                        timeSpan.className = "event-time";
                        timeSpan.textContent = ev.waktu;

                        const nameSpan = document.createElement("span");
                        nameSpan.className = "event-name";
                        nameSpan.textContent = ev.nama_acara;

                        const typeSpan = document.createElement("span");
                        typeSpan.className = "event-type";
                        typeSpan.textContent = ev.jenis_acara;

                        const delBtn = document.createElement("button");
                        delBtn.className = "delete-event";
                        delBtn.textContent = "×";
                        delBtn.title = "Hapus acara";
                        delBtn.onclick = async (e) => {
                            e.stopPropagation();
                            if (confirm("Hapus acara ini?")) {
                                const result = await deleteEventById(ev.id);
                                alert(result.message);
                                if (result.success) {
                                    renderEventList(date);
                                    renderCalendar(currentDate);
                                }
                            }
                        };

                        div.appendChild(timeSpan);
                        div.appendChild(nameSpan);
                        div.appendChild(typeSpan);
                        div.appendChild(delBtn);

                        eventList.appendChild(div);
                    });
                } catch (error) {
                    eventList.innerHTML = `<p>Error: ${error.message}</p>`;
                }
            }

            // Contoh fungsi render event di sel kalender
            function renderEvent(event) {
                const span = document.createElement('span');
                span.className = 'event';
                span.textContent = event.nama_acara;
                // Set onclick supaya bisa edit event
                span.onclick = () => openEditModal(event);
                return span;
            }

            // Fungsi buka modal dan isi data acara yang diedit
            function openEditModal(event) {
                // Buka modal
                document.getElementById('eventModal').style.display = 'block';

                // Isi tanggal modal
                document.getElementById('modalDate').textContent = event.tanggal;

                // Isi form dengan data event yang diedit
                document.getElementById('eventDate').value = event.tanggal;
                document.getElementById('eventTime').value = event.waktu;
                document.getElementById('eventName').value = event.nama_acara;
                document.getElementById('eventType').value = event.jenis_acara;
                document.getElementById('eventDesc').value = event.deskripsi;

                // Ganti tombol submit menjadi "Update" (bisa diganti fungsi submitnya juga)
                document.querySelector('#eventForm button[type="submit"]').textContent = 'Update Acara';

                // Jika perlu, simpan id event untuk update data
                document.getElementById('eventForm').dataset.editingId = event.id;
            }

            // Fungsi tutup modal
            function closeModal() {
                document.getElementById('eventModal').style.display = 'none';

                // Reset form dan state edit
                document.getElementById('eventForm').reset();
                delete document.getElementById('eventForm').dataset.editingId;
                document.querySelector('#eventForm button[type="submit"]').textContent = 'Tambah Acara';
            }


            // Fungsi update event via backend
            async function updateEvent(id, eventData) {
                // Masukkan id ke dalam eventData
                eventData.id = id;

                try {
                    const res = await fetch('crud.php?action=update', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(eventData)
                    });
                    if (!res.ok) throw new Error('Gagal memperbarui acara');
                    return await res.json();
                } catch (error) {
                    console.error(error);
                    return { success: false, message: error.message };
                }
            }


            // Submit event form (tambah atau update)
            eventForm.addEventListener("submit", async e => {
                e.preventDefault();

                const eventData = {
                    tanggal_acara: eventDateInput.value,
                    waktu: eventTimeInput.value,
                    nama_acara: eventNameInput.value.trim(),
                    jenis_acara: eventTypeInput.value,
                    deskripsi: eventDescInput.value.trim()
                };

                if (!eventData.tanggal_acara || !eventData.waktu || !eventData.nama_acara || !eventData.jenis_acara) {
                    alert("Mohon lengkapi semua kolom wajib.");
                    return;
                }

                // Cek apakah sedang edit
                const editingId = eventForm.dataset.editingId;

                let result;
                if (editingId) {
                    // update event
                    result = await updateEvent(editingId, eventData);
                } else {
                    // tambah event baru
                    result = await addEvent(eventData);
                }

                alert(result.message);

                if (result.success) {
                    renderCalendar(currentDate);
                    renderEventList(eventData.tanggal_acara);

                    // Reset form fields
                    eventTimeInput.value = "";
                    eventNameInput.value = "";
                    eventTypeInput.value = "";
                    eventDescInput.value = "";

                    if (editingId) {
                        // Reset edit state & tombol submit setelah update
                        delete eventForm.dataset.editingId;
                        document.querySelector('#eventForm button[type="submit"]').textContent = 'Tambah Acara';
                    }
                }
            });


            // Close modal when clicking outside modal content
            window.onclick = function (event) {
                if (event.target === eventModal) {
                    closeModal();
                }
            };

            // Initialize calendar on page load
            renderCalendar(currentDate);
        </script>
        <footer>
            <p style="margin: 0;">&copy; 2025 By Apradita. All rights reserved.</p>
        </footer>
    </div>

</body>

</html>