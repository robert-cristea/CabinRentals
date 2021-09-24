<?php

class ReservationDAO
{
    public function insert(Reservation $reservation)
    {
    }

    public function update(Reservation $reservation)
    {
    }

    public function delete(Reservation $reservation)
    {

    }

    public function getReservationByPropertyId($rentalId, $startDate = null, $endDate = null)
    {
        $sql = 'SELECT * FROM `reservations` WHERE PropertyID =? AND StartDate <= ? AND EndDate >= ?';
        $stmt = DB::getInstance()->prepare($sql);
        $stmt->execute([$rentalId, $endDate, $startDate]);

        return $stmt->fetchAll(PDO::FETCH_CLASS, "Reservation");
    }

    public function getReservationByDateRange($startDate, $endDate)
    {
        $sql = 'SELECT * FROM `reservations` WHERE StartDate <= ? AND EndDate >= ? ORDER BY PropertyID';
        $stmt = DB::getInstance()->prepare($sql);
        $stmt->execute([$endDate, $startDate]);

        return $stmt->fetchAll(PDO::FETCH_CLASS, "Reservation");
    }
}
