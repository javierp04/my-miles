<?
$desde_str = date("d/m/Y", strtotime($fDesde));
$hasta_str = date("d/m/Y", strtotime($fHasta));

?>
<div class="row clearfix">
    <div class="col-xs-12">
        <h5 class="alert bg-blue-grey">
            <i class="material-icons" style="vertical-align: middle; display: inline-block;">hotel</i>
            &nbsp;&nbsp;
            Seleccioná el rango de fechas de la estadía, cantidad mínima y máxima de días y el total de dinero disponible
        </h5>
    </div>
</div>
<div class="row">
    <div class="col-xs-6 col-md-3">
        <h5 class="m-t--15">
            <?
            $trip_type = 0;
            foreach ($idas as $key => $value) : ?>
                <i class="material-icons" style="vertical-align: middle; display: inline-block;"><?= $trip_type == 0 ? "flight_takeoff" : "flight_land" ?></i>
                <? $i_dest = implode(",", $value);
                echo $trip_type == 0 ? $key : $i_dest; ?>
                <i class="material-icons" style="vertical-align: middle; display: inline-block;">arrow_forward</i>
                <? echo ($trip_type == 0 ? $i_dest : $key) ?>
                <br>
            <? endforeach; ?>
            <?
            $trip_type = 1;
            foreach ($vueltas as $key => $value) : ?>
                <i class="material-icons" style="vertical-align: middle; display: inline-block;"><?= $trip_type == 0 ? "flight_takeoff" : "flight_land" ?></i>
                <? $i_dest = implode(",", $value);
                echo $trip_type == 0 ? $key : $i_dest; ?>
                <i class="material-icons" style="vertical-align: middle; display: inline-block;">arrow_forward</i>
                <? echo ($trip_type == 0 ? $i_dest : $key) ?>
                <br>
            <? endforeach; ?>
            <i class="material-icons" style="vertical-align: middle; display: inline-block;">schedule</i>
            <?= $desde_str . " - " . $hasta_str ?>
        </h5>
        <input type="checkbox" name="chkTSM" id="chkTSM" class="filled-in form-control" checked />
        <label for="chkTSM" id="lblTSM"><b>Mostrar Smiles & Money</b></label>
    </div>
    <div class="col-xs-6 col-md-9">
        <div class="row clearfix">
            <div class="col-xs-6 col-sm-6 col-md-2">
                <b>Desde</b>
                <input type="text" class="form-control filter-ctl" id="estDesde" name="estDesde" placeholder="Fecha Desde...">
            </div>
            <div class="col-xs-6 col-sm-6 col-md-2">
                <b>Hasta</b>
                <input type="text" class="form-control filter-ctl" id="estHasta" name="estHasta" placeholder="Fecha Hasta...">
            </div>
            <div class="col-xs-6 col-sm-6 col-md-2">
                <b>Días Minimo</b>
                <input type="number" class="form-control" id="txtEMin" name="txtEMin" value="5" pattern="[0-9]*">
            </div>
            <div class="col-xs-6 col-sm-6 col-md-2">
                <b>Días Máximo</b>
                <input type="number" class="form-control" id="txtEMax" name="txtEMax" value="10" pattern="[0-9]*">
            </div>
            <div class="col-xs-8 col-sm-8 col-md-2">
                <b>Precio Total</b>
                <input type="number" class="form-control" id="txtTTotal" name="txtTTotal" value="" pattern="[0-9]*">
            </div>
            <div class="col-xs-4 col-sm-4 col-md-2">
                <button id="btnStay" type="button" class="btn bg-green waves-effect btn-sm m-t-15">
                    <i class="material-icons">done</i>
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-xs-12">
        <div class="preloader-generic text-center" id="loadingStay" style="display: none;">
            <div class="preloader pl-size-xs">
                <div class="spinner-layer pl-indigo">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
        </div>
        <div id="tabStay">

        </div>
    </div>
</div>

<script>
    var trip_fdesde = '<?= $fDesde ?>';
    var trip_fhasta = '<?= $fHasta ?>';
    var format_desde = moment(trip_fdesde).format('DD/MM/YYYY');
    var format_hasta = moment(trip_fhasta).format('DD/MM/YYYY');
    var eDesdeInput = $("#estDesde");
    var eHastaInput = $("#estHasta");
    var res_display = 0;

    $(function() {

        eDesdeInput.datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            startDate: format_desde, // Solo seleccionable a partir de mañana
            endDate: format_hasta,
        }).on('changeDate', function(selected) {
            var desdeSeleccionado = new Date(selected.date);
            var inicioHasta = new Date(desdeSeleccionado);
            inicioHasta.setDate(inicioHasta.getDate() + 30);
            var fechaFormatHasta = moment(format_hasta, 'DD/MM/YYYY').toDate(); // Convertir format_hasta a Date
            inicioHasta = inicioHasta > fechaFormatHasta ? fechaFormatHasta : inicioHasta;
            eHastaInput.datepicker('setDate', inicioHasta);
        });

        eHastaInput.datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy',
            startDate: format_desde,
            endDate: format_hasta,
        });

        eDesdeInput.datepicker('setDate', format_desde);
        eHastaInput.datepicker('setDate', format_hasta);

        $("#btnStay").click(function() {
            calculateStay();

        });

        $("#chkTSM").change(function() {
            if (res_display) {
                calculateStay();
            }
        });
    })

    function show_loading_stay(value) {
        if (value) {
            $("#loadingStay").show();
        } else {
            $("#loadingStay").hide();
        }
    }

    function calculateStay() {
        var fdesde = moment(eDesdeInput.val(), 'DD/MM/YYYY');
        var fhasta = moment(eHastaInput.val(), 'DD/MM/YYYY');

        // Formatear las fechas en formato 'YYYY-MM-DD'
        var formatted_desde = fdesde.format('YYYY-MM-DD');
        var formatted_hasta = fhasta.format('YYYY-MM-DD');

        var emin = $("#txtEMin").val();
        var emax = $("#txtEMax").val();
        var ttot = $("#txtTTotal").val();
        var dur = $("#txtDur").val();
        var esc = $("#txtEsc").val();
        var air = $("#filAirline").val();
        var asi = $("#txtAsi").val();
        var tot = $("#txtTot").val();
        var cab = $("#filCabina").val();
        var sm = $("#chkTSM").prop("checked") ? 1 : 0;

        var mile_price = $("#txtMP").val();
        if (!emin || !emax) {
            swal({
                title: "Error selección",
                text: "Debe seleccionar estadía mínima y máxima.",
                icon: "error"
            });
            return;
        }
        show_loading_stay(true);
        var data = {
            emin: emin,
            emax: emax,
            ttot: ttot,
            idas: t_idas,
            vueltas: t_vueltas,
            duracion: dur,
            escalas: esc,
            asientos: asi,
            cabina: cab,
            airline: air,
            fdesde: formatted_desde,
            fhasta: formatted_hasta,
            mile_price: mile_price,
            sm : sm,
            is_ajax: 1
        };

        $.ajax({
            type: 'POST',
            data: data,
            dataType: 'json',
            url: '<?= base_url() . "common/ax_trip_result" ?>',
            success: function(response) {
                if (response.nosession) {
                    showNoSessionMsg();
                    return;
                }
                $("#tabStay").html(response.html);
                show_loading_stay(false);
                res_display = 1;
            }
        });
    }
</script>