<?php

class Visit
{

    static int $counter = 1;
    private int $visit_id;
    private string $station_id;
    private string $visit_date;
    private int $user_id;
    private array $guest_ids;
    static array $visitsStaticArr = [];

    public function __construct($station_id, $visit_date = null, $visit_id = null, $user_id = null, $guest_ids = [])
    {
        $this->visit_id = $visit_id ?? self::$counter++; //TODO: make counter hande non-sequential
        $this->station_id = $station_id;
        $this->visit_date = $visit_date ?? date('Y-m-d H:i:s'); //default: now
        $this->user_id = $user_id;
        $this->guest_ids = $guest_ids ?? [];
        self::$visitsStaticArr[] = $this;
    }


// CRUD methods
    public static function readVisitsJSON()
    {
        #TODO: move here from main
    }
    public static function writeVisitsJSON()
    {

        #TODO
    }
    public static function deleteVisit(mixed $id)
    {
        #TODO - after array changed to id-indexed array
    }
    public static function loadData()
    {

    }

// single-property getters and setters

    public function getVisitId(): int
    {
        return $this->visit_id;
    }

    public function setVisitId(int $visit_id): Visit
    {
        $this->visit_id = $visit_id;
        return $this;
    }

    private static function getVisitsStaticArr() :array
    {
        return self::$visitsStaticArr;
    }

    public function getStationId(): string
    {
        return $this->station_id;
    }

    public function setStationId(string $station_id): Visit
    {
        $this->station_id = $station_id;
        return $this;
    }

    public function getVisitDate(): string
    {
        return $this->visit_date;
    }

    public function setVisitDate(string $visit_date): Visit
    {
        $this->visit_date = $visit_date;
        return $this;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function setUserId(int $user_id): Visit
    {
        $this->user_id = $user_id;
        return $this;
    }

    public function getGuestIds(): array
    {
        return $this->guest_ids;
    }

    public function setGuestIds(array $guest_ids): Visit
    {
        $this->guest_ids = $guest_ids;
        return $this;
    }

//other getters
    public static function getVisitById(int $visit_id): ?Visit
    {
        $visits = self::getVisitsStaticArr();
        print_r(self::$visitsStaticArr);
        foreach ($visits as $visit) { //todo: decide if reading from db or object array!
            if ($visit->getVisitId() == $visit_id) {
                print_r($visit);
                return $visit;
            }
        }
        return null;
    }
}