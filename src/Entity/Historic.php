<?php

namespace App\Entity;

use App\Repository\HistoricRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HistoricRepository::class)
 */
class Historic
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $origin_currency;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $destiny_currency;

    /**
     * @ORM\Column(type="float")
     */
    private $original_import;

    /**
     * @ORM\Column(type="float")
     */
    private $final_import;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginCurrency(): ?string
    {
        return $this->origin_currency;
    }

    public function setOriginCurrency(string $origin_currency): self
    {
        $this->origin_currency = $origin_currency;

        return $this;
    }
    

    public function getDestinyCurrency(): ?string
    {
        return $this->destiny_currency;
    }

    public function setDestinyCurrency(string $destiny_currency): self
    {
        $this->destiny_currency = $destiny_currency;

        return $this;
    }

    public function getFinalImport(): ?float
    {
        return $this->final_import;
    }

    public function setFinalImport(float $final_import): self
    {
        $this->final_import = $final_import;

        return $this;
    }

    public function getOriginalImport(): ?float
    {
        return $this->original_import;
    }

    public function setOriginalImport(float $original_import): self
    {
        $this->original_import = $original_import;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {   
        $this->date = $date;

        return $this;
    }

    public function setAll($data)
    {
        $this->origin_currency = $data['origin_currency'];
        $this->destiny_currency = $data['destiny_currency'];
        $this->original_import = $data['original_import'];
        $this->final_import = $data['finalImport'];
        $this->date = $data['time'];

        return $this;
    }
}
