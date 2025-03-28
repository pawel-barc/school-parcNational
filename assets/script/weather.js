// Calcul des dates de début et de fin pour les trois prochains jours
const today = new Date();
const startDate = today.toISOString().split("T")[0]; // Date d'aujourd'hui en format AAAA-MM-JJ
const endDate = new Date(today.setDate(today.getDate() + 3))
  .toISOString()
  .split("T")[0]; // Trois jours plus tard

// URL de l'API avec les dates spécifiées
const apiUrl = `https://api.open-meteo.com/v1/forecast?latitude=43.2965&longitude=5.3698&daily=precipitation_sum,temperature_2m_max,temperature_2m_min,windspeed_10m_max&timezone=Europe/Paris&start_date=${startDate}&end_date=${endDate}`;

fetch(apiUrl)
  .then((response) => {
    if (!response.ok) {
      throw new Error("Erreur réseau : " + response.statusText);
    }
    return response.json();
  })
  .then((data) => {
    console.log(data); // Vérifier la structure des données

    const weatherDiv = document.getElementById("weather");
    const dailyForecast = data.daily;

    // Afficher les données d'aujourd'hui (premier élément des prévisions)
    const dateToday = dailyForecast.time[0];
    const tempMaxToday = dailyForecast.temperature_2m_max[0];
    const tempMinToday = dailyForecast.temperature_2m_min[0];
    const windSpeedMaxToday = dailyForecast.windspeed_10m_max[0];
    const precipitationToday = dailyForecast.precipitation_sum[0];

    // logique pour le background de la div selon le temps
    if (precipitationToday === 0) {
      weatherDiv.style.backgroundImage = "url('/assets/img/weather/sunny.png')";
    } else if (precipitationToday > 0 && precipitationToday <= 1.0) {
      weatherDiv.style.backgroundImage =
        "url('/assets/img/weather/cloudy.png')";
    } else if (precipitationToday > 1.0) {
      weatherDiv.style.backgroundImage = "url('/assets/img/weather/rainy.png')";
    }

    // Exemple de logique pour l'icône météo principale
    let weatherIcon;
    if (precipitationToday === 0) {
      weatherIcon = "/assets/icon/sun.svg"; // Soleil s'il n'y a aucune précipitation
    } else if (precipitationToday > 0 && precipitationToday <= 1.0) {
      weatherIcon = "/assets/icon/cloud.svg"; // Nuageux si précipitations entre 0.1 et 1.0 mm
    } else if (precipitationToday > 1.0) {
      weatherIcon = "/assets/icon/rain.svg"; // Pluie si précipitations supérieures ou égales à 1.0 mm
    } else {
      weatherIcon = "/assets/icon/sun.svg"; // Icône par défaut
    }

    let todayHtml = `
        <section class="current_date">
          <section class="temperature">
              <p><strong>Ajourd'hui</strong></p>

              <div>
                <img src="/assets/icon/thermo_red.svg" alt="Température max" />
                <p>${tempMaxToday} °C</p>
              </div>

              <div>
                <img src="/assets/icon/thermo_blue.svg" alt="Température min" />
                <p>${tempMinToday} °C</p>
              </div>

              <div>
                <img src="/assets/icon/wind.svg" alt="" />
                <p>${windSpeedMaxToday} km/h</p>
              </div>

          </section>
          <div>
            <img class="current_weather" src="${weatherIcon}" alt="" />
          </div>

        </section>`;

    // Section pour les prévisions des prochains jours
    let forecastHtml = "";

    // Parcourir les prévisions restantes (jours suivants)
    for (let i = 1; i < dailyForecast.time.length; i++) {
      const date = dailyForecast.time[i];
      const tempMax = dailyForecast.temperature_2m_max[i];
      const tempMin = dailyForecast.temperature_2m_min[i];
      const windSpeedMax = dailyForecast.windspeed_10m_max[i];
      const precipitation = dailyForecast.precipitation_sum[i];

      let weatherIcon;
      if (precipitation <= 0) {
        weatherIcon = "/assets/icon/sun.svg";
      } else if (precipitation <= 1) {
        weatherIcon = "/assets/icon/cloud.svg";
      } else if (precipitation >= 1) {
        weatherIcon = "/assets/icon/rain.svg";
      } else {
        weatherIcon = "/assets/icon/sun.svg"; // icône par défaut
      }

      forecastHtml += `      
        <section class="prevision_container_data">
            <p><strong>${date}</strong></p>
            <img src="${weatherIcon}" alt="" />
            
            <section class="prevision_data">

              <div>
                <img src="/assets/icon/thermo_red.svg" alt="Température max" />
                <p>${tempMax} °C</p>
              </div>

              <div>
                <img src="/assets/icon/thermo_blue.svg" alt="Température min" />
                <p>${tempMin} °C</p>
              </div>

              <div>
                <img src="/assets/icon/wind.svg" alt="" />
                <p class="wind-parameter">${windSpeedMax} km/h</p>
              </div>
            
            </section>
          
        </section>`;
    }

    // Afficher la météo du jour et les prévisions séparément dans le div "weather"
    weatherDiv.innerHTML = `
      <div class="weather_container">
        ${todayHtml}
        <div class="previsions">
          ${forecastHtml}
        </div>
      </div>`;
  })
  .catch((error) => {
    console.error("Erreur:", error);
    document.getElementById("weather").innerHTML =
      "Impossible de récupérer les données météo.";
  });
