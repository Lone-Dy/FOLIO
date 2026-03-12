<?php

namespace App\Entity;

class Media {
    private ?int $id_media = null;
    private int $id_projet;
    private string $cheminFichier;
    private string $mediaType;
    private int $ordreAffichage;
    private int $poidsFichier;

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

    public function setPoidsFichier(int $poids): self 
    { 
        $this->poidsFichier = $poids; 
        return $this; 
    }
}

?>