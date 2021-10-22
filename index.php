

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8"/>
    <title>Cabin Rentals</title>
    <link rel="stylesheet" href="./assets/bootstrap-4.3.1/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="./assets/fontawesome-4.7.0/css/font-awesome.min.css">
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
                    <a id="prevMonthLink" href="/cabinrentals?start=">
                        <i class="fa fa-chevron-left"></i>
                    </a>
                </div>
                <div>
                    <a id="nextMonthLink" href="/cabinrentals?start=">
                        <i class="fa fa-chevron-right"></i>
                    </a>
                </div>
            </div>
            <div class="calendar__daterange">
                <span id="rangeStartDate"></span>
                ~
                <span id="rangeEndDate"></span>
            </div>
        </div>
        <div id="root"></div>
    </div>

</div>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"-->
<!--        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"-->
<!--        crossorigin="anonymous"></script>-->
<script src="./assets/bootstrap-4.3.1/js/bootstrap.min.js"></script>
<script src="./build/demo.bundle.js"></script>
</body>
</html>
