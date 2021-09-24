<?php
require '../app/DB.php';
require('../app/model/Rental.php');
require('../app/dao/RentalDAO.php');

$rentalDao = new RentalDAO();
$rentals = $rentalDao->getAll();
$data = array();

echo json_encode($rentals);