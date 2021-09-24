<?php
require './app/DB.php';
require('./app/model/Rental.php');
require('./app/model/Reservation.php');
require('./app/dao/RentalDAO.php');
require('./app/dao/ReservationDAO.php');

$rentalDao = new RentalDAO();
$reservationDao = new ReservationDAO();
$rowData = array();
$itemsData = array();

if (isset($_GET['start']) && !empty($_GET['start'])) {
    $start = date($_GET['start']);
} else {
    $start = date("Y-m-d");
}

$end = date("Y-m-d", strtotime("+1 month", strtotime($start)));

echo $start;
echo $end;

$rentals = $rentalDao->getAll();
foreach ($rentals as $rental) {
    $rowData[] = [
        "PropertyID" => $rental->getPropertyId(),
        "PropertyTitle" => $rental->getPropertyTitle()
    ];
}

$reservations = $reservationDao->getReservationByDateRange($start, $end);
foreach ($reservations as $reservation) {
    if (date($reservation->getStartDate()) < $start) {
        $reservation->setStartDate($start);
    }
    if (date($reservation->getEndDate()) > $end) {
        $reservation->setEndDate($end);
    }

    // change endDate for calendar range
    $EndDate = date("Y-m-d", strtotime("-1 day", strtotime($reservation->getEndDate())));

    $itemsData[] = [
        "PropertyID" => $reservation->getPropertyId(),
        "CabinName" => $reservation->getCabinName(),
        "StartDate" => $reservation->getStartDate(),
        "EndDate" => $EndDate,
    ];
}

//echo json_encode($itemsData);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8"/>
    <title>Cabin Rentals</title>
    <link rel="stylesheet" href="./plugins/bootstrap-4.3.1/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./plugins/fontawesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./plugins/gantt-schedule-timeline-calendar/dist/style.css"/>
    <link rel="stylesheet" href="./plugins/gantt-schedule-timeline-calendar/reset.css"/>
    <link rel="stylesheet" href="./css/custom.css"/>
</head>
<body>
<div class="container-fluid">
    <div class="calendar__box">
        <div class="calendar__action">
            <div class="calendar__navigator">
                <div>
                    <i class="fa fa-calendar"></i>
                </div>
                <div>
                    <a href="/cabinrentals?start=<?php echo date("Y-m-d", strtotime("-1 month", strtotime($start))); ?>">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                </div>
                <div id="prevMonthBtn">
                    <a href="/cabinrentals?start=<?php echo date("Y-m-d", strtotime("+1 month", strtotime($start))); ?>">
                        <i class="fa fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            <div class="calendar__daterange">
                <span>
                     <?php
                     echo date("d M Y", strtotime($start));
                     ?>
                </span>
                ~
                <span>
                     <?php
                     echo date("d M Y", strtotime($end));
                     ?>
                </span>
            </div>
        </div>
        <div id="gstc"></div>
    </div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="./plugins/bootstrap-4.3.1/js/bootstrap.min.js"></script>

<script type="module">
    import GSTC from './plugins/gantt-schedule-timeline-calendar/dist/gstc.esm.min.js';

    function onRowClick(row) {
        window.row = GSTC.api.sourceID(row.id);
        window.location.href = "/cabinrentals/rental.php?id=" + GSTC.api.sourceID(row.id);
    }

    let rowsFromDB = <?php echo json_encode($rowData); ?>;
    let itemsFromDB = <?php echo json_encode($itemsData);?>;

    console.log("itemsFromDB", itemsFromDB)
    const rowsData = rowsFromDB.map((obj) => ({
        id: obj.PropertyID,
        label({row, vido}) {
            return vido.html`<a href="/cabinrentals/rental.php?id=${GSTC.api.sourceID(row.id)}" ><span>${obj.PropertyTitle}</span></a>`;

        }
        // label({row, vido}) {
        //     return vido.html`<div class="my-row-content" @click=${() =>
        //         onRowClick(
        //             row
        //         )} ><span></span>${obj.PropertyTitle}</div>`;
        //
        // }
    }))

    const itemsdata = itemsFromDB.map((obj, index) => ({
        id: index + 1,
        label: obj.CabinName,
        rowId: obj.PropertyID,
        time: {
            start: GSTC.api.date(obj.StartDate).startOf('day').valueOf(),
            end: GSTC.api.date(obj.EndDate).endOf('day').valueOf(),
        },
    }))

    const columnsData = [
        {
            id: 'id',
            label: 'ID',
            data: ({row, vido}) => vido.html`<div>${GSTC.api.sourceID(row.id)}</div>`,
            // data: ({row, vido}) =>
            //     vido.html`<div class="row-content-column" @click=${() =>
            //         onRowClick(row)}>${GSTC.api.sourceID(row.id)}</div>`, // show original id (not internal GSTCID)
            sortable: ({row}) => Number(GSTC.api.sourceID(row.id)), // sort by id converted to number
            width: 80,
            header: {
                content: 'ID',
            },
        },
        {
            id: 'label',
            data: 'label',
            sortable: 'label',
            isHTML: false,
            width: 230,
            header: {
                content: 'Rental',
            },
        },
    ];

    const config = {
        licenseKey:
            '====BEGIN LICENSE KEY====\\nZiLPlk9/lrQSTNjRdRyb0E2EJbDtTCSa3V0wEDHBaY9pES+yarblynJMNMkMjcaFv1Bid5Vgmlq5luov3kD+VWim592U/dXePpwFFsEhvLceTepQ1MftH66F8zmKaxi2KHYGFlOeCWexKo/aas8KaTW99xEYFsJT8zvfWUMywLgj4pOi932E0AZQYhispPVYcljpzkMkoQnSHZwKOZ30al98yEvHwXNNhv0Qmcs1grC9nset3+AIR72WPHdiKQGtOhfPD5Exnso3tc2DI/zX50KcUMQtOd1qqe5TWM1F0rY32UiSJNB/ChOtFD5HkGaEeuGnmEQ6R742cJhqwnXn5Q==||U2FsdGVkX18Jb05PgcQmNIztN9nNkW+U0EiwALnJhm1AqywQCetmDaTQ/1IwhxMsIesRuiV3eChv9CBH5ld6S3WqN41pfRaaj0lddXzZc+E=\\nP/yHzbVoBdOOiAANQEw6KRSvQsWFNgpZ2TVMhEMEJ2LVWW1gdVFqOS+7c7QdjEyi+QPDHdpdijcKWkh3WjC3gDix3lfJeHw1DoDl9RVpyO/YpbWf0dCj6ZOL7SvArNAOuNvLfdySbHtCorJGy6Pm/OovAf9xbR4+99XOskj4aUiazx4xvRwh1TU/epfkKhBSek2JbDqaI1QH+FpA8jZNDXc5C86PYTPYnBCydAKAwVTInQ5rEQhlGVyshnJz/07qLCwf9rMLkAxdO1/SHuUYHYG03sruImg0YdXjGaBY4q4a4ojsv5ZSIfhD0ezysuCU3TwgF2eBAJ+lIy35OjL8Zw==\\n====END LICENSE KEY====',
        list: {
            columns: {
                data: GSTC.api.fromArray(columnsData),
            },
            rows: GSTC.api.fromArray(rowsData),
        },
        chart: {
            items: GSTC.api.fromArray(itemsdata),
        },
    };

    const state = GSTC.api.stateFromConfig(config);

    // @ts-ignore
    window.state = state;

    const app = GSTC({
        element: document.getElementById('gstc'),
        state,
    });

    // @ts-ignore
    window.gstc = app;

</script>
<script type="text/javascript">
    // $(document).ready(function () {
    //     $("#loadBtn").click(function () {
    //         $.ajax({
    //             method: "GET",
    //             url: "php/get-rentals.php",
    //             success: function (result) {
    //                 console.log("result", result);
    //                 state.update('config.chart.items.' + itemId + '.label', (oldLabel) => {
    //                     return 'new label';
    //                 });
    //             },
    //             failure: function (error) {
    //                 console.log("error", error)
    //             }
    //         })
    //     })
    // })

</script>
</body>
</html>
