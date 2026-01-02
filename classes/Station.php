<?php

class Station
{
    // Note: properties must match DB columns for PDO Fetch Class
    // Note: properties must be public for js leaflet
    public ?string $parent_station = null;
    public ?int $endstation_id = null; // derived from parent_station
    public ?string $trip_headsign = null;
    public ?string $endstation_name = null; //derived from trip_headsign automatically
    public ?string $route_short_name = null;
    public ?int $direction_id = null;
    public ?int $route_type = null;
    public ?string $route_color = null;
    public ?string $route_text_color = null;
    public ?string $stop_lat = null;
    public ?string $stop_lon = null;
    public ?string $shape_id = null;
    public ?string $line = null; // string to match DB - converted later with getLineAsArray()
    private static string $cacheFile = __DIR__ . '/../data/cache/rows.json';

    public static array $superfluousStrings = [" Bhf", " (Berlin)", " (TF)"];

    /**
     * @param string $route_short_name Route name (human-readable name, e.g. S42)
     * @param string $trip_headsign Used as station name (destination as written on trains)
     * @param string $endstation_name Station name (cleaned version of trip_headsign)
     * @param string $parent_station Used as station ID (collects all stops at one station)
     * @param int $endstation_id Station ID (final part of parent_station)
     * @param int $route_type Route transit type (U or S-Bahn)
     * @param string $route_color Route color
     * @param string $route_text_color Route text color
     * @param string $stop_lat Stop latitude
     * @param string $stop_lon Stop longitude
     * @param string $shape_id Shape ID of linestring/polyline
     * @param string $line Linestring coordinates
     */
    public function __construct(
        ?string $route_short_name = null,
        ?string $trip_headsign = null,
        ?string $parent_station = null,
        ?int    $route_type = null,
        ?string $route_color = null,
        ?string $route_text_color = null,
        ?string $stop_lat = null,
        ?string $stop_lon = null,
        ?string $shape_id = null,
        ?string $line = null,
        ?int    $endstation_id = null
    ) {
        // ??= operator - only assign if currently null (needed for PDO object fetch)
        $this->route_short_name ??= $route_short_name;
        $this->trip_headsign ??= $trip_headsign;
        $this->parent_station ??= $parent_station;
        $this->route_type ??= $route_type;
        $this->route_color ??= $route_color;
        $this->route_text_color ??= $route_text_color;
        $this->stop_lat ??= $stop_lat;
        $this->stop_lon ??= $stop_lon;
        $this->shape_id ??= $shape_id;
        $this->line ??= $line;
        $this->endstation_id ??= $endstation_id;

        //  only derive when values not already in db
        if ($this->endstation_id === null && $this->parent_station !== null) {
            $this->endstation_id = self::deriveStationId($this->parent_station);
        }
        if ($this->endstation_name === null && $this->trip_headsign !== null) {
            $this->endstation_name = self::deriveStationName($this->trip_headsign);
        }
    }

    // transform input data to get station ID and name, and to correct linestring/polyline array format - #TODO - fix line import format instead
    private static function deriveStationId(string $parent_station): int
    {
        return (int)substr(strrchr($parent_station, ':'), 1);
    }

    public static function deriveStationName(string $trip_headsign): string
    {
        return str_replace(self::$superfluousStrings, "", $trip_headsign);
    }

    public function getLineAsArray(): array
    {
        if (!$this->line) {
            return [];
        }
        $json = (strpos($this->line, '[') === 0) ? $this->line : '[' . $this->line . ']';
        return json_decode($json, true) ?? [];
    }

    // single-attribute getters and setters

    public function getStationId(): ?int
    {
        return $this->endstation_id;
    }

    public function setStationId(int $endstation_id): void
    {
        $this->endstation_id = $endstation_id;
    }

    public static function getAll(): array
    {
        $db = DatabaseMain::getConnection();
        $sql = "SELECT * FROM endstations";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class); //return an object of this class
    }

    public function getRouteShortName(): string
    {
        return $this->route_short_name ?? "";
    }

    public function setRouteShortName(string $route_short_name): void
    {
        $this->route_short_name = $route_short_name;
    }
    public function getDirectionId(): int
    {
        return $this->direction_id ?? 0;
    }
    public function getTripHeadsign(): string
    {
        return $this->trip_headsign ?? "";
    }

    public function setTripHeadsign(string $trip_headsign): void
    {
        $this->trip_headsign = $trip_headsign;
    }

    public function getParentStation(): string
    {
        return $this->parent_station ?? "";
    }

    public function setParentStation(string $parent_station): void
    {
        $this->parent_station = $parent_station;
    }

    public function getRouteType(): int
    {
        return $this->route_type ?? 0;
    }

    public function setRouteType(int $route_type): void
    {
        $this->route_type = $route_type;
    }

    public function getRouteColor(): string
    {
        return $this->route_color ?? "";
    }

    public function setRouteColor(string $route_color): void
    {
        $this->route_color = $route_color;
    }

    public function getRouteTextColor(): string
    {
        return $this->route_text_color ?? "";
    }

    public function setRouteTextColor(string $route_text_color): void
    {
        $this->route_text_color = $route_text_color;
    }

    public function getStopLat(): string
    {
        return $this->stop_lat ?? "";
    }

    public function setStopLat(string $stop_lat): void
    {
        $this->stop_lat = $stop_lat;
    }

    public function getStopLon(): string
    {
        return $this->stop_lon ?? "";
    }

    public function setStopLon(string $stop_lon): void
    {
        $this->stop_lon = $stop_lon;
    }

    public function getShapeId(): string
    {
        return $this->shape_id ?? "";
    }

    public function setShapeId(string $shape_id): void
    {
        $this->shape_id = $shape_id;
    }

    public function getLine(): string
    {
        return $this->line ?? "";
    }

    public function setLine(string $line): void
    {
        $this->line = $line;
    }

    public function getStationName(): string
    {
        return $this->endstation_name ?? "";
    }

    public function setStationName(string $endstation_name): void
    {
        $this->endstation_name = $endstation_name;
    } //derived from parentStation automatically

    //other getters and CRUD functions
    public static function getStationById(int $endstation_id): ?self //can use self instead of Station - ie return an instance of the same class
    {
        $conn = DatabaseMain::getConnection();
        $stmt = $conn->prepare("SELECT * FROM endstations WHERE endstation_id = ?");
        $stmt->execute([$endstation_id]);
        return $stmt->fetchObject(self::class) ?: null;
    }

    public static function getStationByName(string $stationName): ?self
    {
        $conn = DatabaseMain::getConnection();
        $stmt = $conn->prepare("SELECT * FROM endstations WHERE endstation_name = ?");
        $stmt->execute([$stationName]);
        return $stmt->fetchObject(self::class) ?: null;
    }

    public static function loadJSON(): ?array
    {
        if (file_exists(self::$cacheFile)) {
            $stationsArray = json_decode(file_get_contents(self::$cacheFile), true);
            //echo "<br>INFO: Cache file exists, reading from cache";

            // Loop remains for import logic, but we no longer store in a static array property
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
                    $s['line'] // Handled as string
                );
            }
            return $stationsArray;
        } else {
            return null;
        }
    }

    public static function loadData(): ?array
    {
        //$rows = self::loadJSON();
        return Station::getAll();
    }

    //html generators
    /**
     * generates html table for stations using station objects
     */
    /**
     * generates html table for stations by fetching data internally via getall()
     */
    /**
     * generates html table for stations
     */
    public static function generateStationTableHtml(?int $target_user_id = null): string
    {
        $stations = self::getAll();

        $tableHtml = '<table><thead><tr>
        <th>route</th>
        <th></th>
        <th>end station</th>
        <th>go!</th>';

        if ($target_user_id !== null) {
            $tableHtml .= '<th>visited</th>';
        }
        $tableHtml .= '</tr></thead><tbody>';

        if (count($stations) > 0) {
            foreach ($stations as $station) {
                $routeColor = "#" . ltrim($station->getRouteColor(), '#');
                $textColor = "#" . ltrim($station->getRouteTextColor(), '#');

                $tableHtml .= "<tr>"
                    . "<td style='background-color: $routeColor; text-align:center; color: $textColor;'>"
                    . htmlspecialchars($station->getRouteShortName()) . "</td>"
                    . "<td style='text-align:center;'>";

                if ($station->getRouteType() == 109) {
                    $tableHtml .= '<img src="assets/img/s-bahn-logo.png" alt="s-bahn" style="height:22px;">';
                } elseif ($station->getRouteType() == 400) {
                    $tableHtml .= '<img src="assets/img/u-bahn-logo.png" alt="u-bahn" style="height:22px;">';
                } else {
                    $tableHtml .= htmlspecialchars((string)$station->getRouteType());
                }

                $tableHtml .= "</td>"
                    . "<td class='destination-cell' style='color:" . ($station->getRouteType() == 400 ? '#f5f5f5' : 'yellow') . ";'>"
                    . "<a href='index.php?view=showStation&station_id=" . $station->getStationId() . "'> "
                    . htmlspecialchars($station->getStationName())
                    . "</a></td>";

                $lat = urlencode($station->getStopLat());
                $lon = urlencode($station->getStopLon());
                $directionsUrl = "https://www.google.com/maps/dir/?api=1&destination={$lat},{$lon}&travelmode=transit";
                $tableHtml .= "<td><a href='$directionsUrl' target='_blank' title='plan journey on google maps'><img height='16px' src='./assets/img/directions-transit-32.png'></a></td>";

                // real visit check logic
                if ($target_user_id !== null) {
                    $tableHtml .= "<td style='text-align:center;'>";
                    if (Visit::hasUserVisitedStation($target_user_id, (int)$station->getStationId())) {
                        $tableHtml .= "✔";
                    } else {
                        $tableHtml .= "<span style='color: #ccc;'>-</span>";
                    }
                    $tableHtml .= "</td>";
                }

                $tableHtml .= "</tr>";
            }
        } else {
            $cols = $target_user_id !== null ? 5 : 4;
            $tableHtml .= "<tr><td colspan='$cols'>0 results found</td></tr>";
        }

        $tableHtml .= '</tbody></table>';
        return $tableHtml;
    }

    public static function makeSelectOption(): string
    {
        $htmlString = '<select name="StationId" id="Station">';
        foreach (self::getAll() as $station) { // display the name but submit the ID value
            $htmlString .= '<option value="' . $station->getStationID() . '">' . $station->getStationName() . '</option>';
        }
        return $htmlString . '</select><br>';
    }
}