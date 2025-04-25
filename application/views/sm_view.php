<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <title>MY MILES - Administra tus búsquedas Smiles</title>
    <!-- Favicon-->
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">

    <!-- Bootstrap Core Css -->
    <link href="<?= base_url() ?>plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

    <!-- Bootstrap Select Css -->
    <link href="<?= base_url() ?>css/style.css" rel="stylesheet">
    <link href="<?= base_url() ?>plugins/node-waves/waves.css" rel="stylesheet" />

    <link href="<?= base_url() ?>plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet" />
    <link href="<?= base_url() ?>plugins/animate-css/animate.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet">
    <link href="<?= base_url() ?>css/themes/all-themes.css" rel="stylesheet" />

    <!-- Jquery Core Js -->
    <script src="<?= base_url() ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap Core Js -->
    <script src="<?= base_url() ?>plugins/bootstrap/js/bootstrap.js"></script>


    <script src="<?= base_url() ?>plugins/node-waves/waves.js"></script>
    <script src="<?= base_url() ?>plugins/sweetalert/sweetalert.min.js"></script>

    <!-- Slimscroll Plugin Js -->
    <script src="<?= base_url() ?>plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <script src="<?= base_url() ?>plugins/momentjs/moment.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/es.js"></script>
    <script src="<?= base_url() ?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>

    <style>
        /* Estilos CSS para alinear el texto en la esquina inferior derecha */
        .text-container {
            position: relative;
            height: 98%;
            /* Ajusta según sea necesario */
            width: 100%;
            /* Ajusta según sea necesario */
        }

        .text-container p {
            position: absolute;
            bottom: 0;
            right: 0;
            margin: 0;
            padding: 2px;
            font-size: 0.8em;
        }

        .my-disabled-day {
            background-color: #EEEEEE;            
        }

        .my-partial-day {
            background-color: #F8F8F8;            
        }

        .my-past-day {
            background-color: #D5D5D5;            
        }

        .my-ns-day {
            background-color: #FFF5F4;            
        }


        .my-selected-day {
            background-color: #E9F8FA;
        }

        .fc-today {
            background-color: transparent !important;
        }

        .content {
            margin-top: 90px;
            /* Altura de la barra superior */
        }
    </style>

</head>

<body class="theme-orange">
    <!-- Page Loader -->
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <!-- #END# Page Loader -->

    <? $this->view('templates/topbar'); ?>


    <div class="container-fluid content">

        <div class="row clearfix">
            <div class="col-xs-12">
                <? if ($is_mobile) : ?>
                    <h5 class="alert alert-danger"><i class="material-icons" style="vertical-align: middle; display: inline-block;">warning</i>&nbsp;<span>La página esta diseñada para ver en computadora o como mínimo en horizontal</span></h5>
                <? endif; ?>
                <div class="card">
                    <div class="header bg-black" style="padding: 10px;">
                        <div class="row">
                            <div class="col-xs-12">
                                <h2><i class="material-icons">flight</i>&nbsp;<span>VISTA DE RESULTADOS</span></h2>
                            </div>
                        </div>
                    </div>
                    <div class="body">
                        <div id="divFilters">
                            <div class="row clearfix">
                                <form id="frmRes">
                                    <div class="col-xs-6 col-sm-4 col-md-2">
                                        <b>Aerolínea</b>
                                        <select id="filAirline" class="form-control">
                                            <option value="">Todas</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4 col-md-2">
                                        <b>Cabina</b>
                                        <select id="filCabina" class="form-control">
                                            <option value="">Todas</option>
                                            <option value="ECONOMIC">Economy</option>
                                            <option value="PREMIUM_ECONOMIC">Premium Ec.</option>
                                            <option value="BUSINESS">Business</option>
                                        </select>
                                    </div>
                                    <div class="col-xs-6 col-sm-4 col-md-1">
                                        <b>Duracion (hs)</b>
                                        <input type="number" class="form-control" id="txtDur" name="txtDur" pattern="[0-9]*">
                                    </div>
                                    <!--
                                    <div class="col-xs-6 col-sm-4 col-md-1">
                                        <b>Escalas Max</b>
                                        <input type="number" class="form-control" id="txtEsc" name="txtEsc" pattern="[0-9]*">
                                    </div>-->
                                    <div class="col-xs-6 col-sm-4 col-md-1">
                                        <b>Asientos Min</b>
                                        <input type="number" class="form-control" id="txtAsi" name="txtAsi" pattern="[0-9]*">
                                    </div>
                                    <div class="col-xs-6 col-sm-4 col-md-1">
                                        <b>$ Max / Tramo</b>
                                        <input type="number" class="form-control" id="txtTot" name="txtTot" pattern="[0-9]*">
                                    </div>
                                    <div class="col-xs-6 col-sm-4 col-md-1">
                                        <b>Milla $</b>
                                        <input type="number" class="form-control" id="txtMP" name="txtMP" pattern="[0-9]+(\.[0-9]+)?" value="<?= round($this->session->userdata('mile_price'), 2) ?>">
                                    </div>
                                    <div class="col-xs-6 col-sm-4 col-md-2">
                                        <br>
                                        <button id="btnFilter" type="button" class="btn bg-orange waves-effect btn-sm">
                                            <i class="material-icons">filter_list</i>
                                            <span>FILTRAR</span>
                                        </button>
                                        &nbsp;
                                        <? $back_url = $from_search ? "smiles/my_search" : "view"; ?>
                                        <a class="btn bg-red waves-effect btn-sm" href="<?= base_url() . $back_url ?>">
                                            <i class="material-icons">arrow_upward</i>
                                            <span>VOLVER</span>
                                        </a>
                                    </div>
                                </form>
                            </div>
                            <hr>
                            <div class="row clearfix">
                                <div class="col-xs-12 col-md-6">                                    
                                    <button id="btnList" type="button" class="btn bg-indigo waves-effect btn-sm btn-list-type" data-value="1">
                                        <i class="material-icons">list</i>
                                        <span>LISTA</span>
                                    </button>&nbsp;
                                    <? if (!$is_mobile) : ?>
                                    <button id="btnCalendar" type="button" class="btn bg-blue waves-effect btn-sm btn-list-type" data-value="3">
                                        <i class="material-icons">calendar_today</i>
                                        <span>CALENDARIO</span>
                                    </button>
                                    &nbsp;
                                    <? endif; ?>
                                    <button id="btnTrip" type="button" class="btn bg-blue waves-effect btn-sm btn-list-type" title="Una vez finalizada la búsqueda tendras opciones adicionales" data-value="2" disabled>
                                        <i class="material-icons">work</i>
                                        <span>ARMA TU VIAJE</span>
                                    </button>
                                    <br>
                                    <input type="checkbox" name="chkPrecio" id="chkPrecio" class="filled-in form-control m-t-10" />
                                    <label for="chkPrecio" id="lblPrecio" class="m-t-10" style="display: none;"><b>Total $ / Millas</b></label>
                                    &nbsp;&nbsp;
                                    <input type="checkbox" name="chkSM" id="chkSM" class="filled-in form-control" />
                                    <label for="chkSM" id="lblSM" style="display:none;"><b>Mostrar Smiles & Money</b></label>
                                </div>
                                <div id="divRef" class="col-xs-12 col-md-6" style="display: none;">
                                    <div class="row clearfix">
                                        <div class="col-xs-6">
                                            <p><b>CABINAS</b></p>
                                            <span><i class="material-icons" style="vertical-align: middle; display: inline-block;">airline_seat_recline_normal</i>&nbsp;&nbsp;Economy</span>
                                            <br>
                                            <span><i class="material-icons" style="vertical-align: middle; display: inline-block;">airline_seat_legroom_extra</i>&nbsp;&nbsp;Premium Eco.</span>
                                            <br>
                                            <span><i class="material-icons" style="vertical-align: middle; display: inline-block;">airline_seat_flat</i>&nbsp;&nbsp;Business</span>
                                        </div>
                                        <div class="col-xs-6">
                                            <p><b>PRECIO RELATIVO</b></p>
                                            <span style="color: purple;">&#11044;</span>&nbsp;&nbsp;<span>S & M *</span><br>
                                            <span style="color: green;">&#11044;</span>&nbsp;&nbsp;<span>Barato</span><br>
                                            <span style="color: #FFD700;">&#11044;</span>&nbsp;&nbsp;<span>Menor al Promedio</span><br>
                                            <span style="color: orange;">&#11044;</span>&nbsp;&nbsp;<span>Mayor al Promedio</span><br>
                                            <span style="color: red;">&#11044;</span>&nbsp;&nbsp;<span>Caro</span><br>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="preloader-generic text-center" id="loadingFlightResults" style="display: none;">
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
                            <div id="tabResults" style="min-height: 1000px" ;>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?= base_url() ?>js/admin.js"></script>
        <script>
            var is_search = false;
            var fdesde = '<?= $fdesde ?>';
            var fhasta = '<?= $fhasta ?>';
            var ways;
            var currListType = 1;
            var currPriceType = 0;
            var ida_cal_date = null;
            var vta_cal_date = null;
            var t_idas = <?= json_encode($t_idas) ?>;
            var t_vueltas = <?= json_encode($t_vueltas) ?>;
            var selectedIdaDay;
            var selectedVtaDay;

            function fillAirlines(airlines) {
                var selectedValue = $('#filAirline').val();
                $('#filAirline').empty();

                // Agregar la opción por defecto
                $('#filAirline').append($('<option>', {
                    value: '',
                    text: 'Todas'
                }));

                // Iterar sobre las aerolíneas y agregarlas al select
                $.each(airlines, function(codigo, nombre) {
                    $('#filAirline').append($('<option>', {
                        value: codigo,
                        text: nombre
                    }));
                });
                // Establecer el valor seleccionado previamente
                $('#filAirline').val(selectedValue);
            }

            function updateResults() {

                var dur = $("#txtDur").val();
                var esc = $("#txtEsc").val();
                var asi = $("#txtAsi").val();
                var tot = $("#txtTot").val();
                var cab = $("#filCabina").val();
                var air = $("#filAirline").val();
                var mile_price = $("#txtMP").val();
                var sm = $("#chkSM").prop("checked") ? 1 : 0;

                var data = {
                    fdesde: fdesde,
                    fhasta: fhasta,
                    duracion: dur,
                    escalas: esc,
                    asientos: asi,
                    airline: air,
                    total: tot,
                    cabina: cab,
                    mile_price: mile_price,
                    idas: t_idas,
                    vueltas: t_vueltas,
                    sm : sm,
                    is_ajax: 1
                };

                show_loading_flight_results(true);
                $.ajax({
                    type: 'POST',
                    data: data,
                    dataType: 'json',
                    url: '<?= base_url() . "View/ax_list_results/" ?>' + currListType,
                    success: function(response) {
                        show_loading_flight_results(false);
                        if (response.nosession) {
                            showNoSessionMsg();
                            return;
                        }
                        fillAirlines(response.airlines);
                        $("#tabResults").show();
                        $("#tabResults").html(response.html);

                        if (t_vueltas.length > 0) {
                            $("#btnTrip").prop('disabled', false);
                        }
                    }
                });
            }

            function updateResultPanel() {
                if (currListType != 2) {
                    updateResults();
                } else {
                    calculateStay();
                }
            }

            function selectListType(t) {
                $(".btn-list-type").removeClass("bg-indigo").addClass("bg-blue");
                $(".btn-list-type[data-value='" + t + "']").removeClass("bg-blue").addClass("bg-indigo");
                currListType = t;
                if (t == 3) {
                    $("#lblPrecio").show();
                    $("#lblSM").show();

                    $("#divRef").show();

                } else {
                    $("#lblPrecio").hide();
                    $("#lblSM").hide();
                    $("#divRef").hide();
                }
            }

            $(function() {
                $('#btnFilter').click(function() {
                    updateResultPanel();
                });

                $(".btn-list-type").click(function() {
                    var t = $(this).data("value");

                    selectListType(t);
                    updateResults();
                });

                $('#filCabina').change(function() {
                    updateResultPanel();
                });

                $('#chkPrecio').change(function() {
                    currPriceType = $(this).prop('checked');
                    updateResults();
                });

                $('#chkSM').change(function() {                    
                    updateResults();
                });
                //ON LOAD
                updateResults();
            });

            function showNoSessionMsg() {
                swal({
                    title: "Se ha perdido la sesión",
                    text: "Por favor vuelva a iniciar sesión. Ha sido deslogueado por inactividad",
                    icon: "error",
                    button: "OK"
                }).then((ok) => {
                    if (ok) {
                        window.location.href = '<?= base_url() ?>';
                    }
                });
            }

            function show_loading_flight_results(value) {
                if (value) {
                    $("#loadingFlightResults").show();
                } else {
                    $("#loadingFlightResults").hide();
                }
            }
        </script>
</body>

</html>