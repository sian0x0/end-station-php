<?php
class DatabaseMain
{

    public static function createDB() {
        //actual CREATE now happens via Dockerfile
    }


    private static function executeSqlFile(string $dsn, string $sql_filename) :void
    {
        $sql = file_get_contents(__DIR__ . '/../migrations/sql/' . $sql_filename);
        $statements = explode(';', $sql);

        $conn = new PDO($dsn, DB_USER, DB_PASS);

        //use exec() ina  loop to execute each statement
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
    /**
     * Populates the main database from default data (GTFS DB and hard coded)
     */
    public static function populateTableDefaults() : void
    {
        self::executeSqlFile(DSN_NO_DB, 'create_endstations_db.sql');
    }

    public static function loadDefaultEndtationsFromCache(array $rows): void {
        $conn = new PDO(DSN_MAIN, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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
            endstation_id
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
            :endstation_id
        )
    ";

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
                ':endstation_id'     => $row['endstation_id'] ?? null,
            ]);
        }
    }


}