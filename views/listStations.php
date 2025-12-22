<?php
//$jsonFile = '../data/cache/rows.json';
//$rows = json_decode(file_get_contents($jsonFile), true) ?? [];
//print_r($data);

$rows = Station::loadData();

#TODO: replace with function and always read from the  db
if (empty($rows)) {
    echo "<p>No stations found.</p>";
} else {
    echo Station::generateTableHtml($rows);
}
    ?>
            </tbody>
        </table>
</body>
</html>