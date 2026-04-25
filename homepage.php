<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT a.AccommodationID, a.Name, a.Address, a.ContactNum, a.Amenities,
           r.RmType, r.PricePerRmType, r.AvailableRms
        FROM accommodation a
        LEFT JOIN rooms r ON a.AccommodationID = r.AccommodationID
        WHERE r.AvailableRms > 0
        ORDER BY a.AccommodationID LIMIT 8";

$result = $mysqli->query($sql);

$accommodations = [];

while ($row = $result->fetch_assoc()) {
    $id = $row['AccommodationID'];

    if (!isset($accommodations[$id])) {
        $accommodations[$id] = [
            'Name' => $row['Name'],
            'Address' => $row['Address'],
            'ContactNum' => $row['ContactNum'],
            'Amenities' => $row['Amenities'],
            'Rooms' => []
        ];
    }

    if ($row['RmType']) {
        $accommodations[$id]['Rooms'][] = [
            'RmType' => $row['RmType'],
            'Price' => $row['PricePerRmType'],
            'Available' => $row['AvailableRms']
        ];
    }
}

include __DIR__ . "/homepage.html";
?>