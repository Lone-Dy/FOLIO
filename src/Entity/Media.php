<?php

namespace App\Entity;

class Media {
    private ?int $id_media = null;
    private int $id_projet;
    private string $cheminFichier;
    private string $mediaType;
    private int $ordreAffichage;
    private int $poidsFichier;


    public function getIdMedia(): ?int
    {
        return $this->id_media;
    }

    public function setIdMedia(string $id_media): self
    {
        $this->id_media = $id_media;
        return $this;
    }

    public function getIdProjet(): ?int
    {
        return $this->id_projet;
    }

    public function setIdProjet(string $id_projet): self
    {
        $this->id_projet = $id_projet;
        return $this;
    }

    public function getCheminFichier(): string 
    { 
        return $this->cheminFichier; 
    }

    public function setCheminFichier(string $chemin): self 
    { 
        $this->cheminFichier = $chemin; 
        return $this; 
    }
  
    public function getMediaType(): string 
    { 
        return $this->mediaType; 
    }

    public function setMediaType(string $type): self 
    { 
        $this->mediaType = $type; 
        return $this; 
    }

    public function getOrdreAffichage(): int 
    { 
        return $this->ordreAffichage; 
    }

    public function setOrdreAffichage(int $ordre): self 
    { 
        $this->ordreAffichage = $ordre; 
        return $this; 
    }

    public function getPoidsFichier(): int 
    { 
        return $this->poidsFichier; 
    }

    public function setPoidsFichier(int $poids): self 
    { 
        $this->poidsFichier = $poids; 
        return $this; 
    }
}

?>