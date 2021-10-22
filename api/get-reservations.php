<?php

require '../app/DB.php';
require('../app/model/Rental.php');
require('../app/model/Reservation.php');
require('../app/dao/RentalDAO.php');
require('../app/dao/ReservationDAO.php');

$rentalDao = new RentalDAO();
$reservationDao = new ReservationDAO();
$rowData = array();
$itemsData = array();

$start = date("Y-m-d");
$duration = "month";

if (isset($_GET['start']) && !empty($_GET['start'])) {
    $start = date($_GET['start']);
}
if (isset($_GET['duration']) && !empty($_GET['duration'])) {
    if ($_GET['duration'] === "month" || $_GET['duration'] === "week") {
        $duration = $_GET['duration'];
    }
}

if ($duration === "week") {
    $end = date("Y-m-d", strtotime("+1 week", strtotime($start)));
} else {
    $end = date("Y-m-d", strtotime("+1 month", strtotime($start)));
}


$reservations = $reservationDao->getReservationByDateRange($start, $end);
foreach ($reservations as $reservation) {
    $itemsData[] = [
        "PropertyID" => $reservation->getPropertyId(),
        "CabinName" => $reservation->getCabinName(),
        "StartDate" => $reservation->getStartDate(),
        "EndDate" => $reservation->getEndDate(),
    ];
}

echo json_encode($itemsData);