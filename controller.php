<?php
$json = array(
    [
        "date" => "2022-09-19",
        "temperature_1" => 295,
        "temperature_2" => 341,
        "temperature_3" => 136,
    ],[
        "date" => "2022-09-18",
        "temperature_1" => 100,
        "temperature_2" => 362,
        "temperature_3" => 140,
    ],[
        "date" => "2022-09-17",
        "temperature_1" => 9,
        "temperature_2" => 512,
        "temperature_3" => 450,
    ],[
        "date" => "2022-09-16",
        "temperature_1" => 15,
        "temperature_2" => 112,
        "temperature_3" => 450,
    ],[
        "date" => "2022-09-15",
        "temperature_1" => 115,
        "temperature_2" => 312,
        "temperature_3" => 150,
    ],[
        "date" => "2022-09-14",
        "temperature_1" => 215,
        "temperature_2" => 312,
        "temperature_3" => 50,
    ]
);

header('Content-type: application/json');
echo json_encode($json);
?>