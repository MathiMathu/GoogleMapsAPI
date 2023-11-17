<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distance Calculator</title>
    <?php $apiKey = config('services.google.maps_api_key'); ?>
    <script>
        var placeValue = @json($place ?? null);
        var destinationValue = @json($destination ?? null);
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= $apiKey ?>&libraries=places&callback=initMap" async defer></script>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }

        #map {
            height: 400px;
            margin-top: 20px;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #locationInputs {
            display: flex;
            justify-content: space-around;
            margin: 20px;
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        #placeInput,
        #destinationInput {
            width: 200px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        #outputContainer {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        #distanceOutput,
        #fareOutput {
            flex: 1;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid #ccc; /* Outline or border */
        }

        #outputContainer p {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        #outputContainer strong {
            color: #4CAF50;
        }

        .error-message {
            color: #ff0000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div id="locationInputs">
        <div>
            <label for="placeInput">Place:</label>
            <input type="text" id="placeInput" placeholder="Enter place" value="{{ $place ?? '' }}">
        </div>
        <div>
            <label for="destinationInput">Destination:</label>
            <input type="text" id="destinationInput" placeholder="Enter destination" value="{{ $destination ?? '' }}">
        </div>
        <div>
            <button onclick="calculateDistance()">Calculate Distance</button>
        </div>
    </div>

    <div id="map"></div>

    <div id="outputContainer">
        <div id="distanceOutput">
            <div id="resultContainer">
                <p><strong>Place:</strong> <span id="resultPlace">{{ $place ?? '' }}</span></p>
                <p><strong>Destination:</strong> <span id="resultDestination">{{ $destination ?? '' }}</span></p>
                <p><strong>Distance:</strong> <span id="resultDistance"></span></p>
                <p><strong>Duration:</strong> <span id="resultDuration"></span></p>
            </div>
        </div>

        <div id="fareOutput">
            <div id="fareContainer">
                <p><strong>Fare:</strong> <span id="resultFare"></span></p>
            </div>
        </div>
    </div>

    <script>
        var map;
        var distanceService;
        var distanceDisplay = document.getElementById('distanceOutput');
        var fareDisplay = document.getElementById('fareOutput');
        var place = document.getElementById('placeInput').value;
        var destination = document.getElementById('destinationInput').value;

        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: -34.397, lng: 150.644},
                zoom: 8
            });

            distanceService = new google.maps.DistanceMatrixService();

            var placeAutocomplete = new google.maps.places.Autocomplete(document.getElementById('placeInput'));
            var destinationAutocomplete = new google.maps.places.Autocomplete(document.getElementById('destinationInput'));
        }

        function calculateDistance() {
            place = document.getElementById('placeInput').value;
            destination = document.getElementById('destinationInput').value;

            if (place && destination) {
                distanceService.getDistanceMatrix({
                    origins: [place],
                    destinations: [destination],
                    travelMode: 'DRIVING',
                    unitSystem: google.maps.UnitSystem.METRIC,
                    avoidHighways: false,
                    avoidTolls: false,
                }, displayDistance);
            }
        }

        function displayDistance(response, status) {
            var resultContainer = document.getElementById('resultContainer');
            var fareContainer = document.getElementById('fareContainer');

            if (status === 'OK') {
                var distanceText = response.rows[0].elements[0].distance.text;
                var durationText = response.rows[0].elements[0].duration.text;
                var distanceValue = response.rows[0].elements[0].distance.value;

                resultContainer.innerHTML = '<p><strong>Place:</strong> <span id="resultPlace">' + place + '</span></p>' +
                    '<p><strong>Destination:</strong> <span id="resultDestination">' + destination + '</span></p>' +
                    '<p><strong>Distance:</strong> <span id="resultDistance">' + distanceText + '</span></p>' +
                    '<p><strong>Duration:</strong> <span id="resultDuration">' + durationText + '</span></p>';

                
                var ratePerMeter = 0.10;
                var fare = (distanceValue * ratePerMeter / 1000).toFixed(2); 
                fareContainer.innerHTML = '<p><strong>Fare:</strong> $' + fare + '</p>';
            } else {
                resultContainer.innerHTML = '<p class="error-message">Error calculating distance: ' + status + '</p>';
                fareContainer.innerHTML = ''; 
            }
        }
    </script>
</body>
</html>
