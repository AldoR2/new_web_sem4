<x-layout>
  <x-slot:title>{{ $title }}</x-slot:title>

  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <div
    x-data="calendarComponent(@js($events))"
    x-init="initCalendar"
    class="w-[310px] md:w-full mt-5 p-5 bg-white dark:bg-gray-800 dark:text-white rounded-xl shadow-md transition-colors duration-300">
    <div class="mb-6 flex flex-col md:flex-row md:items-end gap-4">
      <div class="w-full md:w-1/2">
        <label for="tahun" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Tahun</label>
        <select x-model="selectedYear" @change="updateDate" id="tahun"
          class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-400 transition">
          <option value="" hidden selected>Pilih tahun</option>
          <template x-for="year in years" :key="year">
            <option x-text="year" :value="year"></option>
          </template>
        </select>
      </div>

      <div class="w-full md:w-1/2">
        <label for="bulan" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">Bulan</label>
        <select x-model="selectedMonth" @change="updateDate" id="bulan"
          class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring focus:ring-blue-200 focus:border-blue-400 transition">
          <template x-for="(month, index) in months" :key="index">
            <option :value="index" x-text="month"></option>
          </template>
        </select>
      </div>
    </div>

    <div id="calendar" class="overflow-x-auto rounded-lg dark:text-white"></div>
  </div>

  <script>
    function calendarComponent(events) {
      return {
        calendar: null,
        years: Array.from({ length: 11 }, (_, i) => new Date().getFullYear() - 5 + i),
        months: [
          "Januari", "Februari", "Maret", "April", "Mei", "Juni",
          "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ],
        selectedYear: new Date().getFullYear(),
        selectedMonth: new Date().getMonth(),

        initCalendar() {
          const calendarEl = document.getElementById('calendar');

          this.calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            initialDate: new Date(this.selectedYear, this.selectedMonth, 1),
            headerToolbar: {
              left: 'prev,next today',
              center: 'title',
              right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            events: events,
            height: 'auto',
            locale: 'id',
            themeSystem: 'standard',
            buttonText: {
              today: 'Hari Ini',
              month: 'Bulan',
              week: 'Minggu',
              day: 'Hari',
              list: 'Daftar'
            },
            dayMaxEventRows: true,
            eventDisplay: 'block'
          });

          this.calendar.render();
        },

        updateDate() {
          const newDate = new Date(this.selectedYear, this.selectedMonth, 1);
          this.calendar.gotoDate(newDate);
        }
      }
    }
  </script>

  <!-- Responsif dan Dark Mode Tambahan -->
  <style>
    @media (max-width: 768px) {
      .fc-header-toolbar {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        align-items: stretch;
      }

      .fc-toolbar-chunk {
        display: flex;
        flex-wrap: nowrap;
        overflow-x: auto;
        gap: 0.5rem;
        padding-bottom: 0.25rem;
      }

      .fc-toolbar-chunk::-webkit-scrollbar {
        height: 4px;
      }

      .fc-toolbar-chunk::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 2px;
      }

      .fc-toolbar-title {
        text-align: center;
        font-size: 1rem;
        font-weight: 600;
      }

      .fc-button {
        flex: 0 0 auto;
        font-size: 0.65rem;
        padding: 0.3rem 0.5rem;
      }
    }

    /* Dark mode untuk fullcalendar */
    html.dark .fc {
      background-color: #1f2937 !important;
      color: #e5e7eb;
    }

    html.dark .fc .fc-button {
      background-color: #374151 !important;
      border-color: #4b5563 !important;
      color: #e5e7eb !important;
    }

    html.dark .fc .fc-button:hover {
      background-color: #4b5563 !important;
    }

    html.dark .fc .fc-daygrid-day-number {
      color: #d1d5db !important;
    }

    html.dark .fc-event {
      background-color: #2563eb !important;
      border: none !important;
    }

    html.dark .fc-event-title {
      color: white !important;
    }

    html.dark .fc-toolbar-title {
      color: white !important;
    }
  </style>
</x-layout>
