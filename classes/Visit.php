<?php

class Visit
{
    // Note: properties must match the schema exactly
    private ?int $visit_id = null;
    private ?int $user_id = null;
    private ?int $endstation_id = null;
    private ?string $guest_ids = null;
    private ?string $visit_datetime = null;
    private ?string $photo = null;
    private ?string $notes = null;

    public function __construct(
        ?int $user_id = null,
        ?int $endstation_id = null,
        ?string $visit_datetime = null,
        ?string $guest_ids = null,
        ?string $photo = null,
        ?string $notes = null,
        ?int $visit_id = null
    ) {
        // "Hydration" guard for PDO FETCH_CLASS (must be callable on nulls)
        $this->visit_id ??= $visit_id;
        $this->user_id ??= $user_id;
        $this->endstation_id ??= $endstation_id;
        $this->guest_ids ??= $guest_ids;
        $this->visit_datetime ??= $visit_datetime ?? date("Y-m-d H:i:s");
        $this->photo ??= $photo;
        $this->notes ??= $notes;
    }

    public function save(): void
    {
        $conn = DatabaseMain::getConnection();
        if ($this->visit_id) {
            $sql = "UPDATE visits SET 
                        user_id = ?, endstation_id = ?, guest_ids = ?, 
                        visit_datetime = ?, photo = ?, notes = ? 
                    WHERE visit_id = ?";
            $params = [
                $this->user_id,
                $this->endstation_id,
                $this->guest_ids,
                $this->visit_datetime,
                $this->photo,
                $this->notes,
                $this->visit_id,
            ];
        } else {
            $sql = "INSERT INTO visits 
                        (user_id, endstation_id, guest_ids, visit_datetime, photo, notes) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $params = [
                $this->user_id,
                $this->endstation_id,
                $this->guest_ids,
                $this->visit_datetime,
                $this->photo,
                $this->notes,
            ];
        }
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);

        if (!$this->visit_id) {
            $this->visit_id = (int) $conn->lastInsertId();
        }
    }

// getters and setters
    public function getVisitId(): ?int
    {
        return $this->visit_id;
    }

    public function getUserId(): int
    {
        return $this->user_id ?? 0;
    }

    public function getStationId(): int
    {
        return $this->endstation_id ?? 0;
    }

    public function getVisitDatetime(): string
    {
        return $this->visit_datetime ?? "";
    }

    public function getNotes(): string
    {
        return $this->notes ?? "";
    }

    public function setNotes(string $notes): void
    {
        $this->notes = $notes;
    }

    public function getPhoto(): string
    {
        return $this->photo ?? "";
    }

    public function setPhoto(string $photo): void
    {
        $this->photo = $photo;
    }

    // convert polyline to/from array/string on get/set
    public function getGuestIds(): array
    {
        return $this->guest_ids ? explode(",", $this->guest_ids) : [];
    }

    public function setGuestIds(array $guest_ids): void
    {
        $this->guest_ids = implode(",", $guest_ids);
    }

//other getters
    public static function getAll(): array
    {
        $conn = DatabaseMain::getConnection();
        $stmt = $conn->query("SELECT * FROM visits");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getVisitById(int $visit_id): ?Visit
    {
        $conn = DatabaseMain::getConnection();
        $stmt = $conn->prepare("SELECT * FROM visits WHERE visit_id = ?");
        $stmt->execute([$visit_id]);
        return $stmt->fetchObject(self::class) ?: null;
    }

    public static function getVisitsByStationId(int $station_id): array
    {
        $conn = DatabaseMain::getConnection();
        $stmt = $conn->prepare("SELECT * FROM visits WHERE endstation_id = ?");
        $stmt->execute([$station_id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getVisitsByUserId(int $user_id): array   //#TODO: handle guests
    {
        $conn = DatabaseMain::getConnection();
        $stmt = $conn->prepare("SELECT * FROM visits WHERE user_id = ?");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function getLastVisitByUserId(int $user_id): ?Visit
    {
        $conn = DatabaseMain::getConnection();
        $stmt = $conn->prepare("SELECT * FROM visits WHERE user_id = ? ORDER BY visit_datetime DESC LIMIT 1");
        $stmt->execute([$user_id]);
        return $stmt->fetchObject(self::class) ?: null;
    }

    public static function deleteVisit(int $id): void
    {
        $conn = DatabaseMain::getConnection();
        $stmt = $conn->prepare("DELETE FROM visits WHERE visit_id = ?");
        $stmt->execute([$id]);
    }

//aggrgegations
    public static function hasUserVisitedStation(int $user_id, int $station_id): bool
    {
        $conn = DatabaseMain::getConnection();
        $sql = "SELECT COUNT(*) FROM visits WHERE user_id = ? AND endstation_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$user_id, $station_id]); //user ordered placeholders instead of :variables
        return (int)$stmt->fetchColumn() > 0;
    }


//html rendering
    public static function generateTableHtml(array $visits): string
    {
        $html = "<table class='users-table'>";
        $html .= "<tr><th>id</th><th>date</th><th>station</th><th>route</th><th>visitor</th><th>guests</th></tr>";

        foreach ($visits as $visit) {
            $visit_id = $visit->getVisitId();
            $date = date('j M Y', strtotime($visit->getVisitDatetime()));

            // get station details from the linked station object
            $station = Station::getStationById($visit->getStationId());
            $name = $station ? $station->getStationName() : "unknown";
            $route = $station ? $station->getRouteShortName() : "n/a";

            $html .= "<tr>";
            $html .= "<td>" . $visit_id . "</td>";
            $html .= "<td><a href='index.php?view=showVisit&visit_id=$visit_id'>" . $date . "</a></td>";
            $html .= "<td><a href='index.php?view=showStation&station_id=" . $visit->getStationId() . "'>" . htmlspecialchars($name) . "</a></td>";
            $html .= "<td>" . htmlspecialchars($route) . "</td>";
            $html .= "<td><strong>" . $visit->getUserId() . "</strong></td>";

            // handle guest display logic
            $guests = $visit->getGuestIds();
            $html .= "<td>" . (!empty($guests) ? count($guests) . " guests" : "none") . "</td>";
            $html .= "</tr>";
        }

        $html .= "</table>";
        return $html;
    }
}