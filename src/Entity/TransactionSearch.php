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

}