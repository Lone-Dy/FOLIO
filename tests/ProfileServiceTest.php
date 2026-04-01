<?php

namespace App\Tests;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\FlashService;
use App\Service\ProfileService;
use PHPUnit\Framework\TestCase;

class ProfileServiceTest extends TestCase 
{
    public function testGetPasswordRequirementsReturnsExpectedArray(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $profileService = new ProfileService($userRepoMock, $flashServiceMock);

        $requirements = $profileService->getPasswordRequirements();

        $this->assertIsArray($requirements);
        $this->assertEquals(12, $requirements['min_length']);
        $this->assertTrue($requirements['require_special']);
    }
    public function testUpdateProfileWithValidDataReturnsTrue(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $profileService = new ProfileService($userRepoMock, $flashServiceMock);

        $user = new User();
        $user->setNom('AncienNom')
             ->setPrenom('AncienPrenom')
             ->setEmail('ancien@test.com');

        $userData = [
            'nom' => 'NouveauNom',
            'prenom' => 'NouveauPrenom',
            'email' => 'nouveau@test.com',
            'age' => 30,
            'biographie' => 'Ma super bio'
        ];

        $userRepoMock->expects($this->once())
                     ->method('update')
                     ->with($user)
                     ->willReturn(true);

        $result = $profileService->updateProfile($user, $userData, []);

        $this->assertTrue($result);
        $this->assertEquals('NouveauNom', $user->getNom());
        $this->assertEquals('nouveau@test.com', $user->getEmail());
    }

    // Test des exigences de mot de passe
    public function testUpdateProfileWithInvalidEmailReturnsFalse(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $profileService = new ProfileService($userRepoMock, $flashServiceMock);

        $user = new User();
        $userData = [
            'email' => 'email-invalide'
        ];

        $flashServiceMock->expects($this->once())
                         ->method('addError')
                         ->with("L'adresse email n'est pas valide.");

        $result = $profileService->updateProfile($user, $userData, []);

        $this->assertFalse($result);
    }

    // Test du changement de mot de passe (Cas valide)
    public function testChangePasswordWithValidDataReturnsTrue(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $profileService = new ProfileService($userRepoMock, $flashServiceMock);

        $user = new User();
      
        $user->setMotDePasse(password_hash('AncienMdp123!', PASSWORD_DEFAULT));

        $userRepoMock->expects($this->once())
                     ->method('update')
                     ->willReturn(true);

        
        $result = $profileService->changePassword($user, 'AncienMdp123!', 'NouveauMdp123!', 'NouveauMdp123!');

        $this->assertTrue($result);
      
        $this->assertTrue(password_verify('NouveauMdp123!', $user->getMotDePasse()));
    }

    // Test du changement de mot de passe (Mauvais mot de passe)
    public function testChangePasswordWithWrongCurrentPasswordReturnsFalse(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $profileService = new ProfileService($userRepoMock, $flashServiceMock);

        $user = new User();
        $user->setMotDePasse(password_hash('CorrectPassword123!', PASSWORD_DEFAULT));

        $flashServiceMock->expects($this->once())
                         ->method('addError')
                         ->with("Le mot de passe actuel est incorrect.");

        $result = $profileService->changePassword($user, 'WrongPassword', 'NouveauMdp123!', 'NouveauMdp123!');

        $this->assertFalse($result);
    }

    // Test de suppression de compte (Cas valide)
    public function testAccountDeletionWithValidPasswordReturnsTrue(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $profileService = new ProfileService($userRepoMock, $flashServiceMock);

        $user = new User();
        $user->setIdUtilisateur(99);
        $user->setMotDePasse(password_hash('Password123!', PASSWORD_DEFAULT));

        $userRepoMock->expects($this->once())
                     ->method('delete')
                     ->with(99)
                     ->willReturn(true);

        $flashServiceMock->expects($this->once())
                         ->method('addSuccess')
                         ->with("Votre compte a été supprimé avec succès.");

        $result = $profileService->accountDeletion($user, 'Password123!');

        $this->assertTrue($result);
    }

    // Test de suppression de compte
    public function testAccountDeletionDatabaseErrorReturnsFalse(): void
    {
        $userRepoMock = $this->createMock(UserRepository::class);
        $flashServiceMock = $this->createMock(FlashService::class);
        $profileService = new ProfileService($userRepoMock, $flashServiceMock);

        $user = new User();
        $user->setIdUtilisateur(99);
        $user->setMotDePasse(password_hash('Password123!', PASSWORD_DEFAULT));

        $userRepoMock->method('delete')->willReturn(false);

        $flashServiceMock->expects($this->once())
                         ->method('addError')
                         ->with("Une erreur est survenue lors de la suppression.");

        $result = $profileService->accountDeletion($user, 'Password123!');

        $this->assertFalse($result);
    }
}