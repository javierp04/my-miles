<?
$ci = count($idas);
$cv = isset($vueltas) ? count($vueltas) : 0;

$iv = array();
$cant = array();
$tot = array();
$f_results = array();
$iv[0] = $idas;
$cant[0] = $ci;
$f_results[0] = $f_idas;
$tot[0] = $tot_idas;

if ($cv > 0) {
    $iv[1] = $vueltas;
    $cant[1] = $cv;
    $f_results[1] = $f_vueltas;
    $tot[1] = $tot_vueltas;
}
$desde_formated = date("d/m/Y", strtotime($fDesde));
$hasta_formated = date("d/m/Y", strtotime($fHasta));

for ($i = 0; $i < sizeof($iv); $i++) {
    $viajes = $iv[$i];
?>
    <?= $i == 0 ? '<a name="idas" id="idas"></a>' : '<a name="vueltas" id="vueltas"></a>'; ?>
    <div class="row">
        <div class="col-xs-9">
            <h5>
                <? foreach ($viajes as $key => $value) : ?>
                    <i class="material-icons" style="vertical-align: middle; display: inline-block;"><?= $i == 0 ? "flight_takeoff" : "flight_land" ?></i>
                    <? $i_dest = implode(",", $value);
                    echo $i == 0 ? $key : $i_dest; ?>
                    <i class="material-icons" style="vertical-align: middle; display: inline-block;">arrow_forward</i>
                    <? echo ($i == 0 ? $i_dest : $key) . (--$cant[$i] == 0 ? " (" . $tot[$i] . ")" : ""); ?>
                    <br>
                <? endforeach; ?>
                <i class="material-icons" style="vertical-align: middle; display: inline-block;">schedule</i>
                <?= $desde_formated . " - " . $hasta_formated ?>
            </h5>
        </div>
        <div class="col-xs-3 text-right">
            <? if ($i == 0 && count($iv) > 1) : ?>
                <a type="button" class="btn bg-blue waves-effect btn-sm m-t-20" href="#vueltas">
                    <i class="material-icons">flight_land</i><span>Vueltas</span>
                </a>
            <? elseif ($i == 1) : ?>
                <a type="button" class="btn bg-blue waves-effect btn-sm m-t-20" href="#idas">
                    <i class="material-icons">flight_takeoff</i><span>Idas</span>
                </a>
            <? else : ?>
                &nbsp;
            <? endif; ?>
        </div>
    </div>
    <div class="row clearfix">
        <div class="col-xs-12">
            <? if ($f_results[$i] != null) : ?>                
                <table class="table table-striped table-hover table-condensed table-bordered table-responsive m-t--25">
                    <thead>
                        <tr class="info">
                            <th class="text-center">Recorrido<br>Duracion</th>
                            <th class="text-center">Salida</th>
                            <th class="text-center">Llegada</th>
                            <th class="text-center">Aerolínea</th>
                            <th class="text-center">Escalas<br>#Vuelo</th>
                            <th class="text-center">Asientos</th>
                            <th class="text-center">Millas</th>
                            <th class="text-center">S&M</th>
                            <th class="text-center">Tasas</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">&nbsp;</th>
                        </tr>
                    </thead>
                    <tbody>
                        <? foreach ($f_results[$i] as $f) :
                            $price_sm = $f->SM * $mile_price + $f->Money;
                            $price_miles = $f->Millas * $mile_price;
                            $is_sm = $price_sm < $price_miles; ?>
                            <tr>
                                <td class="align-middle text-center">
                                    <?= $f->AirOrig . "-" . $f->AirDest . "<br>" . intdiv($f->Duracion, 60) . ":" . str_pad($f->Duracion % 60, 2, "0", STR_PAD_LEFT) ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?
                                    $salida = new DateTime($f->Salida);
                                    echo $salida->format('D d-M') . "<br>" . $salida->format('H:i');
                                    ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?
                                    $llegada = new DateTime($f->Llegada);
                                    echo $llegada->format('D d-M') . "<br>" . $llegada->format('H:i');
                                    ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= ucwords(strtolower($f->Aerolinea)) ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= $f->Escalas . "\n" . $f->Vuelos ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?
                                    $cabin = $f->Cabina == "PREMIUM_ECONOMIC" ? "PEC" : $f->Cabina;
                                    echo $cabin . "<br>" . $f->Asientos;
                                    ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= $f->Millas ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= $f->SM . " + $" . $f->Money ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= "$" . $f->Tasas ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?= (!$is_sm ? "<b>" : "") . "$" . ($f->Millas * $mile_price + $f->Tasas) . (!$is_sm ? "</b>" : "") . "<br>" ?>
                                    <?= ($is_sm ? "<b>" : "") ?>
                                    <span class="<?= $is_sm ? "col-purple" : ""?>">$<?= ($f->SM * $mile_price + $f->Money + $f->Tasas) . " (S&M)" ?></span>
                                    <?= ($is_sm ? "</b>" : "") ?>
                                </td>
                                <td class="align-middle text-center">
                                    <?
                                    $u_depdate = strtotime(substr($f->Salida, 0, 10) . "16:00") * 1000;
                                    $url = "https://www.smiles.com.ar/emission?originAirportCode={$f->AirOrig}&destinationAirportCode={$f->AirDest}&departureDate={$u_depdate}&adults=1&children=0&infants=0" .
                                        "&isFlexibleDateChecked=false&tripType=2&cabinType=all&currencyCode=BRL";
                                    ?>
                                    <a class="btn-xs btn bg-orange waves-effect btn-close-pos" href="<?= $url ?>" target="_blank"><i class="material-icons">visibility</i></a>
                                </td>
                            </tr>
                        <? endforeach ?>
                    </tbody>
                </table>
            <? else : ?>
                <h4>No se encontraron resultados todavía. . .</h4>
            <? endif ?>
        </div>
    </div>
<? } ?>