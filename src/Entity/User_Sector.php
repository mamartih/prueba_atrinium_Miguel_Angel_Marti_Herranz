<?php

namespace App\Entity;

use App\Repository\SectorRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SectorRepository::class)
 */
class User_Sector
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="user_sector")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Sector", inversedBy="sector_user")
     */
    protected $sector;

    public function getUser()
    {
        return $this->user;
    }

    public function getSector()
    {
        return $this->sector;
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function setSector($sector): self
    {
        $this->sector = $sector;

        return $this;
    }

}
