<?php
class Station
{
    private string $parentStation;
    private string $stationId;
    private string $tripHeadsign;
    private string $stationName; //derived from tripHeadsign automatically
    private string $routeShortName;
    private int $routeType;
    private string $routeColor;
    private string $routeTextColor;
    private string $stopLat;
    private string $stopLon;
    private string $shapeId;
    private array $line;
    static array $stationsStaticArr;
    private static string $cacheFile = __DIR__ . '/../data/cache/rows.json';

    public static array $superfluousStrings = [" Bhf"," (Berlin)"," (TF)"];

    /**
     * @param string $routeShortName Route name (human-readable name, e.g. S42)
     * @param string $tripHeadsign Used as station name (destination as written on trains)
     * @param string $parentStation Used as station ID (collects all stops at one station)
     * @param int $routeType Route transit type (U or S-Bahn)
     * @param string $routeColor Route color
     * @param string $routeTextColor Route text color
     * @param string $stopLat Stop latitude
     * @param string $stopLon Stop longitude
     * @param string $shapeId Shape ID of linestring
     * @param array $line Linestring coordinates
     */
    public function __construct(
        string $routeShortName,
        string $tripHeadsign,
        string $parentStation,
        int    $routeType,
        string $routeColor,
        string $routeTextColor,
        string $stopLat,
        string $stopLon,
        string $shapeId,
        array  $line
    )
    {
        $this->stationId = self::deriveStationID($parentStation);
        $this->stationName = self::deriveStationName($tripHeadsign);
        $this->routeShortName = $routeShortName;
        $this->tripHeadsign = $tripHeadsign;
        $this->parentStation = $parentStation;
        $this->routeType = $routeType;
        $this->routeColor = $routeColor;
        $this->routeTextColor = $routeTextColor;
        $this->stopLat = $stopLat;
        $this->stopLon = $stopLon;
        $this->shapeId = $shapeId;
        $this->line = $line;
        self::$stationsStaticArr[] = $this;
    }
// transform input data to get station ID and name
    private static function deriveStationId(string $parentStation): string
    {
        return (int) substr(strrchr($parentStation, ':'), 1);
    }

    public static function deriveStationName(string $tripHeadsign): string
    {
        return str_replace(self::$superfluousStrings, "", $tripHeadsign);
    }


    // single-attribute getters and setters

    public function getStationId(): string
    {
        return $this->stationId;
    }

    public function setStationId(string $stationId): void
    {
        $this->stationId = $stationId;
    }

    private static function getAll()
    {
        return self::$stationsStaticArr;
    }

    public function getRouteShortName(): string
    {
        return $this->routeShortName;
    }

    public function setRouteShortName(string $routeShortName): void
    {
        $this->routeShortName = $routeShortName;
    }

    public function getTripHeadsign(): string
    {
        return $this->tripHeadsign;
    }

    public function setTripHeadsign(string $tripHeadsign): void
    {
        $this->tripHeadsign = $tripHeadsign;
    }

    public function getParentStation(): string
    {
        return $this->parentStation;
    }

    public function setParentStation(string $parentStation): void
    {
        $this->parentStation = $parentStation;
    }

    public function getRouteType(): int
    {
        return $this->routeType;
    }

    public function setRouteType(int $routeType): void
    {
        $this->routeType = $routeType;
    }

    public function getRouteColor(): string
    {
        return $this->routeColor;
    }

    public function setRouteColor(string $routeColor): void
    {
        $this->routeColor = $routeColor;
    }

    public function getRouteTextColor(): string
    {
        return $this->routeTextColor;
    }

    public function setRouteTextColor(string $routeTextColor): void
    {
        $this->routeTextColor = $routeTextColor;
    }

    public function getStopLat(): string
    {
        return $this->stopLat;
    }

    public function setStopLat(string $stopLat): void
    {
        $this->stopLat = $stopLat;
    }

    public function getStopLon(): string
    {
        return $this->stopLon;
    }

    public function setStopLon(string $stopLon): void
    {
        $this->stopLon = $stopLon;
    }

    public function getShapeId(): string
    {
        return $this->shapeId;
    }

    public function setShapeId(string $shapeId): void
    {
        $this->shapeId = $shapeId;
    }

    public function getLine(): array
    {
        return $this->line;
    }

    public function setLine(array $line): void
    {
        $this->line = $line;
    }

    public function getStationName(): string
    {
        return $this->stationName;
    }

    public function setStationName(string $stationName): void
    {
        $this->stationName = $stationName;
    } //derived from parentStation automatically

//other getters and CRUD functions
    public static function getStationById (int $stationId): Station
    {
        $s = null;
        //echo $stationId . "<br>" . "<br>";
        //print_r(self::$stationsStaticArr);
        foreach (self::getAll() as $station) {
            //echo $station->getStationId() . "<br>";
            if ($station->getStationId() == $stationId) {
                $s = $station;
                break; //there is only 1 to find, no need to carry on looping
            }
        }
        return $s;
    }

    public static function getStationByName (string $stationName): Station
    {
        $s = null;
        //echo $stationName;
        //print_r(self::$stationsStaticArr);
        foreach (self::bojhb as $station) {
            if ($station->getStationName() === $stationName) {
                $s = $station;
                break; //there is only 1 to find, no need to carry on looping
            }
        }
        return $s;
    }

    public static function loadJSON(): ?array
    {
        if (file_exists(self::$cacheFile)) {

            $stationsArray = json_decode(file_get_contents(self::$cacheFile), true);
            //echo "<br>INFO: Cache file exists, reading from cache";

            //clear central list of objects first
            Station::$stationsStaticArr = [];

            //create a new object from each item
            foreach ($stationsArray as $s) {
                $sNew = new Station(
                    $s['route_short_name'],
                    $s['trip_headsign'],
                    $s['parent_station'],
                    $s['route_type'],
                    $s['route_color'],
                    $s['route_text_color'],
                    $s['stop_lat'],
                    $s['stop_lon'],
                    $s['shape_id'],
                    [$s['line']] // Convert JSON string to array
                );
            }
            return $stationsArray;
        } else {
            return null;
        }
    }


    public static function loadData() : ?array
    {
        //TODO: read from DB also
        //$rows = self::loadJSON();
        $rows = DatabaseMain::getAll('endstations');
        return $rows;
    }

//html generators
    public static function generateTableHtml($rows): string {
        $tableHtml = '<table>
        <tr>
            <th>Route</th>
            <th></th>
            <th>End station</th>
            <th>Go!</th>';
        global $user_id;
        if (isset($user_id)) {
            $tableHtml .= '<th>visited</th>';}
        $tableHtml .= '</tr>';

        //generate columns 1-4, or 1-5 if user logged in
        if (count($rows) > 0) {
            foreach ($rows as $row) {
                // 1. Route names and colors
                $tableHtml .= "<tr>"
                    . "<td bgcolor='" . htmlspecialchars($row['route_color'])
                    . "' style='text-align:center;color:#" . htmlspecialchars($row['route_text_color']) . ";'>"
                    . htmlspecialchars($row['route_short_name']) . " -" . $row['direction_id'] . "</td>"
                    . "<td style='text-align:center;'>";

                // 2. Service logos
                if ($row['route_type'] == 109) {
                    $tableHtml .= '<img src="assets/img/s-bahn-logo.png" alt="S-Bahn" style="height:22px;">';
                } elseif ($row['route_type'] == 400) {
                    $tableHtml .= '<img src="assets/img/u-bahn-logo.png" alt="U-Bahn" style="height:22px;">';
                } else {
                    $tableHtml .= htmlspecialchars($row['route_type']);
                }

                // 3. Station name (cleaned Headsign) formatted depending on transit type
                $stationName = str_replace(self::$superfluousStrings, "", $row['trip_headsign']);
                $endstation_id = $row['endstation_id'];
                $tableHtml .= "</td>"
                    . "<td class='destination-cell' style='color:" . ($row['route_type'] == 400 ? '#f5f5f5' : 'yellow') . ";'>"
                    . "<a href ='index.php?view=showStation&station_id=$endstation_id'> "
                    . htmlspecialchars($stationName)
                    . "</a></td>";

                // 4. Google directions button
                $lat = htmlspecialchars($row['stop_lat']);
                $lon = htmlspecialchars($row['stop_lon']);
                $directionsUrl = "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lon}&travelmode=transit";
                $directionsTitle='plan journey on Google Maps';
                $tableHtml .= "<td><a href='$directionsUrl' target='_blank' title='$directionsTitle'><img height=16px src='./assets/img/directions-transit-32.png'></a></td>";

                // 5. User visited staus if logged in
                $tableHtml .= "<td>";
                $userHasVisited = rand(0,1); //#TODO test display of different states before making checker function #TEST
                global $user_id;
                if(isset($user_id)) {
                    if ($userHasVisited == 1) {
                    $tableHtml .= "X";}
                }
                $tableHtml .= "</td>";

                // 6. Close the row
                $tableHtml .= "</tr>";
            }
        } else {
            $tableHtml .= "<tr><td colspan='4'>0 results</td></tr>";
        }

        $tableHtml .= '</table>';
        return $tableHtml;
    }

    public static function makeSelectOption (array $objectArray): string
    {
        $entity = strtolower(get_class($objectArray[0]));
        $htmlString = '<select name="' . $entity . 'Id"' . 'id="' . $entity . '">';
        foreach ($objectArray as $object) { // display the name but submit the ID value
            $htmlString .= '<option value="' . $object->getStationID() . '">' . $object->getStationName() . '</option>';
        }
        return $htmlString . '</select><br>';
    }
}