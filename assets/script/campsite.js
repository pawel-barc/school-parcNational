document.addEventListener("DOMContentLoaded", function () {
  const defaultColor = "#FF1473";
  const campsiteStatusEl = document.querySelectorAll(".campsite-status");

  if (!Array.isArray(vacationEvents) || vacationEvents.length === 0) {
    console.log("pas d'événement");
    vacationEvents = [];
  }
  // Les événements sont injectés via PHP
  let events = vacationEvents.map((event) => ({
    title: "Fermeture", // Titre fixe pour chaque événement de fermeture
    start: event.start,
    end: event.end,
    color: defaultColor,
    allDay: true,
  }));

  // Fonction pour vérifier si le camping est fermé aujourd'hui
  const isClosedToday = (events) => {
    const today = new Date().toISOString().split("T")[0];
    return events.some((event) => today >= event.start && today <= event.end);
  };

  // Mettre à jour le statut du camping en fonction des vacances
  campsiteStatusEl.forEach((statusEl) => {
    const campsiteId = statusEl.id.split("-")[2];
    const campsiteStatus = campsiteStatuses.find((c) => c.id == campsiteId);
    if (isClosedToday(events)) {
      statusEl.textContent = "Fermé"; // Statut: Fermé
      statusEl.classList.add("status-icon", "closed");
    } else if (campsiteStatus.status === "Camping complet") {
      statusEl.textContent = "Complet";
      statusEl.classList.add("status-icon", "full");
    } else {
      statusEl.textContent = "Ouvert"; // Statut: Ouvert
      statusEl.classList.add("status-icon", "open");
    }
  });

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
    //initialisation FC
    initialView: "dayGridMonth",
    locale: "fr",
    headerToolbar: {
      start: "title",
      center: "",
      end: "prev,next today",
    },
    events: events,
    selectable: true,
    validRange: function (nowDate) {
      let today = new Date();
      return {
        start: today,
      };
    },

    select: function (info) {
      document.getElementById("start_date").value = info.startStr;
      document.getElementById("end_date").value = info.endStr;

      // date de début - date de fin = différence de jours   (1000 ms * 60 s * 60 min * 24 h)
      let startDate = new Date(info.startStr);
      let endDate = new Date(info.endStr);
      selectedDays = (endDate - startDate) / (1000 * 60 * 60 * 24);

      calculateTotalPrice();
    },
  });

  calendar.render();

  // 'voir +'
  document.querySelectorAll(".show-more").forEach(function (button) {
    button.addEventListener("click", function () {
      const shortText = this.previousElementSibling.previousElementSibling; // Texte court
      const longText = this.previousElementSibling;

      if (longText.style.display === "none" || !longText.style.display) {
        longText.style.display = "inline";
        shortText.style.display = "none";
        this.textContent = "Voir moins";
      } else {
        longText.style.display = "none";
        shortText.style.display = "inline";
        this.textContent = "Voir plus";
      }
    });
  });
});
