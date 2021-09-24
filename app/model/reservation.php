<?php

class Reservation
{
    private $ID;
    private $PropertyID;
    private $CabinName;
    private $StartDate;
    private $StartTime;
    private $EndDate;
    private $EndTime;
    private $Title;
    private $Location;
    private $Description;

    public function getId()
    {
        return $this->ID;
    }

    public function setId($id)
    {
        $this->ID = $id;
    }

    public function getPropertyId()
    {
        return $this->PropertyID;
    }

    public function setPropertyId($property_id)
    {
        $this->PropertyID = $property_id;
    }

    public function getCabinName()
    {
        return $this->CabinName;
    }

    public function setCabinName($cabin_name)
    {
        $this->CabinName = $cabin_name;
    }

    public function getStartDate()
    {
        return $this->StartDate;
    }

    public function setStartDate($start_date)
    {
        $this->StartDate = $start_date;
    }

    public function getStartTime()
    {
        return $this->StartTime;
    }

    public function setStartTime($start_time)
    {
        $this->StartDate = $start_time;
    }

    public function getEndDate()
    {
        return $this->EndDate;
    }

    public function setEndDate($end_date)
    {
        $this->EndDate = $end_date;
    }

    public function getEndTime()
    {
        return $this->EndTime;
    }

    public function setEndTime($end_time)
    {
        $this->EndTime = $end_time;
    }

    public function getTitle()
    {
        return $this->Title;
    }

    public function setTitle($title)
    {
        $this->Title = $title;
    }

    public function getLocation()
    {
        return $this->Location;
    }

    public function setLocation($location)
    {
        $this->Location = $location;
    }

    public function getDescription()
    {
        return $this->Description;
    }

    public function setDescription($description)
    {
        $this->Description = $description;
    }
}
