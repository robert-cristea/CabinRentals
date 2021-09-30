<?php
//echo $_GET["id"];

require './app/DB.php';
require('./app/model/Rental.php');
require('./app/model/Reservation.php');
require('./app/dao/RentalDAO.php');
require('./app/dao/ReservationDAO.php');


$rentalDao = new RentalDAO();
$reservationDao = new ReservationDAO();

$rental_id = $_GET["id"];
$start = date("Y-m-d");
$end = date("Y-m-d", strtotime("+6 month", strtotime(date("Y-m-d"))));

$array = $rentalDao->getById($rental_id);
$rental = $array[0];

$array = $reservationDao->getReservationByPropertyId($rental_id, $start, $end);
foreach ($array as $reservation) {
    $reservations[] = [
        "PropertyID" => $reservation->getPropertyId(),
        "CabinName" => $reservation->getCabinName(),
        "StartDate" => $reservation->getStartDate(),
        "EndDate" => $reservation->getEndDate(),
    ];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8"/>
    <title>Cabin Rentals</title>
    <link rel="stylesheet" href="./assets/bootstrap-4.3.1/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./assets/fontawesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href='./assets/fullcalendar/main.css'/>
    <link rel="stylesheet" href="./css/custom.css"/>
</head>
<body>
<div class="container-fluid rental-calendar-block">
    <div class="container">
        <a href="/cabinrentals" class="text-uppercase mb-5">Home</a>
        <h2>Availability Calendar</h2>
        <h5><?php echo $rental->getPropertyTitle(); ?></h5>

        <div class="row">
            <div class="col-lg-6 col-sm-12">
                <div id="rental-calendar1" class="rental-calendar-wrapper">

                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div id="rental-calendar2" class="rental-calendar-wrapper">

                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div id="rental-calendar3" class="rental-calendar-wrapper">

                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div id="rental-calendar4" class="rental-calendar-wrapper">

                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div id="rental-calendar5" class="rental-calendar-wrapper">

                </div>
            </div>
            <div class="col-lg-6 col-sm-12">
                <div id="rental-calendar6" class="rental-calendar-wrapper">

                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>

<script src="./assets/bootstrap-4.3.1/js/bootstrap.min.js"></script>
<script src='./assets/fullcalendar/main.js'></script>
<script type="text/javascript">
    $(document).ready(function () {
        let rental = <?php echo json_encode($rental); ?>;
        let reservations = <?php echo json_encode($reservations); ?>;
        let events = reservations.map((obj) => {

            var start = new Date(obj.StartDate);
            console.log("reservations", obj.StartDate)
            console.log(start)
            start.setDate(start.getDate() + 1);
            console.log(start)

            return {
                start: start.toISOString().split('T')[0],
                end: obj.EndDate,
                display: 'background'
            }
        })

        let calendars = []

        for (let i = 0; i < 6; i++) {
            let calendarEl = document.getElementById(`rental-calendar${i + 1}`);
            calendars[i] = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    start: '',
                    center: 'title',
                    end: '',
                },
                initialView: 'dayGridMonth',
                initialDate: getInitialDate(i),
                showNonCurrentDates: false,
                eventBackgroundColor: '#e74c3c',
                events: events,
                dayCellContent: function (info, create) {
                    console.log("info", info)

                    let element;
                    if (info.isPast) {
                        element = create('span', {class: "fc-day-span-past"}, info.dayNumberText);
                    } else if (isDayBooked(info.date)) {
                        let classList = ["fc-day-span-booked"];
                        if (isBookingCheckinDate(info.date)) {
                            classList.push("fc-day-span-checkin")
                        }
                        if (isBookingCheckoutDate(info.date)) {
                            classList.push("fc-day-span-checkout")
                        }
                        element = create('span', {class: classList.join(" ")}, info.dayNumberText);
                    } else {
                        element = create('span', {}, info.dayNumberText);
                    }
                    return element;
                },
                dayCellDidMount: function (info) {
                    $(".fc-day-span-checkin").closest("td").addClass("fc-day-checkin")
                    $(".fc-day-span-checkout").closest("td").addClass("fc-day-checkout")
                    // $(".fc-event-start").closest("td").css({backgroundColor: "yellow"})
                    // $(".fc-event-end").closest("td").css({backgroundColor: "yellow"})
                },
            });

            calendars[i].render();
        }


        function isDayBooked(day) {
            return reservations.some(
                (range, index) =>
                    !!(
                        moment(day).isSameOrAfter(range.StartDate, "day") &&
                        moment(day).isSameOrBefore(range.EndDate, "day")
                    )
            );
        }

        function isBookingCheckinDate(day) {
            return reservations.some(
                (range) => moment(day).isSame(range.StartDate, "day")
            );
        }

        function isBookingCheckoutDate(day) {
            return reservations.some(
                (range) => moment(day).isSame(range.EndDate, "day")
            );
        }

        function getInitialDate(i) {
            let now = new Date();
            let current = new Date(now.getFullYear(), now.getMonth() + i, 1);

            return current;
        }

    });

    // $(document).ready(function () {
    //     let calendarEl = $("#rental-calendar1");
    //     var calendar = new FullCalendar.Calendar(calendarEl, {
    //         initialView: 'dayGridMonth'
    //     });
    //     calendar.render();
    // })

</script>
