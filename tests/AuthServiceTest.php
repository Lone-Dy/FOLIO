<?php

namespace App\Tests;

use App\Entity\User;
use App\Service\FlashService;
use App\Service\AuthService;
use App\Repository\UserRepository;
use PHPUnit\Framework\TestCase;

class AuthServiceTest extends TestCase
{
    public function testGetPasswordRequirementsReturnsExpectedArray(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $authService = new AuthService($userRepoMock, $flashServiceMock);

        $requirements = $authService->getPasswordRequirements();

        $this->assertIsArray($requirements);
        $this->assertEquals(12, $requirements['min_lenght']);
        $this->assertTrue($requirements['require_uppercase']);

    }

    public function testRegisterWithInvalidEmailReturnsFalse(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $authService = new AuthService($userRepoMock, $flashServiceMock);

        $_SESSION = [];

        $userData = [
            'nom' => 'Dupont',
            'prenom' => 'Jean',
            'email' => 'email-invalide',
            'age' => 25,
            'password' => 'MotDePasseTresSecurise123!',
            'password_confirmation' => 'MotDePasseTresSecurise123!'
        ];

        $result = $authService->register($userData);

        $this->assertFalse($result);
        $this->assertStringContainsString("L'adresse email n'est pas valide.", $_SESSION['flash_error']);
    }

    public function testLoginWithValidCredentialsReturnsUser(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $authService = new AuthService($userRepoMock, $flashServiceMock);

        $user = new User();
        $user->setEmail('test@test.com')
             ->setStatutCompte('actif')
             ->setMotDePasse(password_hash('password123', PASSWORD_BCRYPT));

        $userRepoMock->method('findByEmail')
                     ->with('test@test.com')
                     ->willReturn($user);

        $flashServiceMock->expects($this->once())
                         ->method('addSuccess')
                         ->with("Connexion réussie !");

        $result = $authService->login('test@test.com', 'password123');

        $this->assertSame($user, $result);
    }

    public function testGetCurrentUserReturnsUserWhenSessionExists(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $authService = new AuthService($userRepoMock, $flashServiceMock);

        $_SESSION = [];
        $_SESSION['user']['id'] = 42;

        $user = new User();
        $user->setIdUtilisateur(42);

        $userRepoMock->method('findById')
                     ->with(42)
                     ->willReturn($user);

        $result = $authService->getCurrentUser();

        $this->assertSame($user, $result);
    }

    public function testLogoutClearsSession(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $authService = new AuthService($userRepoMock, $flashServiceMock);

        $_SESSION = [];
        $_SESSION['user']['id'] = 42;
        $_SESSION['flash_error'] = "Une erreur";

        @$authService->logout();

        $this->assertEmpty($_SESSION);
    }
}