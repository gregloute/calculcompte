<?php
namespace App\Entity;



class TransactionSearch {

    /**
     * @var string[] | null
     */
    private  $motsName;

    /**
     * @var float | null
     */
    private  $price;

    /**
     * @var boolean
     */
    private $depense = false;

    /**
     * @var boolean
     */
    private $revenu = false;

    /**
     * @return string[]|null
     */
    public function getMotsName(): ?array
    {
        return $this->motsName;
    }

    /**
     * @param string $motsName
     * @return TransactionSearch
     */
    public function setMotsName(string $motsName): TransactionSearch
    {
        $this->motsName = explode(' ', $motsName);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return TransactionSearch
     */
    public function setPrice(float $price): TransactionSearch
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return bool
     */
    public function isdepense(): bool
    {
        return $this->depense;
    }

    /**
     * @param bool $depense
     * @return TransactionSearch
     */
    public function setdepense(bool $depense): TransactionSearch
    {
        $this->depense = $depense;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRevenu(): bool
    {
        return $this->revenu;
    }

    /**
     * @param bool $revenu
     * @return TransactionSearch
     */
    public function setRevenu(bool $revenu): TransactionSearch
    {
        $this->revenu = $revenu;
        return $this;
    }

}