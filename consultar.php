<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'vendor/autoload.php';
use GeoIp2\Database\Reader;

// This creates the Reader object, which should be reused across
// lookups.
$reader = new Reader(__DIR__ . '/GeoLite2-City.mmdb');

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css" integrity="sha384-GJzZqFGwb1QTTN6wy59ffF1BuGJpLSa9DkKMp0DgiMDm4iYMj70gZWKYbI706tWS" crossorigin="anonymous">
    <title>GEO IP Tool</title>
</head>
<body>
<h1>GEO IP</h1>
<h2>Resultado:</h2>
<?php
$ips = isset($_POST['ips']) ? trim($_POST['ips']) : null;
$lista = [];
if (!empty($ips)) {
    $ips = explode("\n", $ips);
    if (is_array($ips) && count($ips) > 0) {
        $resultado = [];
        foreach ($ips as $ip) {
            $ip = trim($ip);
            if (!empty($ip)) {

                $record = $reader->city($ip);
                $resultado[$record->country->isoCode][$record->mostSpecificSubdivision->isoCode][$record->city->name][] = $ip;
                $lista[$record->city->name] = "$ip {$record->country->isoCode}{$record->mostSpecificSubdivision->isoCode}{$record->city->name}";

            }
        }

        ksort($lista);

        echo "<ul>";
        foreach ($resultado as $country => $states) {
            echo "<li>$country: " . count($states) . " estados";
            echo "<ul>";
            $tt_country = 0;
            foreach ($states as $state => $cities) {
                $tt_state = 0;
                echo "<li>$state: " . count($cities) . " cidades <ul>";
                foreach($cities as $city => $ips) {
                    echo "<li>$city: " . count($ips) . "</li>";
                    $tt_state += count($ips);
                    $tt_country += count($ips);

                }
                echo "<li><strong>Total: $tt_state</strong></li>";
                echo "</ul></li>";

            }
            echo "<li><strong>Total: $tt_country</strong></li>";
            echo "</ul></li>";
        }
        echo "</ul>";

        echo "<h2>Geral</h2>";
        echo "<pre>".implode(PHP_EOL,$lista)."</pre>";
        
    } else {
        echo '<div class="alert alert-danger" role="alert">Informe ao menos um ip!</div>';
    }
} else {
    echo '<div class="alert alert-danger" role="alert">Lista vazia!</div>';
}
?>
<p>
    <a href="index.php">Voltar</a>
</p>
<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js" integrity="sha384-wHAiFfRlMFy6i5SRaxvfOCifBUQy1xHdJ/yoi7FRNXMRBu5WHdZYu1hA6ZOblgut" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js" integrity="sha384-B0UglyR+jN6CkvvICOB2joaf5I4l3gm9GU6Hc1og6Ls7i6U/mkkaduKaBhlAXv9k" crossorigin="anonymous"></script>
<style>

    body {
        margin: 20px;

    }
</style>
</body>
</html>