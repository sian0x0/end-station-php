<?php
class DatabaseMain
{
    private static object $dbh; // database handle to re-use
    public static function getConnection(): object
    {
        if (!isset(self::$dbh)) {
            try {
                self::$dbh = new PDO(DSN_MAIN, DB_USER, DB_PASS);
                self::$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo 'Connection failed: ' . $e->getMessage();
            }
        }
        return self::$dbh;
    }

    public static function createDB() {
        //actual CREATE now happens via Dockerfile
    }

    /**
     * Populates the main database from default data (GTFS DB, JSON, or hard coded)
     */
    public static function populateTableDefaults(array $rows = null) : void
    {
        self::executeSqlFile(DSN_NO_DB, 'create_endstations_db.sql');
        if ($rows) {
            self::populateDefaultEndtationsFromCache($rows);
        }
    }

    private static function executeSqlFile(string $dsn, string $sql_filename) :void
    {
        $sql = file_get_contents(__DIR__ . '/../migrations/sql/' . $sql_filename);
        $statements = explode(';', $sql);

        $conn = new PDO($dsn, DB_USER, DB_PASS);

        //use exec() in a loop to execute each statement
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                #echo $statement;
                try {
                    $conn->exec($statement);
                } catch (PDOException $e) {
                    echo "\nError executing statement:\n$statement\n";
                    echo $e->getMessage() . "\n\n";
                }
            }
        }
    }

    public static function populateDefaultEndtationsFromCache(array $rows): void {
        $sql = "
        INSERT INTO endstation_db.endstations (
            route_short_name,
            trip_headsign,
            parent_station,
            direction_id,
            route_type,
            route_color,
            route_text_color,
            stop_lat,
            stop_lon,
            shape_id,
            line,
            endstation_id,
            endstation_name
        )
        VALUES (
            :route_short_name,
            :trip_headsign,
            :parent_station,
            :direction_id,
            :route_type,
            :route_color,
            :route_text_color,
            :stop_lat,
            :stop_lon,
            :shape_id,
            :line,
            :endstation_id,
            :endstation_name
        )
    ";
        $conn = self::getConnection();
        $stmt = $conn->prepare($sql);

        foreach ($rows as $row) {
            $stmt->execute([
                ':route_short_name'  => $row['route_short_name'] ?? null,
                ':trip_headsign'     => $row['trip_headsign'] ?? null,
                ':parent_station'    => $row['parent_station'] ?? null,
                ':direction_id'      => $row['direction_id'] ?? null,
                ':route_type'        => $row['route_type'] ?? null,
                ':route_color'       => $row['route_color'] ?? null,
                ':route_text_color'  => $row['route_text_color'] ?? null,
                ':stop_lat'          => $row['stop_lat'] ?? null,
                ':stop_lon'          => $row['stop_lon'] ?? null,
                ':shape_id'          => $row['shape_id'] ?? null,
                ':line'              => $row['line'] ?? null,
                ':endstation_name'   => Station::deriveStationName($row['trip_headsign']) ?? null,
                ':endstation_id'     => (int) substr(strrchr($row['parent_station'], ':'), 1) ?? null,
            ]);
        }
    }

    public static function getByID(int $id, string $tableName) : ?array {
        $id_string = rtrim($tableName, 's') . '_id';

        $conn = self::getConnection();

        $sql = "SELECT * from $tableName where $id_string = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getAll(string $tableName) : array {
        //echo "getting $tableName\n";
        $conn = self::getConnection();
        if (in_array($tableName, ['endstations', 'visits'])) {
            $sql = "SELECT * from $tableName";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } if ($tableName == 'users') {
            $sql = "SELECT user_id, username, join_date, profile_picture, role from $tableName";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_CLASS);
        } else {
            return [];
        }

    }

    public static function isDatabaseEmpty(): bool {
        $conn = self::getConnection();

        $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN); //TODO:make method getTables
        // if a table has at least 1 row, the db is not empty
        foreach ($tables as $table) {
            $count = $conn->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            if ($count > 0) {
                return false;
            }
        }
        return true; // if no data in any table, db is empty
    }

}

