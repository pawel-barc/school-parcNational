<?php 
// Pour effectuer le test, la fonction 'protected' doit être remplacée par 'public', et 'exit;' doit être supprimé.

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../controllers/Controller.php';

class ControllerTest extends TestCase {
    private $controller;
    //Fonction standard 'setUp' pour préparer l'environnement de test, 'void' indique que la fonction ne retourne aucune valeur.
    protected function setUp(): void {
        // En simulant un objet de la classe Controller, nous pouvons imiter son comportement,
        // ce qui permet de se concentrer uniquement sur une partie spécifique de la fonction sans exécuter la redirection.
        $this->controller = $this->getMockBuilder(Controller::class)
                                 ->onlyMethods(['redirect'])// Seule la méthode 'redirect' sera simulée.
                                 ->getMock();
    }

    public function testCheckAdmin_RedirectsWhenNotLoggedIn() {
        // La session et le rôle de l'utilisateur sont ignorés.
        unset($_SESSION['user_id']);
        $_SESSION['user_role'] = null;
        // Vérification que la méthode 'redirect' a été appelée exactement une fois avec l'argument 'login'.
        $this->controller->expects($this->once())
                         ->method('redirect')
                         ->with('login');
        // Ici, la méthode checkAdmin est exécutée.
        $this->controller->checkAdmin();
    }

    public function testCheckAdmin_RedirectsWhenNotAdmin() {
        $_SESSION['user_id'] = 1; 
        $_SESSION['user_role'] = 1; 
        $this->controller->expects($this->once())
                         ->method('redirect')
                         ->with('login');
        $this->controller->checkAdmin();
    }

    public function testCheckAdmin_AllowsAccessForAdmin() {
        $_SESSION['user_id'] = 1; // L'utilisateur est connecté.
        $_SESSION['user_role'] = 2; // L'utilisateur est un administrateur.
        // On s'attend à ce que la méthode 'redirect' ne soit pas appelée.
        $this->controller->expects($this->never())
                         ->method('redirect');
        $this->controller->checkAdmin();
    }
}
