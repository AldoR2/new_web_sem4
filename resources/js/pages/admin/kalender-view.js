import { Calendar } from "@fullcalendar/core";
import dayGridPlugin from "@fullcalendar/daygrid";
import interactionPlugin from "@fullcalendar/interaction";

document.addEventListener("DOMContentLoaded", function () {
    const calendarEl = document.getElementById("calendar");

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: "dayGridMonth",
        events: JSON.parse(calendarEl.dataset.events),
        eventClick: function (info) {
            alert(
                info.event.title +
                    "\n" +
                    (info.event.extendedProps.description ?? "")
            );
        },
    });

    calendar.render();
});
