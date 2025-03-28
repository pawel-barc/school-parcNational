document.addEventListener("DOMContentLoaded", function () {
  let calendarEl = document.getElementById("calendar");
  const urlParams = new URLSearchParams(window.location.search);
  const defaultColor = "#FF1473";

  // Les événements sont injectés via PHP
  let events = vacationEvents.map((event) => ({
    title: "Fermeture",
    start: new Date(event.start).toISOString().split("T")[0], // yyyy-mm-dd
    end: new Date(event.end).toISOString().split("T")[0],
    color: "#FF1473",
    allDay: true,
  }));

  // Fonction pour vérifier si le camping est fermé aujourd'hui
  const isClosedToday = (events) => {
    const today = new Date().toISOString().split("T")[0];
    return events.some((event) => today >= event.start && today <= event.end);
  };

  // Mettre à jour le statut du camping en fonction des vacances
  const campsiteStatusEl = document.getElementById("campsite-status");

  if (campsiteStatusEl) {
    if (isClosedToday(events)) {
      campsiteStatusEl.textContent = "Fermé"; // Statut: Fermé
      campsiteStatusEl.classList.add("status-icon", "closed");
    } else {
      campsiteStatusEl.textContent = "Ouvert"; // Statut: Ouvert
      campsiteStatusEl.classList.add("status-icon", "open");
    }
  }

  // Récup prix/nuit
  const campsiteInfo = document.querySelector(".calendar-info");
  let pricePerNight = parseFloat(
    campsiteInfo.getAttribute("data-price-per-night")
  );

  let totalPriceElement = document.getElementById("total-price");
  let personsInput = document.getElementById("num_persons");
  let priceInput = document.getElementById("calculated_price");

  let selectedDays = 0;

  const calculateTotalPrice = () => {
    let numPersons = parseInt(personsInput.value, 10) || 1; // Par défaut : 1 personne
    let totalPrice = selectedDays * pricePerNight * numPersons;
    totalPriceElement.textContent = totalPrice.toFixed(2);
    priceInput.value = totalPrice.toFixed(2);
  };

  // maj du prix selon le nombre de personnes
  personsInput.addEventListener("input", calculateTotalPrice);

  let calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    locale: "fr",
    height: "auto",
    headerToolbar: {
      start: "title",
      center: "",
      end: "prev,next today",
    },
    dayHeaderFormat: { weekday: "short" },
    events: events,
    selectable: true,
    validRange: function (nowDate) {
      return {
        start: nowDate, // Correction : pas de redéclaration de 'nowDate'
      };
    },
    selectOverlap: function (event) {
      return event.title !== "Fermeture";
    },
    select: function (info) {
      document.getElementById("start_date").value = info.startStr;
      document.getElementById("end_date").value = info.endStr;

      let startDate = new Date(info.startStr);
      let endDate = new Date(info.endStr);
      selectedDays = (endDate - startDate) / (1000 * 60 * 60 * 24);

      calculateTotalPrice();
    },
  });
  calendar.render();

  // redirection vers le calendrier au clic du form
  const startDateField = document.getElementById("start_date");
  const endDateField = document.getElementById("end_date");

  function scrollToCalendar() {
    calendarEl.scrollIntoView({ behavior: "smooth", block: "center" });
  }

  startDateField.addEventListener("focus", scrollToCalendar);
  endDateField.addEventListener("focus", scrollToCalendar);
});
