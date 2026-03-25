<?php

namespace App\Entity;

class User
{
    private ?int $id_utilisateur = null;
    private string $nom;
    private string $prenom;
    private string $email;
    private int $age;
    private string $mot_de_passe;
    private string $statut_compte = 'actif';
    private string $role = 'user';
    private ?string $biographie = null;
    private ?string $photo_profil = 'default-avatar.png';

    // --- ID ---
    public function getIdUtilisateur(): ?int
    {
        return $this->id_utilisateur;
    }

    public function setIdUtilisateur(?int $id): self
    {
        $this->id_utilisateur = $id;
        return $this;
    }

    // --- NOM ---
    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    // --- PRENOM ---
    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    // --- EMAIL ---
    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    // --- AGE ---
    public function getAge(): int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;
        return $this;
    }

    // --- MOT DE PASSE ---
    public function getMotDePasse(): string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mdp): self
    {
        $this->mot_de_passe = $mdp;
        return $this;
    }

    // --- STATUT ---
    public function getStatutCompte(): string
    {
        return $this->statut_compte;
    }

    public function setStatutCompte(string $statut): self
    {
        $this->statut_compte = $statut;
        return $this;
    }

    // --- ROLE ---
    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    // --- BIO ---
    public function getBiographie(): ?string 
    {
        return $this->biographie;
    }

    public function setBiographie(?string $bio): self
    {
        $this->biographie = $bio; 
        return $this;
    } 

    // --- PHOTO DE PROFIL ---
    public function getPhotoProfil(): ?string 
    { 
        return $this->photo_profil; 
    }

    public function setPhotoProfil(?string $photo): self 
    { 
        $this->photo_profil = $photo; 
        return $this;
    }
}
?>