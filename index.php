<!-- // ================================================= // -->

<?php
// define variables and set to empty values
$locationNameErr = "";
$location = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(urlencode($_POST["location"]))) {
        $locationNameErr = "Location name is required";
    } else {
        $location = urlencode(test_input($_POST["location"]));
        // check if name only contains letters and whitespace
        if (!preg_match("/^[a-zA-Z-' ]*$/", $location)) {
            $locationNameErr = "Only letters and white space allowed";
        }
    }
}

$api_key = 'Your_API_KEY';

$url = "http://api.weatherapi.com/v1/current.json?key=$api_key&q=$location";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$locationData = json_decode($response);



if ($locationData) {
    $country = $locationData->location->country;
    $loc_name = $locationData->location->name;
    $lon = $locationData->location->lon;
    $lat = $locationData->location->lat;
    $timezone = $locationData->location->tz_id;

    $celTemp = $locationData->current->temp_c;
    $farTemp = $locationData->current->temp_f;
    $la_updated = $locationData->current->last_updated;
    $day_or_night = $locationData->current->is_day;

    if ($day_or_night == 1) {
        $day_or_night = "Day";
    } elseif ($day_or_night == 0) {
        $day_or_night = "Night";
    } else {
        $day_or_night = "Invalid Data";
    }

    $condition = $locationData->current->condition->text;

    $wind_mph = $locationData->current->wind_mph;
    $wind_kph = $locationData->current->wind_kph;
    $pressure_mb = $locationData->current->pressure_mb;
    $pressure_in = $locationData->current->pressure_in;

    $precip_mm = $locationData->current->precip_mm;
    $precip_in = $locationData->current->precip_in;
    $humidity = $locationData->current->humidity;
    $uv = $locationData->current->uv;
} else {
    echo "Failed to retrieve weather data.";
}


function test_input($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<!-- // ================================================= // -->

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Weather App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        .row-cols-1 .col {
            flex-basis: 0;
            flex-grow: 1;
            max-width: 24%;
            margin-bottom: 10px;
            align-items: center;
        }

        .right {
            position: absolute;
            right: 0px;
            margin-right: 3px;
        }

        .error {
            color: #FF0000;
        }
    </style>

</head>

<body>
    <div class="container mt-3">

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="col-form-label">Enter Place Name</label>
                </div>
                <div class="col-auto">
                    <input type="text" id="text" name="location" class="form-control">
                </div>

                <div class="col-auto right">
                    <?php date_default_timezone_set($timezone);
                    echo date("Y/m/d") . " " . date("h:i:sa") . " " . date("l");
                    ?>
                </div>

                <div class="col-auto">
                    <button type="submit" class="btn btn-secondary" name="submit">Submit</button>
                </div>

                <span class="error"><?php echo $locationNameErr; ?></span>
                <br><br>

            </div>
        </form>
        <hr>

        <div class="row mb-2">

            <!-- === ===  LOCATION DETAILS  === ===  -->
            <div class="col-md-6">
                <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <h3 class="mb-0">Location</h3>
                        <div class="mb-1 text-body-secondary"> <?php echo "{$loc_name}"; ?> </div>
                        <p class="card-text mb-auto"><?php echo "Country: {$country}"; ?></p>
                        <p class="card-text mb-auto"><?php echo "Longitude: {$lon}"; ?> </p>
                        <p class="card-text mb-auto"> <?php echo "Latitude: {$lat}"; ?> </p>
                        <p class="card-text mb-auto"> <?php echo "Timezone: {$timezone}"; ?> </p>
                        <p class="card-text mb-auto"> <?php echo "Day/Night: {$day_or_night}"; ?> </p>
                    </div>

                    <div class="col-auto d-none d-lg-block">
                        <svg class="bd-placeholder-img" width="200" height="250" focusable="false">
                            <i class="fa-solid fa-location-dot fa-bounce text-center m-2 bd-placeholder-img" width="200" height="250" focusable="false" style="font-size: 40px;"></i>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- === ===  TEMPERATURE/CONDITION DETAILS  === ===  -->
            <div class="col-md-6">
                <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <h3 class="mb-0">Terperature</h3> <br>
                        <p class="card-text mb-auto"><?php echo "Temperature(℃/℉): {$celTemp}/{$farTemp}"; ?></p>
                        <p class="card-text mb-auto"><?php echo "Last updated: {$la_updated}"; ?> </p>
                        <p class="card-text mb-auto"> <?php echo "Condition: {$condition}"; ?> </p>
                    </div>

                    <div class="col-auto d-none d-lg-block">
                        <svg class="bd-placeholder-img" width="200" height="250" focusable="false">
                            <i class="fa-solid fa-temperature-high fa-bounce text-center m-2 bd-placeholder-img" width="200" height="250" focusable="false" style="font-size: 40px;"></i>
                        </svg>
                    </div>
                </div>
            </div>


            <!-- === ===  WIND/PRESSURE  === ===  -->
            <div class="col-md-6">
                <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <h3 class="mb-0">Wind/Pressure</h3> <br>
                        <p class="card-text mb-auto"><?php echo "Wind Speed(MPH/KPH): {$wind_mph}/{$wind_mph}"; ?><br><br>
                            <?php echo "Pressure(MB/IN): {$pressure_mb}/{$pressure_in}"; ?> </p>
                    </div>

                    <div class="col-auto d-none d-lg-block">
                        <svg class="bd-placeholder-img" width="200" height="250" focusable="false">
                            <i class="fa-solid fa-wind fa-bounce text-center m-2 bd-placeholder-img" width="200" height="250" focusable="false" style="font-size: 40px;"></i>
                        </svg>
                    </div>
                </div>
            </div>



            <!-- === ===  HUMIDITY/UV  === ===  -->
            <div class="col-md-6">
                <div class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <h3 class="mb-0">Humidity/UV</h3> <br>
                        <p class="card-text mb-auto"><?php echo "Precipitation(MM/IN): {$precip_mm}/{$precip_in}"; ?><br><br>
                            <?php echo "Humidity: {$humidity}"; ?> <br><br>
                            <?php echo "UV Ray: {$uv}"; ?>

                        </p>
                    </div>

                    <div class="col-auto d-none d-lg-block">
                        <svg class="bd-placeholder-img" width="200" height="250" focusable="false">
                            <i class="fa-solid fa-droplet fa-bounce text-center m-2 bd-placeholder-img" width="200" height="250" focusable="false" style="font-size: 40px;"></i>
                        </svg>
                    </div>
                </div>
            </div>


        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" integrity="sha384-rOA1PnstxnOBLzCLMcre8ybwbTmemjzdNlILg8O7z1lUkLXozs4DHonlDtnE7fpc" crossorigin="anonymous"></script>

    <script defer src="https://use.fontawesome.com/releases/v5.15.4/js/fontawesome.js" integrity="sha384-dPBGbj4Uoy1OOpM4+aRGfAOc0W37JkROT+3uynUgTHZCHZNMHfGXsmmvYTffZjYO" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" integrity="sha384-DyZ88mC6Up2uqS4h/KRgHuoeGwBcD4Ng9SiP4dIRy0EXTlnuz47vAwmeGwVChigm" crossorigin="anonymous" />


    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/fontawesome.css" integrity="sha384-jLKHWM3JRmfMU0A5x5AkjWkw/EYfGUAGagvnfryNV3F9VqM98XiIH7VBGVoxVSc7" crossorigin="anonymous" />

</body>

</html>
