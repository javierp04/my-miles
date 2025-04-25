<? 
    foreach ($favs as $t => $vs) : 
?>
    <h5><?= $t ?></h5>
    <table class="table table-hover table-condensed table-bordered table-responsive">
        <thead>
            <tr class="info">
                <th class="text-center">Validez</th>
                <th class="text-center">Duracion</th>
                <th class="text-center">Salida</th>
                <th class="text-center">Llegada</th>
                <th class="text-center">Aerolínea</th>
                <th class="text-center">Escalas</th>
                <th class="text-center">Asientos</th>
                <th class="text-center">Millas</th>
                <th class="text-center">S&M</th>
                <th class="text-center">Tasas</th>
                <th class="text-center">Total</th>
                <th class="text-center">&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <? foreach ($vs as $f) : 
                $validez = $int_hours - $f->Age;            
                $tr_class = $validez < 0 ? "col-grey" : ""; ?>
                <tr class="<?=$tr_class?>">
                    <td class="align-middle text-center">
                        <? if ($validez > 0) {
                            echo $validez . " hs";
                        } else {
                            echo '<i class="material-icons col-' . ($validez - $int_hours ? 'red' : 'amber') . '">warning</i>';
                        }
                         ?>
                    </td>
                    <td class="align-middle text-center">
                        <?= intdiv($f->Duracion, 60) . ":" . str_pad($f->Duracion % 60, 2, "0", STR_PAD_LEFT) ?>
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
                        <?= "$" . ($f->Millas * $mile_price + $f->Tasas) . "<br>$" . ($f->SM * $mile_price + $f->Money + $f->Tasas) . " (S&M)" ?>
                    </td>
                    <td class="align-middle text-center">
                        <?
                        $u_depdate = strtotime(substr($f->Salida, 0, 10) . "16:00") * 1000;
                        $url = "https://www.smiles.com.ar/emission?originAirportCode={$f->AirOrig}&destinationAirportCode={$f->AirDest}&departureDate={$u_depdate}&adults=1&children=0&infants=0" .
                            "&isFlexibleDateChecked=false&tripType=2&cabinType=all&currencyCode=BRL";
                        ?>
                    
                        <button type="button" class="btn bg-red waves-effect btn-xs btn-delete" data-value="<?= $f->Id ?>">
                            <i class="material-icons">delete</i>
                        </button>
                        &nbsp;
                        <a class="btn-xs btn bg-orange waves-effect btn-close-pos" href="<?= $url ?>" target="_blank"><i class="material-icons">visibility</i></a>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>
    <br>
<? endforeach ?>