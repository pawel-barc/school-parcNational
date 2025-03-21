document.addEventListener("DOMContentLoaded", function () {
  // Récupération des champs de mot de passe et de répétition du mot de passe
  let passwordInput = document.getElementById("inputPassword");
  let repeatPasswordInput = document.getElementById("inputRepeatPassword");
  // Création des éléments pour afficher les messages d'erreur
  let passwordError = document.createElement("div");
  passwordError.id = "passwordError";
  passwordError.className = "error-message";
  passwordInput.parentNode.appendChild(passwordError);

  let repeatPasswordError = document.createElement("div");
  repeatPasswordError.id = "repeatPasswordError";
  repeatPasswordError.className = "error-message";
  repeatPasswordInput.parentNode.appendChild(repeatPasswordError);
  // Expression régulière pour valider le mot de passe
  const passwordPattern =
    /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};':"\\|,.<>/?]).{8,}$/;
  // Validation du mot de passe après avoir quitté le champ (blur)
  passwordInput.addEventListener("blur", function () {
    if (!passwordPattern.test(passwordInput.value)) {
      passwordInput.style.borderColor = "red";
      passwordError.textContent =
        "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
      passwordError.style.color = "red";
    } else {
      passwordInput.style.borderColor = "";
      passwordError.textContent = "";
    }
  });
  // Vérification si le mot de passe répété correspond au premier (input)
  repeatPasswordInput.addEventListener("input", function () {
    if (repeatPasswordInput.value !== passwordInput.value) {
      repeatPasswordInput.style.borderColor = "red";
      repeatPasswordError.textContent =
        "Les mots de passe ne correspondent pas.";
      repeatPasswordError.style.color = "red";
    } else {
      repeatPasswordInput.style.borderColor = "";
      repeatPasswordError.textContent = "";
    }
  });
  // Validation avant l'envoi du formulaire
  document.querySelector("form").addEventListener("submit", function (event) {
    let isValid = true;
    let errorMessage = "";
    // Vérification de la validité du mot de passe
    if (!passwordPattern.test(passwordInput.value)) {
      passwordInput.style.borderColor = "red";
      errorMessage +=
        "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.\n";
      isValid = false;
    }
    // Vérification si les mots de passe correspondent
    if (repeatPasswordInput.value !== passwordInput.value) {
      repeatPasswordInput.style.borderColor = "red";
      errorMessage += "Les mots de passe ne correspondent pas.";
      isValid = false;
    }

    if (!isValid) {
      alert(errorMessage);
      event.preventDefault();
    }
  });
});
