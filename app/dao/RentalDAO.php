<?php

class RentalDAO
{

    public function __construct()
    {
    }

    public function insert(Rental $rental)
    {

    }

    public function update(Rental $rental)
    {

    }

    public function delete(Rental $rental)
    {

    }

    public function getById($property_id)
    {
        $sql = 'SELECT * FROM `cabinrentals` WHERE PropertyID=?';
        $stmt = DB::getInstance()->prepare($sql);
        $stmt->execute([$property_id]);

        return $stmt->fetchAll(PDO::FETCH_CLASS, "Rental");
    }

    public function getAll()
    {
        $sql = 'SELECT * FROM `cabinrentals`';
        $stmt = DB::getInstance()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_CLASS, "Rental");
    }

}
