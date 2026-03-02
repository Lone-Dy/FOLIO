<?php

namespace App\Entity;

class User
{
    private ?int $id_user = null;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $password;
    private string $statut_compte;
    private string $role;

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getStatutCompte(): string
    {
        return $this->statut_compte;
    }

    public function setStatutCompte(string $statut_compte): self
    {
        $this->statut_compte = $statut_compte;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }
}
?>