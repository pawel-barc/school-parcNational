// Vérification en temps réel de l'adresse e-mail
document.getElementById("inputEmail").addEventListener("input", function () {
  const email = this.value.trim();
  const emailError = document.getElementById("emailError");
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  // Vérification de la validité de l'email
  if (!emailRegex.test(email)) {
    emailError.style.display = "block";
  } else {
    emailError.style.display = "none";
  }
});

// Vérification en temps réel du mot de passe
document.getElementById("inputPassword").addEventListener("input", function () {
  const password = this.value.trim();
  const passwordError = document.getElementById("passwordError");
  const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
  // Vérification de la validité du mot de passe
  if (!passwordRegex.test(password)) {
    passwordError.style.display = "block";
  } else {
    passwordError.style.display = "none";
  }
});
