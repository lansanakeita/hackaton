<?php

namespace App\Entity;

class OfficeSearch {

    /**
     * @var int|null
     */
    private $maxPrice;


    /**
     * @return int|null
     */
    public function getMaxPrice(): ?int
    {
        return $this->maxPrice;
    }

    /**
     * @param int|null $maxPrice
     * @return OfficeSearch
     */
    public function setMaxPrice(int $maxPrice): OfficeSearch
    {
        $this->maxPrice = $maxPrice;
        return $this;
    }


}