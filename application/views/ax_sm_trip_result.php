<? if (isset($trips) && count($trips) > 0) : ?>
    <table class="table table-striped table-hover table-condensed table-bordered">
        <thead>
            <tr class="info">            
                <th class="text-center">Recorrido</th>
                <th class="text-center">Duracion</th>
                <th class="text-center">Salida</th>
                <!--<th class="text-center">Llegada</th>-->
                <th class="text-center">Aerolínea</th>          
                <th class="text-center">Escalas</th>
                <th class="text-center">Cabina</th>
                <th class="text-center">Millas</th>
                <th class="text-center">Dinero</th>
                <th class="text-center">Estadia</th>            
                <th class="text-center">Total</th>
                <th class="text-center">&nbsp;</th>
            </tr>
        </thead>
        <tbody>        
            <? foreach ($trips as $t) : ?>
                <tr>                
                    <td class="align-middle text-center">
                        <?= $t->ida->AirOrig . "-" . $t->ida->AirDest ?>
                        <br>
                        <?= $t->vuelta->AirOrig . "-" . $t->vuelta->AirDest?>
                    </td>
                    <td class="align-middle text-center">
                        <?= intdiv($t->ida->Duracion, 60) . ":" . str_pad($t->ida->Duracion % 60, 2, "0", STR_PAD_LEFT) ?>
                        <br>
                        <?= intdiv($t->vuelta->Duracion, 60) . ":" . str_pad($t->vuelta->Duracion % 60, 2, "0", STR_PAD_LEFT) ?>
                    </td>
                    <td class="align-middle text-center">
                        <? 
                            $salida = new DateTime($t->ida->Salida); 
                            echo $salida->format('D d-M') . " " . $salida->format('H:i');
                            echo "<br>";

                            $salida = new DateTime($t->vuelta->Salida); 
                            echo $salida->format('D d-M') . " " . $salida->format('H:i');
                        ?>
                    </td>
                    <!--
                    <td class="align-middle text-center">
                        <? 
                            $llegada = new DateTime($t->ida->Llegada); 
                            echo $llegada->format('D d-M') . " " . $llegada->format('H:i');
                            echo "<br>";

                            $llegada = new DateTime($t->vuelta->Llegada); 
                            echo $llegada->format('D d-M') . " " . $llegada->format('H:i');
                        ?>
                    </td> -->
                    <td class="align-middle text-center">
                        <?=$t->ida->Aerolinea ?>
                        <br>
                        <?=$t->vuelta->Aerolinea ?>
                    </td>                
                    <td class="align-middle text-center">
                        <?=$t->ida->Escalas ?>
                        <br>
                        <?=$t->vuelta->Escalas ?>
                    </td>
                    <td class="align-middle text-center">
                        <?
                            
                            $cabin = $t->ida->Cabina == "PREMIUM_ECONOMIC" ? "PEC" : $t->ida->Cabina;
                            echo $cabin . " (" . $t->ida->Asientos . ")";
                            echo "<br>";
                            $cabin = $t->vuelta->Cabina == "PREMIUM_ECONOMIC" ? "PEC" : $t->vuelta->Cabina;
                            echo $cabin . " (" . $t->vuelta->Asientos . ")";
                        ?>               
                    </td>
                    <td class="align-middle text-center">
                        <span class="<?= $t->ida->Type != "MILES" ? "col-purple" : "" ?>"><?=$t->millas_ida ?></span>
                        <br>
                        <span class="<?= $t->vuelta->Type != "MILES" ? "col-purple" : "" ?>"><?=$t->millas_vta ?></span>
                    </td>
                    <td class="align-middle text-center">
                        <?= "$" . $t->dinero_ida ?>
                        <br>
                        <?= "$" . $t->dinero_vta ?>
                    </td>
                    <td class="align-middle text-center">
                        <?= $t->estadia ." días" ?>
                    </td>
                    <td class="align-middle text-center">
                        <?= "$" . $t->precio_tot ?>
                    </td>
                    <td class="align-middle text-center">
                        <?                        
                            $u_depdate = strtotime(substr($t->ida->Salida,0,10) . "16:00") * 1000;
                            $u_depdate2 = strtotime(substr($t->vuelta->Salida,0,10) . "16:00") * 1000;                        
                            $url = "https://www.smiles.com.ar/emission?originAirportCode={$t->ida->AirOrig}&destinationAirportCode={$t->ida->AirDest}&departureDate={$u_depdate}&adults=1&children=0&infants=0" .
                                    "&isFlexibleDateChecked=false&tripType=3&cabinType=all&currencyCode=BRL&segments=2&departureDate2={$u_depdate2}&originAirportCode2={$t->vuelta->AirOrig}" . 
                                    "&destinationAirportCode2={$t->vuelta->AirDest}";
                        ?>
                        <a class="btn-xs btn bg-orange waves-effect btn-close-pos" href="<?=$url?>" target="_blank"><i class="material-icons">visibility</i></a>                    
                    </td>
            </tr>                                 
            <? endforeach ?>        
        </tbody>
    </table>
<? else : ?>
    <h5 class="alert alert-danger">No hay viajes con los criterios seleccionados. Pruebe modificando los filtros.</h5>
<? endif; ?>