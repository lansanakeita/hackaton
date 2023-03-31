<?php

namespace App\Entity;

class OfficeAddress
{
    private Office $office;
    private Address $address;

    /**
     * @return Office
     */
    public function getOffice(): Office
    {
        return $this->office;
    }

    /**
     * @param Office $office
     */
    public function setOffice(Office $office): void
    {
        $this->office = $office;
    }

    /**
     * @return Address
     */
    public function getAddress(): Address
    {
        return $this->address;
    }

    /**
     * @param Address $address
     */
    public function setAddress(Address $address): void
    {
        $this->address = $address;
    }

}