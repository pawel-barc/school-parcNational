<?php
date_default_timezone_set('Europe/Paris');
require_once 'Controller.php';
require_once __DIR__ . '/../models/User.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class RegisterController extends Controller
{
    public function registerView()
    {
    // Vérifier si un token CSRF existe déjà, sinon en générer un nouveau
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        // Affichage du formulaire d'inscription avec le token CSRF
        $this->render('registerForm', ['csrf_token' => $_SESSION['csrf_token']]);
    }

    public function registerSaveForm() {
    // Vérification du token CSRF pour éviter les attaques CSRF
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $this->render('registerForm', ['error' => 'Token CSRF manquant.', 'csrf_token' => $_SESSION['csrf_token']]);
        return;
    }
    // Nettoyage et validation des entrées utilisateur
    $email = trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));
    $password = trim($_POST['password']);
    $repeatPassword = trim($_POST['repeatpassword']);
    // Vérifier si les mots de passe sont identiques
    if ($password !== $repeatPassword) {
        $this->render('registerForm', ['error' => 'Les mots de passe ne sont pas les mêmes']);
        return;
    }
        // Vérification de la complexité du mot de passe
        $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
        if (!preg_match($passwordPattern, $password)) {
            $this->render('registerForm', ['error' => 'Le mot de passe doit contenir au moins 8 caractères, y compris des lettres majuscules et minuscules, des chiffres et des caractères spéciaux.']);
            return;
        }
        // Vérification du format de l'adresse e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->render('registerForm', ['error' => "L'email doit contenir '@' et être au format correct."]);
            return;
        }
        // Vérifier si l'utilisateur existe déjà dans la base de données
        $user = new User('users');
        if ($user->userExists($email)) {
            $this->render('registerForm', ['error' => 'L\'adresse e-mail est déjà utilisée.']);
            return;
        }
        // Génération d'un token d'activation unique pour l'utilisateur
        $activationToken = bin2hex(random_bytes(50));
        $user->saveUserWithActivation($_POST, $activationToken);
        // Création du lien d'activation
        $activationLink = "http://school-parcnational.local/login?token=" . $activationToken;
        $this->sendActivationEmail($email, $activationLink);
        // Message de confirmation après l'enregistrement
        $this->render('registerForm', ['message' => 'Un email de confirmation à été envoyé. Veuillez vérifier votre boîte de réception.']);
    }

    private function sendActivationEmail($email, $link){
        $mail = new PHPMailer(true);
        try{
            // Configuration du serveur SMTP pour l'envoi des e-mails
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail-> SMTPAuth = true;
            $mail-> Username = $_ENV['MAIL_USERNAME'];
            $mail->Password = $_ENV['MAIL_PASSWORD'];
            $mail->Port = 465;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->setFrom('no-reply@parcnational.com', 'No Reply');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Activation de votre compte';
            // Contenu de l'email d'activation
            $mail->Body = "<p>Pour activer votre compte, cliquez sur le lien suivant : <a href='$link'>Activer le compte en cliquant ici</a></p>";
            // Envoi de l'email
            $mail->send();
        } catch
             (Exception $e){
                error_log("Erreur d'envoi de l'email: {$mail->ErrorInfo}");
        }
    }

    public function activateAccount() {
        // Récupération du token d'activation depuis l'URL
        $token = $_GET['token'] ?? null;
        // Vérification et activation du compte utilisateur
        if($token && (new User('users'))->activateUser($token)){
            $this->render('login', ['message' => 'Votre compte a été activé avec succès. Vous pouvez maintenant vous connecter.']);

        } else {
            // Gestion des erreurs si le token est invalide ou expiré
            $this->render('error', ['message' => 'Lien d\'activation invalide ou expiré.']);
        }
    }
}