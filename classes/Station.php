<?php
class Station
{
    private string $parentStation;
    private string $stationId; //derived from parentStation automatically
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

    /**
     * @param string $routeShortName Route name (human-readable name)
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
        //#TODO
        return trim($parentStation);
    }

    private static function deriveStationName(string $tripHeadsign): string
    {
        $superfluousStrings = [" Bhf"," (Berlin)"," (TF)"];
        return trim($tripHeadsign);
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
//other getters and CRUD functions
    public function loadData()
    {

    }


}