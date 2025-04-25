<?
$cant = count($viajes);
?>
<h5>
    <? foreach ($viajes as $key => $value) : ?>
        <i class="material-icons" style="vertical-align: middle; display: inline-block;"><?= $trip_type == 0 ? "flight_takeoff" : "flight_land" ?></i>
        <? $i_dest = implode(",", $value);
        echo $trip_type == 0 ? $key : $i_dest; ?>
        <i class="material-icons" style="vertical-align: middle; display: inline-block;">arrow_forward</i>
        <? echo ($trip_type == 0 ? $i_dest : $key) . (--$cant == 0 ? " (" . $total_vuelos . ")" : ""); ?>
        <br>
    <? endforeach; ?>
    <i class="material-icons" style="vertical-align: middle; display: inline-block;">schedule</i>
    <?= $fDesde . " - " . $fHasta ?>
</h5>