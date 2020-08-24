<?php
namespace App\Entity;



class MoisSearch {

    /**
     * @var string[] | null
     */
    private  $motsName;

    /**
     * @return string[]|null
     */
    public function getMotsName(): ?array
    {
        return $this->motsName;
    }

    /**
     * @param string $motsName
     * @return MoisSearch
     */
    public function setMotsName(string $motsName): MoisSearch
    {
        $this->motsName = explode(' ', $motsName);
        return $this;
    }

}