<?
$has_vueltas = !empty($vueltas) && isset($map_vueltas);

$d_for_title["fDesde"] = date("d/m/Y", strtotime($fDesde));
$d_for_title["fHasta"] = date("d/m/Y", strtotime($fHasta));
$d_for_title["viajes"] = $idas;
$d_for_title["total_vuelos"] = $tot_idas;
$d_for_title["trip_type"] = 0; //0 = IDA - 1 = VUELTA    
$idasInfo = $this->view('templates/flight_info_title', $d_for_title, true);
$idasInfo = str_replace(["\r", "\n", "\t"], '', $idasInfo);

if ($has_vueltas) {
    $d_for_title["viajes"] = $vueltas;
    $d_for_title["total_vuelos"] = $tot_vueltas;
    $d_for_title["trip_type"] = 1;
    $vueltasInfo = $this->view('templates/flight_info_title', $d_for_title, true);
    $vueltasInfo = str_replace(["\r", "\n", "\t"], '', $vueltasInfo);
}

?>
<style>
    .fc-center {
        margin-bottom: -20px;
        width: 80%;
    }

    .fc-left,
    .fc-right {
        margin-top: 25px;
    }
</style>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
        <div id="calFrom"></div>
    </div>
    <? if ($has_vueltas) : ?>
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-6">
            <div id="calTo"></div>
        </div>
    <? endif; ?>

    <div class="col-xs-12 col-md-6">
        <h5 id="h_ida_fecha">SELECCIONE LA IDA</h5>
        <? foreach ($map_idas as $f_ida => $vuelos) : ?>
            <div id="ida_<?= $f_ida ?>" class="row ida-data" style="display: none;">
                <div class="col-xs-12 text-center">
                    <table class="table table-striped table-hover table-condensed table-bordered table-responsive">
                        <thead style="font-weight: bold;">
                            <tr>
                                <td>&nbsp;</td>
                                <td>Tramo</td>
                                <td>Duración</td>
                                <td>Salida</td>
                                <td>Llegada</td>
                                <td>Aerolinea</td>
                                <td>Escalas</td>
                                <td>Millas</td>
                                <td>Total</td>
                                <? if ($this->config->item['is_free_view'] === 0) : ?>
                                    <td>Fav.</td>
                                <? endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <? foreach ($vuelos as $v) :
                                $price_sm = $v->SM * $mile_price + $v->Money;
                                $price_miles = $v->Millas * $mile_price;
                                $is_sm = $sm && ($price_sm < $price_miles); ?>
                                <tr>
                                    <td>
                                        <?
                                        if ($is_sm) {
                                            $percColor = "purple";
                                        } else {
                                            $percColor = "green";
                                            switch ($v->Percentil) {
                                                case 2:
                                                    $percColor = "#FFD700";
                                                    break;
                                                case 3:
                                                    $percColor = "orange";
                                                    break;
                                                case 4:
                                                    $percColor = "red";
                                                    break;
                                            }
                                        }
                                        ?>
                                        <i class="material-icons" style="vertical-align: middle; display: inline-block; color: <?= $percColor ?>">
                                            <?= $v->Cabina == "BUSINESS" ? "airline_seat_flat" : ($v->Cabina == "PREMIUM_ECONOMIC" ? "airline_seat_legroom_extra" : "airline_seat_recline_normal") ?>
                                        </i>
                                    </td>
                                    <td>
                                        <?
                                        $u_depdate = strtotime(substr($v->Salida, 0, 10) . "16:00") * 1000;
                                        $url = "https://www.smiles.com.ar/emission?originAirportCode={$v->AirOrig}&destinationAirportCode={$v->AirDest}&departureDate={$u_depdate}&adults=1&children=0&infants=0" .
                                            "&isFlexibleDateChecked=false&tripType=2&cabinType=all&currencyCode=BRL" ?>
                                        <a class="btn-xs btn bg-black waves-effect btn-close-pos" href="<?= $url ?>" target="_blank"><?= $v->AirOrig . "-" . $v->AirDest ?></a>
                                    </td>
                                    <td><?= intdiv($v->Duracion, 60) . ":" . str_pad($v->Duracion % 60, 2, "0", STR_PAD_LEFT) ?></td>
                                    <td><?= date("H:i", strtotime($v->Salida)) ?></td>
                                    <td>
                                        <?
                                        $fechaSalida = strtotime($v->Salida);
                                        $fechaLlegada = strtotime($v->Llegada);
                                        $diff = $fechaLlegada - $fechaSalida;
                                        $diferenciaEnDias = round($diff / (60 * 60 * 24));
                                        if ($fechaLlegada < $fechaSalida)
                                            $diferenciaEnDias++;
                                        if ($diferenciaEnDias > 0) {
                                            echo "+ {$diferenciaEnDias}d&nbsp;";
                                        }
                                        echo date("H:i", strtotime($v->Llegada)) ?>
                                    </td>
                                    <td><?= ucwords(strtolower($v->Aerolinea)) ?></td>
                                    <td><?= $v->Escalas ?></td>
                                    <td><?= $is_sm ? round($v->SM / 1000) : round($v->Millas / 1000) ?>k</td>
                                    <td>$<?= $is_sm ? round(($v->SM * $mile_price + $v->Tasas + $v->Money) / 1000) : round(($v->Millas * $mile_price + $v->Tasas) / 1000) ?>k</td>
                                    <? if ($this->config->item['is_free_view'] === 0) : ?>
                                        <td>
                                            <a href="javascript:" class="btn-fav" data-value="<?= $v->Id ?>">
                                                <i class="material-icons" id="fav_<?= $v->Id ?>">
                                                    <?= $v->Op_Id == null ? "star_border" : "star" ?>
                                                </i>
                                            </a>
                                        </td>
                                    <? endif; ?>
                                </tr>
                            <? endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <? endforeach ?>
    </div>
    <? if ($has_vueltas) : ?>
        <div class="col-xs-12 col-md-6">
            <h5 id="h_vta_fecha">SELECCIONE LA VUELTA</h5>
            <? foreach ($map_vueltas as $f_vta => $vuelos) : ?>
                <div id="vta_<?= $f_vta ?>" class="row vta-data" style="display: none;">
                    <div class="col-xs-12 text-center">
                        <table class="table table-striped table-hover table-condensed table-bordered table-responsive">
                            <thead style="font-weight: bold;">
                                <tr>
                                    <td>&nbsp;</td>
                                    <td>Tramo</td>
                                    <td>Duración</td>
                                    <td>Salida</td>
                                    <td>Llegada</td>
                                    <td>Aerolinea</td>
                                    <td>Escalas</td>
                                    <td>Millas</td>
                                    <td>Total</td>
                                    <? if ($this->config->item['is_free_view'] === 0) : ?>
                                        <td>Fav.</td>
                                    <? endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <? foreach ($vuelos as $v) :
                                    $price_sm = $v->SM * $mile_price + $v->Money;
                                    $price_miles = $v->Millas * $mile_price;
                                    $is_sm = $sm && ($price_sm < $price_miles); ?>
                                    <tr>
                                        <td>
                                            <?
                                            if ($is_sm) {
                                                $percColor = "purple";
                                            } else {
                                                $percColor = "green";
                                                switch ($v->Percentil) {
                                                    case 2:
                                                        $percColor = "#FFD700";
                                                        break;
                                                    case 3:
                                                        $percColor = "orange";
                                                        break;
                                                    case 4:
                                                        $percColor = "red";
                                                        break;
                                                }
                                            }
                                            ?>
                                            <i class="material-icons" style="vertical-align: middle; display: inline-block; color: <?= $percColor ?>">
                                                <?= $v->Cabina == "BUSINESS" ? "airline_seat_flat" : ($v->Cabina == "PREMIUM_ECONOMIC" ? "airline_seat_legroom_extra" : "airline_seat_recline_normal") ?>
                                            </i>
                                        </td>
                                        <td>
                                            <?
                                            $u_depdate = strtotime(substr($v->Salida, 0, 10) . "16:00") * 1000;
                                            $url = "https://www.smiles.com.ar/emission?originAirportCode={$v->AirOrig}&destinationAirportCode={$v->AirDest}&departureDate={$u_depdate}&adults=1&children=0&infants=0" .
                                                "&isFlexibleDateChecked=false&tripType=2&cabinType=all&currencyCode=BRL" ?>
                                            <a class="btn-xs btn bg-black waves-effect btn-close-pos" href="<?= $url ?>" target="_blank"><?= $v->AirOrig . "-" . $v->AirDest ?></a>
                                        </td>
                                        <td><?= intdiv($v->Duracion, 60) . ":" . str_pad($v->Duracion % 60, 2, "0", STR_PAD_LEFT) ?></td>
                                        <td><?= date("H:i", strtotime($v->Salida)) ?></td>
                                        <td>
                                            <?
                                            $fechaSalida = strtotime($v->Salida);
                                            $fechaLlegada = strtotime($v->Llegada);
                                            $diff = $fechaLlegada - $fechaSalida;
                                            $diferenciaEnDias = round($diff / (60 * 60 * 24));
                                            if ($fechaLlegada < $fechaSalida)
                                                $diferenciaEnDias++;
                                            if ($diferenciaEnDias > 0) {
                                                echo "+ {$diferenciaEnDias}d&nbsp;";
                                            }
                                            echo date("H:i", strtotime($v->Llegada)) ?>
                                        </td>
                                        <td><?= ucwords(strtolower($v->Aerolinea)) ?></td>
                                        <td><?= $v->Escalas ?></td>
                                        <td><?= $is_sm ? round($v->SM / 1000) : round($v->Millas / 1000) ?>k</td>
                                        <td>$<?= $is_sm ? round(($v->SM * $mile_price + $v->Tasas + $v->Money) / 1000) : round(($v->Millas * $mile_price + $v->Tasas) / 1000) ?>k</td>
                                        <? if ($this->config->item['is_free_view'] === 0) : ?>
                                            <td>
                                                <a href="javascript:" class="btn-fav" data-value="<?= $v->Id ?>">
                                                    <i class="material-icons" id="fav_<?= $v->Id ?>">
                                                        <?= $v->Op_Id == null ? "star_border" : "star" ?>
                                                    </i>
                                                </a>
                                            </td>
                                        <? endif; ?>
                                    </tr>
                                <? endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            <? endforeach ?>
        </div>
    <? endif; ?>
</div>

<script>
    // VARIABLES GLOBALES DE LA PAGINA: ida_cal_date y vta_cal_date
    // Almacenan el mes corriente. Si son null (primera vez), ida = fDesde, vta = max_date_vueltas (el ultimo dia buscado)
    // Si el usuario no clickeo nada se va poniendo el calendario de vuelta en el ultimo mes
    var prevIdaButtonClickHandler;
    var nextIdaButtonClickHandler;
    var prevVtaButtonClickHandler;
    var nextVtaButtonClickHandler;

    var currentIdaView;
    var currentVtaView;
    var cabina = '<?= $cab ?>';
    var fDesde = '<?= $fDesde ?>';
    var fHasta = '<?= $fHasta ?>';
    var fHastaCal = '<?= $fHastaCal ?>';
    var idas = <?= json_encode($hash_idas) ?>;
    var nf_idas = <?= json_encode($ida_no_flight) ?>;
    var ns_idas = <?= json_encode($ida_not_completed) ?>;
    var mile_price = '<?= $mile_price ?>';
    var show_sm = '<?= $sm ?>' == 1;

    const formatMes = (string) => string.charAt(0).toUpperCase() + string.slice(1);
        
    function seleccionaIda(f_sel) {

        $(".ida-data").hide();
        if ($("#ida_" + f_sel).length) {
            $("#ida_" + f_sel).show();
            $("#h_ida_fecha").html("IDA " + moment(f_sel).format('dddd DD [de] MMMM [de] YYYY'));
            if (selectedIdaDay != null) {
                var elem = $('#calFrom td[data-date="' + selectedIdaDay + '"]');
                elem.removeClass("fc-highlight");
                elem.removeClass("my-selected-day");
            }

            selectedIdaDay = f_sel;
            if (selectedIdaDay != null) {
                var elem = $('#calFrom td[data-date="' + selectedIdaDay + '"]');
                elem.addClass("my-selected-day");
            }
        } else {
            $("#h_ida_fecha").html("No hay vuelos para el dia seleccionado");
        }
    }

    function handleIdaButtonClick(direction) {
        ida_cal_date = currentIdaView.intervalStart.format('YYYY-MM-DD');
    }

    $(function() {
        $(".btn-fav").click(function() {
            var id = $(this).data('value');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                data: {
                    is_ajax: 1
                },
                url: '<?= base_url() . "smiles/ax_fav_flight/" ?>' + id,
                success: function(response) {
                    if (response.nosession) {
                        showNoSessionMsg();
                        return;
                    }
                    if (response.fav) {
                        $("#fav_" + id).html(response.fav);
                    }
                }
            });

        });

        $('#calFrom').fullCalendar({
            header: {
                left: 'prev',
                center: 'title',
                right: 'next'
            },
            eventAfterAllRender: function(view) {
                // Almacenar la vista actual
                currentIdaView = view;

                // Desvincular el manejador de clic existente si hay alguno
                if (prevIdaButtonClickHandler) {
                    $('#calFrom .fc-prev-button').off('click', prevIdaButtonClickHandler);
                }
                if (nextIdaButtonClickHandler) {
                    $('#calFrom .fc-next-button').off('click', nextIdaButtonClickHandler);
                }

                // Vincular nuevos manejadores de clic a los botones prev y next
                prevIdaButtonClickHandler = function() {
                    handleIdaButtonClick('prev');
                };
                nextIdaButtonClickHandler = function() {
                    handleIdaButtonClick('next');
                };
                $('#calFrom .fc-prev-button').on('click', prevIdaButtonClickHandler);
                $('#calFrom .fc-next-button').on('click', nextIdaButtonClickHandler);

            },
            viewRender: function(view, element) {
                // Personaliza el contenido del centro del encabezado          
                var titleContainer = $('#calFrom .fc-center');
                titleContainer.empty(); // Limpiamos el contenido actual            
                tithtml = '<table style="width: 100%">' +
                    '<tr>' +
                    '<td><?= $idasInfo ?></td>' +
                    '<td style="vertical-align: middle"><h3>' + formatMes(view.title) + '</h3></td>' +
                    '</tr>' +
                    '</table>';
                titleContainer.append(tithtml);
                var calFrom = $('#calFrom').fullCalendar('getDate');

                <? if ($has_vueltas) : ?>
                    var calTo = $('#calTo').fullCalendar('getDate');

                    if (calFrom.isSame(calTo, 'month')) {
                        $('#calFrom .fc-next-button').css('visibility', 'hidden');
                        $('#calTo .fc-prev-button').css('visibility', 'hidden');
                    } else {
                        $('#calFrom .fc-next-button').css('visibility', 'visible');
                        $('#calTo .fc-prev-button').css('visibility', 'visible');
                    }
                <? else : ?>
                    if (view.intervalStart.isSameOrAfter(fHasta, 'month')) {
                        $('#calFrom .fc-next-button').css('visibility', 'hidden');
                    } else {
                        $('#calFrom .fc-next-button').css('visibility', 'visible');
                    }

                <? endif; ?>

                if (view.intervalStart.isSameOrBefore(fDesde, 'month')) {
                    $('#calFrom .fc-prev-button').css('visibility', 'hidden');
                } else {
                    $('#calFrom .fc-prev-button').css('visibility', 'visible');
                }
            },
            selectAllow: function(selectInfo) {
                var today = moment().startOf('day');
                var selectedDate = selectInfo.start.format('YYYY-MM-DD');
                //return selectInfo.start.isAfter(today) && (!nf_idas.includes(selectedDate) || ns_idas[selectedDate] >= 0) && selectedDate <= fHasta;
                return selectInfo.start.isAfter(today) && selectedDate <= fHasta && ns_idas[selectedDate] > 0;
            },
            dayClick: function(date, jsEvent, view) {
                var today = moment().startOf('day');
                if (date.isSameOrAfter(today)) {
                    var f_sel = date.format('YYYY-MM-DD');
                    if (is_search && ns_idas[f_sel] < t_idas.length) {
                        if (!finished) {
                            showSearchingMsg();
                        } else {
                            //searchDialogRange(f_sel, t_idas, '<?= $idasInfo ?>');
                        }

                    } else if (!nf_idas.includes(f_sel) && f_sel <= fHasta) {
                        seleccionaIda(f_sel);
                        //chequeo de seleccionado de vuelta
                        if (selectedVtaDay != null && selectedVtaDay < selectedIdaDay) {
                            seleccionaVta(null);
                        }
                    }
                }
                return false;
            },
            selectable: true, // Habilitar la selección de días
            selectHelper: true,
            dayRender: function(date, cell) {
                appendCell(date, cell, idas);
                var today = moment().startOf('day');
                var fechaActual = date.format('YYYY-MM-DD');
                if (date.isSame(selectedIdaDay, 'day')) {
                    cell.addClass('my-selected-day');
                }
                if (date.isSameOrBefore(today, 'day')) {
                    cell.addClass('my-past-day');
                } else if (fechaActual < fHasta) {
                    if (ns_idas[fechaActual] === undefined || ns_idas[fechaActual] == 0) {
                        cell.addClass('my-ns-day');
                    } else if (ns_idas[fechaActual] < t_idas.length) {
                        cell.addClass('my-partial-day');
                    } else if (nf_idas.includes(fechaActual)) {
                        cell.addClass('my-disabled-day');
                    }
                }
            },
        });

        if (ida_cal_date == null) {
            ida_cal_date = fDesde;
        }
        $('#calFrom').fullCalendar('gotoDate', ida_cal_date);

        if (selectedIdaDay != null) {
            seleccionaIda(selectedIdaDay);
        }

        <? if ($has_vueltas) : ?>
            var vueltas = JSON.parse('<?= json_encode($hash_vueltas) ?>');
            var nf_vtas = JSON.parse('<?= json_encode($vta_no_flight) ?>');
            var ns_vtas = JSON.parse('<?= json_encode($vta_not_completed) ?>');

            function seleccionaVta(f_sel) {
                $(".vta-data").hide();
                if (selectedVtaDay != null) {
                    var elem = $('#calTo td[data-date="' + selectedVtaDay + '"]');
                    elem.removeClass("fc-highlight");
                    elem.removeClass("my-selected-day");
                }
                if (f_sel == null) {
                    $("#h_vta_fecha").html("SELECCIONE VUELTA");
                    selectedVtaDay = null;
                    return;
                }
                if ($("#vta_" + f_sel).length) {
                    $("#vta_" + f_sel).show();
                    $("#h_vta_fecha").html("VUELTA " + moment(f_sel).format('dddd DD [de] MMMM [de] YYYY'));

                    selectedVtaDay = f_sel;
                    if (selectedVtaDay != null) {
                        var elem = $('#calTo td[data-date="' + selectedVtaDay + '"]');
                        elem.addClass("my-selected-day");
                    }
                } else {
                    $("#h_vta_fecha").html("No hay vuelos para el dia seleccionado");
                }

            }

            function handleVtaButtonClick(direction) {
                vta_cal_date = currentVtaView.intervalStart.format('YYYY-MM-DD');
            }

            $('#calTo').fullCalendar({
                header: {
                    left: 'prev',
                    center: 'title',
                    right: 'next'
                },
                viewRender: function(view, element) {

                    var titleContainer = $('#calTo .fc-center');
                    titleContainer.empty(); // Limpiamos el contenido actual            
                    tithtml = '<table style="width: 100%">' +
                        '<tr>' +
                        '<td><?= $vueltasInfo ?></td>' +
                        '<td style="vertical-align: middle"><h3>' + formatMes(view.title) + '</h3></td>' +
                        '</tr>' +
                        '</table>';
                    titleContainer.append(tithtml);
                    var calFrom = $('#calFrom').fullCalendar('getDate');
                    var calTo = $('#calTo').fullCalendar('getDate');

                    if (calFrom.isSame(calTo, 'month')) {
                        $('#calFrom .fc-next-button').css('visibility', 'hidden');
                        $('#calTo .fc-prev-button').css('visibility', 'hidden');
                    } else {
                        $('#calFrom .fc-next-button').css('visibility', 'visible');
                        $('#calTo .fc-prev-button').css('visibility', 'visible');
                    }

                    if (view.intervalStart.isSameOrAfter(fHasta, 'month')) {
                        $('#calTo .fc-next-button').css('visibility', 'hidden');
                    } else {
                        $('#calTo .fc-next-button').css('visibility', 'visible');
                    }
                },
                eventAfterAllRender: function(view) {
                    // Almacenar la vista actual
                    currentVtaView = view;

                    // Desvincular el manejador de clic existente si hay alguno
                    if (prevVtaButtonClickHandler) {
                        $('#calTo .fc-prev-button').off('click', prevVtaButtonClickHandler);
                    }
                    if (nextVtaButtonClickHandler) {
                        $('#calTo .fc-next-button').off('click', nextVtaButtonClickHandler);
                    }

                    // Vincular nuevos manejadores de clic a los botones prev y next
                    prevVtaButtonClickHandler = function() {
                        handleVtaButtonClick('prev');
                    };
                    nextVtaButtonClickHandler = function() {
                        handleVtaButtonClick('next');
                    };
                    $('#calTo .fc-prev-button').on('click', prevVtaButtonClickHandler);
                    $('#calTo .fc-next-button').on('click', nextVtaButtonClickHandler);
                },
                selectAllow: function(selectInfo) {
                    var today = moment().startOf('day');
                    var selectedDate = selectInfo.start.format('YYYY-MM-DD');
                    return selectInfo.start.isAfter(today) && selectedDate <= fHasta && ns_vtas[selectedDate] > 0;
                    //return selectInfo.start.isAfter(today) && selectedDate <= fHasta && !nf_vtas.includes(selectedDate) && ns_vtas[selectedDate] >= 0 ;
                },
                dayClick: function(date, jsEvent, view) {
                    var fechaActual = date.format('YYYY-MM-DD');
                    var today = moment().startOf('day');
                    var f_sel = date.format('YYYY-MM-DD');
                    if (date.isSameOrAfter(today)) {
                        var f_sel = date.format('YYYY-MM-DD');
                        if (is_search && ns_vtas[f_sel] < t_vueltas.length) {
                            if (!finished) {
                                showSearchingMsg();
                            } else {
                                //searchDialogRange(f_sel, t_vueltas, '<?= $vueltasInfo ?>');
                            }

                        } else if (!nf_vtas.includes(f_sel) && f_sel <= fHasta) {
                            //if (selectedIdaDay != null && date.isAfter(selectedIdaDay)) {
                            if (selectedIdaDay == null || date.isAfter(selectedIdaDay)) {
                                seleccionaVta(f_sel);
                            } else {
                                msg = selectedIdaDay != null ? "La fecha de VUELTA debe ser mayor a la de IDA." : "Priemro debe seleccionar la IDA ";
                                swal({
                                    title: "Error selección",
                                    text: msg,
                                    icon: "warning"
                                });
                            }
                        }
                    }
                    return false;

                },
                selectable: true, // Habilitar la selección de días
                selectHelper: true,
                dayRender: function(date, cell) {
                    appendCell(date, cell, vueltas);
                    var today = moment().startOf('day');
                    var fechaActual = date.format('YYYY-MM-DD'); // Obtener la fecha actual en formato YYYY-MM-DD
                    if (date.isSame(selectedVtaDay, 'day')) {
                        cell.addClass('my-selected-day');
                    }
                    if (date.isSameOrBefore(today, 'day')) {
                        cell.addClass('my-past-day');
                    } else if (fechaActual < fHasta) {
                        if (ns_vtas[fechaActual] === undefined || ns_vtas[fechaActual] == 0) {
                            cell.addClass('my-ns-day');
                        } else if (ns_vtas[fechaActual] < t_vueltas.length) {
                            cell.addClass('my-partial-day');
                        } else if (nf_vtas.includes(fechaActual)) {
                            cell.addClass('my-disabled-day');
                        }
                    }

                }
            });

            if (vta_cal_date == null) {
                vta_cal_date = fHastaCal;
                //vta_cal_date = fHasta;
            }

            $('#calTo').fullCalendar('gotoDate', vta_cal_date);
            if (selectedVtaDay != null) {
                seleccionaVta(selectedVtaDay);
            }
        <? endif; ?>
    });

    function showSearchingMsg() {
        swal({
            title: "Búsqueda en progreso",
            text: "Hay una búsqueda en progreso. Una vez finalizada podrá buscar los rangos faltantes pintados en rosa.",
            icon: "info"
        });

    }

    function appendCell(date, cell, tram) {
        var fecha = date.format('YYYY-MM-DD');
        if (tram[fecha]) {
            texCol = "blue";
            cellHtml = '<div class="text-container">';
            cellHtml += '<p>';
            tram[fecha].forEach(function(dato) {
                is_sm = false;
                p_sm = dato.SM * mile_price + parseInt(dato.Money);
                if (show_sm && p_sm < dato.Millas * mile_price) {
                    millas = Math.round(dato.SM / 1000) + 'k';
                    tasas = '$' + Math.round((parseInt(dato.Tasas) + parseInt(dato.Money)) / 1000) + 'k';
                    is_sm = true;
                } else {
                    millas = Math.round(dato.Millas / 1000) + 'k';
                    tasas = '$' + Math.round(dato.Tasas / 1000) + 'k';
                }
                total = '$' + Math.round(dato.Total / 1000) + 'k';
                icon = dato.Cabina == "BUSINESS" ? "airline_seat_flat" : dato.Cabina == "PREMIUM_ECONOMIC" ? "airline_seat_legroom_extra" : "airline_seat_recline_normal";
                if (is_sm) {
                    percColor = "purple";
                } else {
                    switch (dato.Percentil) {
                        case 2:
                            percColor = "#FFD700";
                            break;
                        case 3:
                            percColor = "orange";
                            break;
                        case 4:
                            percColor = "red";
                            break;
                        default:
                            percColor = "green";
                    }
                }
                dur = Math.floor(dato.Duracion / 60) + ':' + (dato.Duracion % 60).toString().padStart(2, '0');
                if (!cabina) {
                    cellHtml += '<span style="color: ' + percColor + ';">';
                    cellHtml += '<i class="material-icons" style="vertical-align: middle; display: inline-block;">' + icon + '</i>';
                } else {
                    cellHtml += '<span style="font-size: 22px; color: ' + percColor + ';">';
                    cellHtml += '&#x25CF;';
                }
                cellHtml += '</span>';
                cellHtml += '<span style="cursor: pointer; color: ' + texCol + ';" title="' + dato.Orig + '-' + dato.Dest + " " + dato.AirCode + ' (' + dur + ')">';
                cellHtml += currPriceType == 1 ? total : millas + '+' + tasas;
                cellHtml += '</span><br>';
            });
            cellHtml += '</p>';
            cellHtml += '</div>';
            cell.append(cellHtml);
        }
    }
</script>