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


$rentals = $rentalDao->getAll();
foreach ($rentals as $rental) {
    $rowData[] = [
        "PropertyID" => $rental->getPropertyId(),
        "PropertyTitle" => $rental->getPropertyTitle()
    ];
}

$reservations = $reservationDao->getReservationByDateRange($start, $end);
foreach ($reservations as $reservation) {
    // modify startdate and enddate to fit in a month range
//    if (date($reservation->getStartDate()) < $start) {
//        $reservation->setStartDate($start);
//    }
//    if (date($reservation->getEndDate()) > $end) {
//        $reservation->setEndDate($end);
//    }

    $itemsData[] = [
        "PropertyID" => $reservation->getPropertyId(),
        "CabinName" => $reservation->getCabinName(),
        "StartDate" => $reservation->getStartDate(),
        "EndDate" => $reservation->getEndDate(),
    ];
}

//echo json_encode($itemsData);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8"/>
    <title>Cabin Rentals</title>
    <link rel="stylesheet" href="./assets/bootstrap-4.3.1/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./assets/fontawesome-4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="./assets/gantt-schedule-timeline-calendar/dist/style.css"/>
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
<script src="./assets/bootstrap-4.3.1/js/bootstrap.min.js"></script>

<script type="module">
    import GSTC from './assets/gantt-schedule-timeline-calendar/dist/gstc.esm.min.js';
    import {Plugin as TimelinePointer} from './assets/gantt-schedule-timeline-calendar/dist/plugins/timeline-pointer.esm.min.js';
    import {Plugin as Selection} from './assets/gantt-schedule-timeline-calendar/dist/plugins/selection.esm.min.js';
    import {Plugin as ItemMovement} from './assets/gantt-schedule-timeline-calendar/dist/plugins/item-movement.esm.min.js';
    import {Plugin as ItemResizing} from './assets/gantt-schedule-timeline-calendar/dist/plugins/item-resizing.esm.min.js';
    import {Plugin as CalendarScroll} from './assets/gantt-schedule-timeline-calendar/dist/plugins/calendar-scroll.esm.min.js';
    import {Plugin as HighlightWeekends} from './assets/gantt-schedule-timeline-calendar/dist/plugins/highlight-weekends.esm.min.js';
    import {Plugin as DependencyLines} from './assets/gantt-schedule-timeline-calendar/dist/plugins/dependency-lines.esm.min.js';
    import {Plugin as ItemTypes} from './assets/gantt-schedule-timeline-calendar/dist/plugins/item-types.esm.min.js';

    const licenseKey = '====BEGIN LICENSE KEY====\\nZiLPlk9/lrQSTNjRdRyb0E2EJbDtTCSa3V0wEDHBaY9pES+yarblynJMNMkMjcaFv1Bid5Vgmlq5luov3kD+VWim592U/dXePpwFFsEhvLceTepQ1MftH66F8zmKaxi2KHYGFlOeCWexKo/aas8KaTW99xEYFsJT8zvfWUMywLgj4pOi932E0AZQYhispPVYcljpzkMkoQnSHZwKOZ30al98yEvHwXNNhv0Qmcs1grC9nset3+AIR72WPHdiKQGtOhfPD5Exnso3tc2DI/zX50KcUMQtOd1qqe5TWM1F0rY32UiSJNB/ChOtFD5HkGaEeuGnmEQ6R742cJhqwnXn5Q==||U2FsdGVkX18Jb05PgcQmNIztN9nNkW+U0EiwALnJhm1AqywQCetmDaTQ/1IwhxMsIesRuiV3eChv9CBH5ld6S3WqN41pfRaaj0lddXzZc+E=\\nP/yHzbVoBdOOiAANQEw6KRSvQsWFNgpZ2TVMhEMEJ2LVWW1gdVFqOS+7c7QdjEyi+QPDHdpdijcKWkh3WjC3gDix3lfJeHw1DoDl9RVpyO/YpbWf0dCj6ZOL7SvArNAOuNvLfdySbHtCorJGy6Pm/OovAf9xbR4+99XOskj4aUiazx4xvRwh1TU/epfkKhBSek2JbDqaI1QH+FpA8jZNDXc5C86PYTPYnBCydAKAwVTInQ5rEQhlGVyshnJz/07qLCwf9rMLkAxdO1/SHuUYHYG03sruImg0YdXjGaBY4q4a4ojsv5ZSIfhD0ezysuCU3TwgF2eBAJ+lIy35OjL8Zw==\\n====END LICENSE KEY===='

    let rowsFromDB = <?php echo json_encode($rowData); ?>;
    let itemsFromDB = <?php echo json_encode($itemsData);?>;
    let rangeFrom = '<?php  echo $start;?>';
    let rangeTo = '<?php echo $end;?>';


    function canSelectItem(item) {
        if (typeof item.canSelect === 'boolean') return item.canSelect;
        return !doNotSelectThisItems.includes(item.id);
    }

    function preventSelection(selecting) {
        return {
            'chart-timeline-grid-row-cell': selecting['chart-timeline-grid-row-cell'].filter(
                (cell) => !doNotSelectThisCells.includes(cell.time.leftGlobalDate.format('YYYY-MM-DD'))
            ),
            'chart-timeline-items-row-item': selecting['chart-timeline-items-row-item'].filter((item) => canSelectItem(item)),
        };
    }

    function addCellBackground({time, row, vido}) {
        const isSelectable = !doNotSelectThisCells.includes(time.leftGlobalDate.format('YYYY-MM-DD'));
        console.log('ceell', time.leftGlobalDate.format('YYYY-MM-DD'), isSelectable);
        return isSelectable
            ? vido.html`<div class="selectable-cell" style="width:100%;height:100%;"></div>`
            : vido.html`<div class="not-selectable-cell" style="width:100%;height:100%;">🚫</div>`;
    }

    function onRowClick(row) {
        window.row = GSTC.api.sourceID(row.id);
        window.location.href = "/cabinrentals/rental.php?id=" + GSTC.api.sourceID(row.id);
    }

    function generateCheckinDatetime(date) {
        let dt = new Date(date)
        dt.setHours(16)
        return dt
    }

    function generateCheckoutDatetime(date) {
        let dt = new Date(date)
        dt.setHours(9)
        return dt
    }

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
            start: GSTC.api.date(generateCheckinDatetime(obj.StartDate)),
            end: GSTC.api.date(generateCheckoutDatetime(obj.EndDate)),
        },
    }))

    const columnsData = [
        {
            id: 'id',
            label: 'ID',
            data: ({row, vido}) => vido.html`<div>${GSTC.api.sourceID(row.id)}</div>`,
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

    const dateRange = {
        leftGlobal: GSTC.api.date().startOf("month").valueOf(),
        from: GSTC.api.date(rangeFrom),
        to: GSTC.api.date(rangeTo).endOf('day'),
    }

    const config = {
        licenseKey,
        list: {
            columns: {
                data: GSTC.api.fromArray(columnsData),
            },
            rows: GSTC.api.fromArray(rowsData),
        },
        chart: {
            items: GSTC.api.fromArray(itemsdata),
            time: {
                ...dateRange,
                calculatedZoomMode: true,
                onLevelDates: [({dates}) => dates],
                onCurrentViewLevelDates: [({dates}) => dates],
                onDate: [({date}) => date],
            },
        },
    };

    const state = GSTC.api.stateFromConfig(config);

    window.state = state;

    const app = GSTC({
        element: document.getElementById('gstc'),
        state,
    });

    window.gstc = app;

    // set height to max-content
    state.update("config.innerHeight", (height) => {
        let rowsCount = rowsFromDB.length
        let headerHeight = 72
        let horizontalScrollerHeight = 20
        let rowHeight = 40
        console.log("rowsFromDB", rowsCount, headerHeight + horizontalScrollerHeight + rowsCount * rowHeight, rowsFromDB)
        return headerHeight + horizontalScrollerHeight + rowsCount * rowHeight;
    });


</script>
</body>
</html>
