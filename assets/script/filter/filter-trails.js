//* Attache un event listener 'change' à chaque case à cocher
const Trailcheckboxes = document.getElementsByName("tag");
Trailcheckboxes.forEach(function (checkbox) {
  checkbox.addEventListener("change", function () {
    applyFilters(); // Applique le filtre dès qu'une case est cochée ou décochée
    manageTagDisplay(checkbox); // Gérer l'affichage des tags
  });
});

//* Fonction pour récupérer l'ID de l'URL
function getUrlId() {
  const params = new URLSearchParams(window.location.search);
  return params.get("id");
}

//* Fonction pour appliquer les filtres en temps réel
function applyFilters() {
  const selectedDifficulties = [];
  const selectedStatuses = [];
  const selectedLengths = [];
  const selectedTimes = [];

  // Vérifie quelles cases sont cochées
  Trailcheckboxes.forEach(function (checkbox) {
    if (checkbox.checked) {
      // Distinguer les types de filtres par classe
      if (checkbox.classList.contains("difficulty")) {
        selectedDifficulties.push(checkbox.value);
      } else if (checkbox.classList.contains("status")) {
        selectedStatuses.push(checkbox.value);
      } else if (checkbox.classList.contains("length")) {
        selectedLengths.push(checkbox.value);
      } else if (checkbox.classList.contains("time")) {
        selectedTimes.push(checkbox.value);
      }
    }
  });

  // Créer une chaîne de requête
  const queryParams = [];
  if (selectedDifficulties.length > 0) {
    queryParams.push(
      `difficulty=${encodeURIComponent(selectedDifficulties.join(","))}`
    );
  }
  if (selectedStatuses.length > 0) {
    queryParams.push(
      `status=${encodeURIComponent(selectedStatuses.join(","))}`
    );
  }
  if (selectedLengths.length > 0) {
    queryParams.push(
      `length_km=${encodeURIComponent(selectedLengths.join(","))}`
    );
  }
  if (selectedTimes.length > 0) {
    queryParams.push(`time=${encodeURIComponent(selectedTimes.join(","))}`);
  }

  const queryString = queryParams.join("&");

  fetch(`/data/data_filter_trails.php?${queryString}`)
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok: " + response.statusText);
      }
      return response.json();
    })
    .then((data) => {
      updateTrailDisplay(data);
    })
    .catch((error) => console.error("Erreur:", error));
}

//* Fonction pour gérer l'affichage des tags en fonction des cases cochées
function manageTagDisplay(checkbox) {
  const tag = document.getElementById("active-filter");

  if (checkbox.checked) {
    // Si la case est cochée, créer un tag
    const card = document.createElement("div");
    card.className = "tags";
    card.id = `tag-${checkbox.value}`; // Assigner un ID unique basé sur la valeur de la checkbox
    card.innerHTML = `
      <p>${checkbox.value}</p>
      <img class="close-tag" src='assets/icon/cross.svg'/>
    `;
    tag.appendChild(card);
    // Ajouter un event listener pour l'icône de fermeture (croix)
    card.querySelector(".close-tag").addEventListener("click", function () {
      card.remove(); // Supprimer le tag entier lorsqu'on clique sur la croix
      checkbox.checked = false; // Décocher la case correspondante
      applyFilters(); // Appliquer les filtres après suppression du tag
    });
  } else {
    // Supprimer le tag correspondant quand la checkbox est décochée
    const tagToRemove = document.getElementById(`tag-${checkbox.value}`);
    if (tagToRemove) {
      tagToRemove.remove();
    }
  }
}

//* Fonction pour afficher les données filtrées dans la div
function updateTrailDisplay(data) {
  const resultsContainer = document.getElementById("overflow");
  resultsContainer.innerHTML = ""; // Vider le conteneur des résultats précédents

  if (data && data.length > 0) {
    data.forEach((item) => {
      const card = document.createElement("div");
      card.className = "card_trails";
      // Vérifie si le sentier est dans les favoris et s'il est complété
      const isFavorite = favoriteTrailIds.includes(item.trail_id);
      const isCompleted = completedTrailIds.includes(item.trail_id);

      card.innerHTML = `
        <div class="card_top">
          <a href="details_trails?id=${encodeURIComponent(item.trail_id)}">
            <h2>${item.name}</h2>
            <img class="pic-trails" src="${item.image}" alt="${item.name}">
          </a>
        </div>

        <div class="card_details">
          <div class="lenght_trails">
            <img src="assets/icon/hiking.svg" alt="icon length">
            <p>${item.length_km} km</p>
          </div>
          
          <div class="time_trails">
            <img src="assets/icon/time.svg" alt="icon time">
            <p>${item.time}</p>
          </div>
          
          <div class="difficulty_trails">
            <img src="assets/icon/${getDifficultyIcon(
              item.difficulty
            )}" alt="${getDifficultyAlt(item.difficulty)}">
            <p>${item.difficulty}</p>
          </div>

          <div class="state_trails">
            <img src="assets/icon/${getStatusIcon(
              item.status
            )}" alt="${getStatusAlt(item.status)}">
            <p>${item.status}</p>
          </div>
        </div>

        <hr>

        <div class="access">
          <p><strong>Description du sentier</strong></p>
          <p>${item.description}</p>
          <p><strong>Accéder au sentier</strong></p>
          <p>${item.acces}</p>
        </div>
        <div class="fav-btn-container">
          <a href="/manage-favorite-trail-ajax?trail_id=${
            item.trail_id
          }" class="fav-btn ${isFavorite ? "" : "fav-btn-add"}">
            <img src="assets/icon/favorite-fill.svg" alt="heart icon">
          </a>
          <a href="/manage-completed-trail-ajax?trail_id=${
            item.trail_id
          }" class="hiking-btn ${isCompleted ? "" : "hiking-btn-add"}">
            <img src="assets/icon/hiking.svg" alt="hiking icon">
          </a>
        </div>
      `;
      // Récupère les boutons pour gérer l'ajout aux favoris et marquer comme complété
      const addFavoriteButton = card.querySelector(".fav-btn");
      const addCompletedButton = card.querySelector(".hiking-btn");
      // Fonction pour ajouter le sentier aux favoris
      function addTrailToFavorite(e) {
        e.preventDefault();
        if (isLoggedIn) {
          fetch(addFavoriteButton.getAttribute("href")).then(function () {
            addFavoriteButton.classList.remove("fav-btn-add");
            addFavoriteButton.removeEventListener("click", addTrailToFavorite);
            addFavoriteButton.addEventListener("click", deleteFavoriteButton);
          });
        } else {
          window.location.href = "/login";
        }
      }
      // Fonction pour retirer le sentier des favoris
      function deleteFavoriteButton(e) {
        e.preventDefault();
        fetch(addFavoriteButton.getAttribute("href")).then(function () {
          addFavoriteButton.classList.add("fav-btn-add");
          addFavoriteButton.removeEventListener("click", deleteFavoriteButton);
          addFavoriteButton.addEventListener("click", addTrailToFavorite);
        });
      }
      // Fonction pour marquer le sentier comme complété
      function addTrailToCompleted(e) {
        e.preventDefault();
        if (isLoggedIn) {
          fetch(addCompletedButton.getAttribute("href")).then(function () {
            addCompletedButton.classList.remove("hiking-btn-add");
            addCompletedButton.removeEventListener(
              "click",
              addTrailToCompleted
            );
            addCompletedButton.addEventListener("click", deleteCompletedButton);
          });
        } else {
          // Redirection vers la page de connexion si non connecté
          window.location.href = "/login";
        }
      }
      // Fonction pour annuler la complétion du sentier
      function deleteCompletedButton(e) {
        e.preventDefault();
        fetch(addCompletedButton.getAttribute("href")).then(function () {
          addCompletedButton.classList.add("hiking-btn-add");
          addCompletedButton.removeEventListener(
            "click",
            deleteCompletedButton
          );
          addCompletedButton.addEventListener("click", addTrailToCompleted);
        });
      }

      if (isFavorite) {
        addFavoriteButton.addEventListener("click", deleteFavoriteButton);
      } else {
        addFavoriteButton.addEventListener("click", addTrailToFavorite);
      }

      if (isCompleted) {
        addCompletedButton.addEventListener("click", deleteCompletedButton);
      } else {
        addCompletedButton.addEventListener("click", addTrailToCompleted);
      }

      resultsContainer.appendChild(card);
    });
  } else {
    resultsContainer.textContent = "Aucune donnée disponible pour ce filtre.";
  }
}

// Fonction pour obtenir l'icône de difficulté
function getDifficultyIcon(difficulty) {
  switch (difficulty) {
    case "Facile":
      return "shoes-green.svg";
    case "Moyen":
      return "shoes-orange.svg";
    case "Difficile":
      return "shoes-red.svg";
    default:
      return "shoes-default.svg";
  }
}

// Fonction pour obtenir le texte alternatif de difficulté
function getDifficultyAlt(difficulty) {
  switch (difficulty) {
    case "Facile":
      return "icon green shoes";
    case "Moyen":
      return "icon orange shoes";
    case "Difficile":
      return "icon red shoes";
    default:
      return "icon default shoes";
  }
}

// Fonction pour obtenir l'icône de statut
function getStatusIcon(status) {
  switch (status) {
    case "active":
      return "circle-green.svg";
    case "work":
      return "circle-orange.svg";
    case "inactive":
      return "circle-red.svg";
    default:
      return "circle-default.svg";
  }
}

// Fonction pour obtenir le texte alternatif de statut
function getStatusAlt(status) {
  switch (status) {
    case "active":
      return "icon green circle";
    case "work":
      return "icon orange circle";
    case "inactive":
      return "icon red circle";
    default:
      return "no info available";
  }
}

// Fonction pour récupérer tous les sentiers
function fetchAllTrails() {
  fetch("/data/data_filter_trails.php") // URL de récupération des données
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erreur lors de la récupération des données");
      }
      return response.json();
    })
    .then((data) => {
      updateTrailDisplay(data); // Met à jour l'affichage avec toutes les données
    })
    .catch((error) => {
      console.error("Erreur:", error);
    });
}

// Appelle la fonction pour récupérer tous les sentiers lors du chargement de la page
document.addEventListener("DOMContentLoaded", function () {
  fetchAllTrails(); // Récupère tous les sentiers par défaut
});

function removeAll() {
  const remove = document.getElementById("remove-filter");
  remove.addEventListener("click", function () {
    // Supprimer les tags et décocher les cases
    Trailcheckboxes.forEach(function (checkbox) {
      if (checkbox.checked) {
        checkbox.checked = false;
        const tagToRemove = document.getElementById(`tag-${checkbox.value}`);
        if (tagToRemove) {
          tagToRemove.remove();
        }
      }
    });

    // Récupérer toutes les données et les afficher
    fetch("/data/data_filter_trails.php")
      .then((response) => {
        if (!response.ok) {
          throw new Error("Erreur lors de la récupération des données");
        }
        return response.json();
      })
      .then((data) => {
        updateTrailDisplay(data); // Met à jour l'affichage avec toutes les données
      })
      .catch((error) => {
        console.error("Erreur:", error);
      });
  });
}

// Appelle la fonction pour la configurer
removeAll();
