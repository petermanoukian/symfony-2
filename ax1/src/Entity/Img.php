<?php

namespace App\Entity;

use App\Repository\ImgRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImgRepository::class)]
class Img
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $picurl = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $picsize = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicurl(): ?string
    {
        return $this->picurl;
    }

    public function setPicurl(string $picurl): static
    {
        $this->picurl = $picurl;

        return $this;
    }

    public function getPicsize(): ?int
    {
        return $this->picsize;
    }

    public function setPicsize(?int $picsize): static
    {
        $this->picsize = $picsize;

        return $this;
    }
	

	
	
	
	
}
