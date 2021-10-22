<?php

require '../app/DB.php';
require('../app/model/Rental.php');
require('../app/model/Reservation.php');
require('../app/dao/RentalDAO.php');
require('../app/dao/ReservationDAO.php');

$rentalDao = new RentalDAO();
$reservationDao = new ReservationDAO();
$rentalData = array();

$rentals = $rentalDao->getAll();
foreach ($rentals as $rental) {
    $rentalData[] = [
        "PropertyID" => $rental->getPropertyId(),
        "PropertyTitle" => $rental->getPropertyTitle()
    ];
}


echo json_encode($rentalData);