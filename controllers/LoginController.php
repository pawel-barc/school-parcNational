<?php
date_default_timezone_set('Europe/Paris');
require_once 'Controller.php';
require_once 'AdminMembershipController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class LoginController extends Controller
{
    // Constructeur pour charger les variables d'environnement
    public function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }

    // Affiche la page de connexion
    public function login()
    {
        $this->render('login');
    }

    // Vérifie les informations d'identification et connecte l'utilisateur
    public function loginSaveForm(){
        $user = new User('users');
        $dbUser = $user->getUserByEmail($_POST['email']);
        if ($dbUser != false) {
            if (!empty($dbUser['password'])) {
                if (password_verify($_POST['password'], $dbUser['password'])) {
                    $_SESSION['user_id'] = $dbUser['user_id'];
                    $_SESSION['user_role'] = $dbUser['role'];
                    // Redirection selon le rôle de l'utilisateur
                    if ($dbUser['role'] == 1) {
                        $this->redirect('home');
                    } else if ($dbUser['role'] == 2) {
                        $this->redirect('home');
                    }
                } else {
                $this->render('login', ['error' => 'Données incorrectes']);
            } 
            } else {
                $this->render('login', ["error' => L'utilisateur n'a pas de mot de passe."]);
            }

        } else {
            $this->render('login', ['error' => 'Données incorrectes']);
        }
    }

    // Redirige l'utilisateur vers Google pour l'authentification OAuth
    public function loginUsingGoogle()
    {
        $google_client_id = $_ENV['GOOGLE_CLIENT_ID'];
        $google_client_secret = $_ENV['GOOGLE_CLIENT_SECRET'];
        $google_redirect_url = 'http://localhost/parcNational/google-login';
        // Paramètres pour l'authentification OAuth avec Google
        $params = [
            'response_type' => 'code',
            'client_id' => $google_client_id,
            'redirect_uri' => $google_redirect_url,
            'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
            'access_type' => 'offline',
            'prompt' => 'consent'
        ];
        // Redirection vers la page de connexion Google
        header('Location: https://accounts.google.com/o/oauth2/auth?' . http_build_query($params));// converts an associative array into a link (a string with GET parameters)
    }

    // Récupère les données de l'utilisateur à partir de Google après l'authentification OAuth
    public function getDataFromGoogle(){
        if(!isset($_GET['code'])){
            error_log("La connexion annulée par l'utilisateur");
            $this->render('login', ['error' => "La connexion annulée, veuillez réessayer la connexion" ]);
            exit;
        }
        // Requête pour obtenir un token d'accès
        $google_client_id = $_ENV['GOOGLE_CLIENT_ID'];
        $google_client_secret = $_ENV['GOOGLE_CLIENT_SECRET'];
        $google_redirect_url = 'http://localhost/parcNational/google-login'; 
        $params = [
            'code' => $_GET['code'],
            'client_id' => $google_client_id,
            'client_secret' => $google_client_secret,
            'redirect_uri' => $google_redirect_url,
            'grant_type' => 'authorization_code'//We tell Google that we want an authorization code
        ];
        // Communication avec l'API Google pour récupérer les informations de l'utilisateur
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://accounts.google.com/o/oauth2/token');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);
        if ($response === false) {
            $error = curl_error($curl);
            $this->render('login', ['error' => $error]);
            return;
        }
        $responseData = json_decode($response);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://www.googleapis.com/oauth2/v3/userinfo');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $responseData->access_token]);
        $response = curl_exec($curl);

        $responseData = json_decode($response);

        $userObject = new User('users');
        $user = $userObject->getByGoogleId($responseData->sub);
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_role'] = $user['role'];
            if ($user['role'] != 1) {
                $this->redirect('admin_home');
            } else {
                $this->redirect('');
            }

        } else {
            $userObject->saveUserFromGoogle($responseData);
            $newUser = $userObject->getByGoogleId($responseData->sub);
            $_SESSION['user_id'] = $newUser['user_id'];
            $this->redirect('');
        }
    }

    // Redirige l'utilisateur vers Facebook pour l'authentification OAuth
    public function loginUsingFacebook()
    {
        if(!isset($_GET['code'])){
            error_log("La connexion annulée par l'utilisateur");
            $this->render('login', ['error' => "La connexion annulée, veuillez réessayer la connexion" ]);
            exit;
        }
        // Cette méthode sera déclenchée lorsque Facebook nous redirigera de nouveau
        $facebook_client_id = $_ENV['FACEBOOK_CLIENT_ID'];
        $facebook_client_secret = $_ENV['FACEBOOK_CLIENT_SECRET'];
        $facebook_redirect_url = 'http://localhost/parcNational/facebook-login';

        $params = [
            'code' => $_GET['code'],
            'client_id' => $facebook_client_id,
            'client_secret' => $facebook_client_secret,
            'redirect_uri' => $facebook_redirect_url
        ];

        // Configuration de la requête - nous récupérons la clé pour obtenir les données
        $curl = curl_init();// Initialise l'envoi de la requête permettant à PHP de communiquer avec des services externes via les protocoles HTTP ou HTTPS
        curl_setopt($curl, CURLOPT_URL, 'https://graph.facebook.com/oauth/access_token');
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response);

        // Data download 
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, 'https://graph.facebook.com/me?fields=name,email,picture');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $responseData->access_token]);
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response);

        $userObject = new User('users');
        $user = $userObject->getByFacebookId($responseData->id);
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_role'] = $user['role'];
            if ($user['role'] != 1) {
                $this->redirect('admin_home');
            } else {
                $this->redirect('home');
            }
        } else {
            $userObject->saveUserFromFacebook($responseData);
            $newUser = $userObject->getByFacebookId($responseData->id);
            $_SESSION['user_id'] = $newUser['user_id'];
            $_SESSION['user_role'] = $newUser['role'];
            $this->redirect('');
        }
    }

    // Affiche la page de récupération du mot de passe
    public function forgotPassword()
    {
        $this->render('forgotPassword');
    }

    // Gère la demande de réinitialisation du mot de passe et envoie un e-mail avec un lien de réinitialisation
    public function resetPasswordRequest()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $user = new User('users');
            $dbUser = $user->getUserByEmail($email);

            if ($dbUser) {
                $token = bin2hex(random_bytes(50));
                $expiry = new DateTime('+1 hour');
                $user->savePasswordResetToken($dbUser['user_id'], $token, $expiry->format('Y-m-d H:i:s'));
                $resetLink = "http://school-parcnational.local/reset-password?token=$token";
                $name = $dbUser['lastname'];
                $this->sendPasswordResetEmail($email, $resetLink, $name);
                $this->render('forgotPassword', ['message' => 'Un lien pour réinitialiser le mot de passe a été envoyé.']);
            } else {
                $this->render('forgotPassword', ['error' => "L'utilisateur avec l'adresse e-mail fournie n'existe pas."]);
            }
        }
    }

    // Envoie un e-mail contenant le lien de réinitialisation du mot de passe
    public function sendPasswordResetEmail($userEmail, $resetLink, $name)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME'];
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
            $mail->addAddress($userEmail);
            $mail->isHTML(true);
            $mail->Subject = "Réinitialisation de mot de passe";
            $mail->CharSet = 'UTF-8';
            $mail->Body = "
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <title>Réinitialisation de mot de passe</title>
                </head>
                <body>
                    <h2>Demande de réinitialisation de mot de passe</h2>
                    <p>Madame/Monsieur: <strong>$name</strong></p>
                    <p>Pour réinitialiser votre mot de passe, veuillez cliquer sur le lien ci-dessous:</p>
                    <p><a href='$resetLink'>Réinitialiser le mot de passe</a></p>
                    <p>Ce lien expirera dans 1 heure.</p>
                </body>
                </html>
            ";

            $mail->send();
        } catch (Exception $e) {
            error_log("L'erreur lors de l'envoi de l'email: {$mail->ErrorInfo}");
        }
    }

    // Réinitialise le mot de passe en utilisant le jeton envoyé par e-mail
    public function resetPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST['token'];
            $newPassword = $_POST['new_password'];
            $repeatPassword = $_POST['repeat_password'];
            if ($newPassword !== $repeatPassword) {
                $this->render('resetPassword', ['message' => 'Les mots de passe ne correspondent pas.', 'token' => $token]);
                return;
            }
            if (!$this->isPasswordValid($newPassword)) {
                $this->render('resetPassword', ['message' => 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.', 'token' => $token]);
                return;
            }
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $user = new User('users');
            $pdoToken = $user->getResetToken($token);

            if ($pdoToken && new DateTime() < new DateTime($pdoToken['expires_at'])) {
                $user->updatePassword($pdoToken['user_id'], $hashedPassword);
                $user->deleteResetToken($token);
                $this->render('login', ['message' => 'Le mot de passe a été changé avec succès.']);
            } else {
                $this->render('resetPassword', ['message' => 'Le token est invalide ou a expiré.', 'token' => $token]);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (isset($_GET['token'])) {
                $this->render('resetPassword', ['token' => $_GET['token']]);
            } else {
                $this->render('error', ['message' => 'Token manquant.']);
            }
        }
    }

    // Vérifie si un mot de passe répond aux critères de sécurité
    private function isPasswordValid($password)
    {
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
    }

    // Déconnexion de l'utilisateur
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();
        $this->redirect('');
        exit();
    }

}
