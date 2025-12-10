USE VBB_GTFS;

## DROP TABLE endstations;
CREATE TABLE endstations AS
WITH 
-- 1. Get only S-Bahn and U-Bahn Services routes run by the agencies BVG and Berlin S-Bahn
    filtered_routes AS (
        SELECT route_id, route_short_name, route_type, route_color, route_text_color, agency_id
        FROM routes
        WHERE route_type IN (400, 109) AND agency_id IN (1, 796)
        ),
-- 2. Get all platform stops
    stop_sequences AS (
        SELECT r.route_short_name, r.route_type, r.route_color, r.route_text_color,
                s.stop_name, s.parent_station, s.stop_lat, s.stop_lon,
                t.trip_headsign, t.direction_id, t.shape_id,
                st.stop_id, st.stop_sequence
        FROM stop_times st
        JOIN trips t ON st.trip_id = t.trip_id
        JOIN stops s ON st.stop_id = s.stop_id
        JOIN filtered_routes r ON t.route_id = r.route_id
        WHERE s.parent_station IS NOT NULL
        ),
-- 3. Rank the sequence number per route name and direction (to find the real end of the line)    
    ranked_terminal_stops AS (
        SELECT *, ROW_NUMBER() OVER (PARTITION BY route_short_name, direction_id ORDER BY stop_sequence DESC) AS seq_rank
        FROM stop_sequences
        ),
-- 4. Get only the stop that has the highest sequence number per direction (to find the real end of the line)   
    final_terminals AS (
        SELECT route_short_name, trip_headsign, direction_id, route_type, route_color, route_text_color, stop_lat, stop_lon, parent_station, shape_id
        FROM ranked_terminal_stops
        WHERE seq_rank = 1
        ),
-- 5. Check that any disqualifying end connections actually run more than once (to ignore edge cases in the data)
    trip_counts_by_station_route AS (
        SELECT r.route_short_name, s.parent_station, COUNT(DISTINCT t.trip_id) AS trip_count
        FROM stop_times st
        JOIN stops s ON st.stop_id = s.stop_id
        JOIN trips t ON st.trip_id = t.trip_id
        JOIN filtered_routes r ON t.route_id = r.route_id
        WHERE s.parent_station IS NOT NULL
        GROUP BY r.route_short_name, s.parent_station
        ),
    station_routes_with_regular_service AS (
        SELECT route_short_name, parent_station
        FROM trip_counts_by_station_route
        WHERE trip_count > 1
        ),
    stations_with_unique_regular_route AS (
        SELECT parent_station, MIN(route_short_name) AS route_short_name
        FROM station_routes_with_regular_service
        GROUP BY parent_station
        HAVING COUNT(DISTINCT route_short_name) = 1
        ),
    other_direction_trip_count AS (
        SELECT f.route_short_name, f.parent_station, f.direction_id,
                COUNT(DISTINCT t.trip_id) AS trip_count
        FROM final_terminals f
        JOIN filtered_routes r ON f.route_short_name = r.route_short_name
        JOIN trips t ON t.route_id = r.route_id AND t.direction_id != f.direction_id
        JOIN stop_times st ON st.trip_id = t.trip_id
        JOIN stops s ON st.stop_id = s.stop_id AND s.parent_station = f.parent_station
        GROUP BY f.route_short_name, f.parent_station, f.direction_id
        ),
    filtered_unique_terminals AS (
        SELECT s.parent_station, s.route_short_name
        FROM stations_with_unique_regular_route s
        LEFT JOIN other_direction_trip_count od ON s.route_short_name = od.route_short_name AND s.parent_station = od.parent_station
        WHERE od.trip_count IS NULL OR od.trip_count > 1
        ),
-- 6. Get one row per end station with the data needed to display in the page
endstations_pre AS (
    SELECT DISTINCT f.route_short_name, f.trip_headsign, f.parent_station, f.direction_id, f.route_type, f.route_color, f.route_text_color, f.stop_lat, f.stop_lon, f.shape_id
    FROM final_terminals f
    JOIN filtered_unique_terminals u ON f.parent_station = u.parent_station AND f.route_short_name = u.route_short_name
    ORDER BY f.route_short_name, f.direction_id
),
-- 7. Aggregate shapes to get one definitive shape per shape_id
aggregated_shapes AS (
    SELECT
        shape_id,
        GROUP_CONCAT(CONCAT("[", shape_pt_lat, ", ", shape_pt_lon, "]") ORDER BY shape_pt_sequence) as line
    FROM shapes
    GROUP BY shape_id
)
-- 8. JOIN shapes to results
SELECT endstations_pre.*, aggregated_shapes.line
FROM endstations_pre
JOIN aggregated_shapes ON aggregated_shapes.shape_id = endstations_pre.shape_id;