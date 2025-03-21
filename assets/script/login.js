document.addEventListener("DOMContentLoaded", function () {
  // Récupération des champs de saisie pour l'e-mail et le mot de passe
  var emailInput = document.getElementById("inputEmail");
  var passwordInput = document.getElementById("inputPassword");
  var emailError = document.createElement("div");
  emailError.id = "emailError";
  emailError.className = "error-message";
  emailInput.parentNode.appendChild(emailError);

  // Création et ajout d'un message d'erreur pour le mot de passe
  var passwordError = document.createElement("div");
  passwordError.id = "passwordError";
  passwordError.className = "error-message";
  passwordInput.parentNode.appendChild(passwordError);
  // Expression régulière pour valider l'adresse e-mail
  var emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  // Vérification de l'e-mail lors de la perte de focus
  emailInput.addEventListener("blur", function () {
    if (!emailInput.value.match(emailPattern)) {
      emailInput.style.borderColor = "red";
      emailError.textContent = "L'adresse e-mail n'est pas correcte.";
      emailError.style.color = "red";
    } else {
      emailInput.style.borderColor = "";
      emailError.textContent = "";
    }
  });
  // Vérification du mot de passe lors de la perte de focus
  passwordInput.addEventListener("blur", function () {
    if (passwordInput.value.length < 8) {
      passwordInput.style.borderColor = "red";
      passwordError.textContent =
        "Le mot de passe doit contenir au moins 8 caractères.";
      passwordError.style.color = "red";
    } else {
      passwordInput.style.borderColor = "";
      passwordError.textContent = "";
    }
  });

  // Validation des champs lors de la soumission du formulaire
  document.querySelector("form").addEventListener("submit", function (event) {
    var isValid = true;
    var errorMessage = "";
    // Vérification de l'e-mail
    if (!emailInput.value.match(emailPattern)) {
      emailInput.style.borderColor = "red";
      emailError.textContent = "L'adresse e-mail n'est pas correcte.";
      emailError.style.color = "red";
      isValid = false;
    } else {
      emailError.textContent = "";
    }

    // Vérification du mot de passe
    if (passwordInput.value.length < 8) {
      passwordInput.style.borderColor = "red";
      errorMessage += "Le mot de passe doit contenir au moins 8 caractères.";
      passwordError.style.color = "red";
      isValid = false;
    } else {
      passwordError.textContent = "";
    }

    if (!isValid) {
      alert(errorMessage);
      event.preventDefault();
    }
  });
});
