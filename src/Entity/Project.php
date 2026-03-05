<?php

namespace App\Entity;

class Project
{
    private ?int $id_projet = null;
    private string $type;
    private string $contenu;
    private string $ordre_affichage;


    public function getIdProjet(): ?int
    {
        return $this->id_projet;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getContenu(): string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    public function getOrdreAffichage(): string
    {
        return $this->ordre_affichage;
    }

    public function setOrdreAffichage(string $ordre_affichage): self
    {
        $this->ordre_affichage = $ordre_affichage;
        return $this;
    }
}
?>