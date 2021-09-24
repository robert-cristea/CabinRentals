<?php

class Rental
{
    private $ID;
    private $PropertyID;
    private $PropertyTitle;
    private $PropertyType;

    public function __construct()
    {

    }

    public function getPropertyId()
    {
        return $this->PropertyID;
    }

    public function setPropertyId($property_id)
    {
        $this->PropertyID = $property_id;
    }

    public function getPropertyTitle()
    {
        return $this->PropertyTitle;
    }

    public function setPropertyTitle($property_title)
    {
        $this->PropertyTitle = $property_title;
    }

    public function getPropertyType()
    {
        return $this->PropertyType;
    }

    public function setPropertyType($property_type)
    {
        $this->PropertyType = $property_type;
    }

}
