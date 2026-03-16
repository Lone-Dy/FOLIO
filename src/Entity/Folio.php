<?php

namespace App\Entity;

class Folio
{

    private ?int $id_folio = null;
    private string $titre;
    private string $description;
    private string $categorie_folio;

    public function getIdFolio(): ?int
    {
        return $this->id_folio;
    }

    public function setIdFolio(string $id_folio): self
    {
        $this->id_folio = $id_folio;
        return $this;
    }

    public function getTitre(): string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }


    public function getCategorieFolio(): string
    {
        return $this->categorie_folio;
    }

    public function setCategorieFolio(string $categorie_folio): self
    {
        $this->categorie_folio = $categorie_folio;
        return $this;
    }
}
?>