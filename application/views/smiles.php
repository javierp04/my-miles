<? $btn_style = $rol_id != 1 ? 'style="display: none;"' : ""; ?>
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
            pointer-events: none !important;
            cursor: not-allowed !important;
        }

        .my-partial-day {
            background-color: #E8FFE0;
        }

        .my-past-day {
            background-color: #D5D5D5;
            pointer-events: none !important;
            cursor: not-allowed !important;
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

        @keyframes blink {
            0% {
                background-color: yellow;
            }

            50% {
                background-color: white;
            }

            100% {
                background-color: yellow;
            }
        }

        /* Aplicar la animación a una clase de celda específica */
        .blinking-day {
            animation: blink 1s infinite;
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
    <!-- Overlay For Sidebars -->
    <div class="overlay"></div>
    <!-- #END# Overlay For Sidebars -->
    <?
    
    $this->view('templates/topbar');
    $this->view('templates/sidebar');
    ?>

    <section class="content">
        <div class="container-fluid">
            <div class="row clearfix">
                <div class="col-xs-12">
                    <div class="card">
                        <div class="header bg-black" style="padding: 10px;">
                            <div class="row">
                                <div class="col-xs-12">
                                    <h2><i class="material-icons" style="vertical-align: middle; display: inline-block; margin-top: -4px;">flight</i>&nbsp;<span>BUSCAR VUELOS POR TRAMO</span></h2>
                                </div>
                            </div>
                            <form id="frmFilters" method="POST">
                                <input type="hidden" name="hdnOrig" id="hdnOrig" />
                                <input type="hidden" name="hdnDest" id="hdnDest" />
                                <input type="hidden" name="hdnchkI" id="hdnchkI" value="<?= $chkI ?>" />
                                <input type="hidden" name="hdnchkV" id="hdnchkV" value="<?= $chkV ?>" />

                                <div class="row m-l-10 m-t-10">
                                    <div class="col-md-8">
                                        <div class="row">
                                            <div class="col-xs-1 col-sm-1 col-md-1">
                                                <h3 class="m-t-25"><i class="material-icons">flight_takeoff</i></h3>
                                                <h3 class="m-t-25"><i class="material-icons">flight_land</i></h3>
                                            </div>
                                            <div class="col-xs-6 col-sm-6 col-md-4">
                                                <b>País - Ciudad</b>
                                                <select id="airport_0" class="form-control air-dest filter-ctl" data-value="0">
                                                    <?= $from_search ? "" : '<option value="">-- Seleccione --</option>';
                                                    foreach ($all as $a) :
                                                        $sel_str = $origenes[0] == $a->Air_Code ? "selected" : "";
                                                        if (!$from_search || $sel_str) : ?>
                                                            <option value="<?= $a->Air_Code ?>" <?= $sel_str ?>><?= strtoupper($a->Country) . " - " . $a->City . " ({$a->Air_Code})" ?></option>
                                                        <? endif; ?>
                                                    <? endforeach; ?>
                                                </select>
                                                <br>
                                                <select id="airport_1" class="form-control air-dest filter-ctl" data-value="1">
                                                    <?= $from_search ? "" : '<option value="">-- Seleccione --</option>';
                                                    foreach ($all as $a) :
                                                        $sel_str = $destinos[0] == $a->Air_Code ? "selected" : "";
                                                        if (!$from_search || $sel_str) : ?>
                                                            <option value="<?= $a->Air_Code ?>" <?= $sel_str ?>><?= strtoupper($a->Country) . " - " . $a->City . " ({$a->Air_Code})" ?></option>
                                                        <? endif; ?>
                                                    <? endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-xs-5 col-sm-5 col-md-4">
                                                <b>Alternativa 1</b>
                                                <select id="airport_2" class="form-control air-dest filter-ctl" data-value="2" <?= !$origenes[1] ? "disabled" : "" ?>>
                                                    <?= $from_search ? "" : '<option value="">No Utilizar</option>';
                                                    foreach ($all as $a) :
                                                        $sel_str = $origenes[1] == $a->Air_Code ? "selected" : "";
                                                        if (!$from_search || $sel_str) : ?>
                                                            <option value="<?= $a->Air_Code ?>" <?= $sel_str ?>><?= strtoupper($a->Country) . " - " . $a->City . " ({$a->Air_Code})" ?></option>
                                                        <? endif; ?>
                                                    <? endforeach; ?>
                                                </select>
                                                <br />
                                                <select id="airport_3" class="form-control air-dest filter-ctl" data-value="3" <?= !$destinos[1] ? "disabled" : "" ?>>
                                                    <?= $from_search ? "" : '<option value="">No Utilizar</option>';
                                                    foreach ($all as $a) :
                                                        $sel_str = $destinos[1] == $a->Air_Code ? "selected" : "";
                                                        if (!$from_search || $sel_str) : ?>
                                                            <option value="<?= $a->Air_Code ?>" <?= $sel_str ?>><?= strtoupper($a->Country) . " - " . $a->City . " ({$a->Air_Code})" ?></option>
                                                        <? endif; ?>
                                                    <? endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="hidden-sm hidden-xs col-md-3">
                                                <b>Alternativa 2</b>
                                                <select id="airport_4" class="form-control air-dest filter-ctl" data-value="4" <?= !$origenes[2] || !$from_search ? "disabled" : "" ?>>
                                                    <?= $from_search ? "" : '<option value="">No Utilizar</option>';
                                                    foreach ($all as $a) :
                                                        $sel_str = $origenes[2] == $a->Air_Code ? "selected" : "";
                                                        if (!$from_search || $sel_str) : ?>
                                                            <option value="<?= $a->Air_Code ?>" <?= $sel_str ?>><?= strtoupper($a->Country) . " - " . $a->City . " ({$a->Air_Code})" ?></option>
                                                        <? endif; ?>
                                                    <? endforeach; ?>
                                                </select>
                                                <br />
                                                <select id="airport_5" class="form-control air-dest filter-ctl" data-value="5" <?= !$destinos[2] || !$from_search ? "disabled" : "" ?>>
                                                    <?= $from_search ? "" : '<option value="">No Utilizar</option>';
                                                    foreach ($all as $a) :
                                                        $sel_str = $destinos[2] == $a->Air_Code ? "selected" : "";
                                                        if (!$from_search || $sel_str) : ?>
                                                            <option value="<?= $a->Air_Code ?>" <?= $sel_str ?>><?= strtoupper($a->Country) . " - " . $a->City . " ({$a->Air_Code})" ?></option>
                                                        <? endif; ?>
                                                    <? endforeach; ?>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-xs-12 col-md-4">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-7">
                                                <b>Rango de Fechas</b>
                                                <input type="text" class="form-control filter-ctl" id="fechaDesde" name="fDesde" placeholder="Fecha Desde...">
                                                <br>
                                                <input type="text" class="form-control filter-ctl" id="fechaHasta" name="fHasta" placeholder="Fecha Hasta...">
                                            </div>
                                            <div class="col-xs-12 col-sm-12 col-md-5">
                                                <br>
                                                <?
                                                $disabled_iv = $from_search ? "disabled" : "";
                                                $checked_iv = !$from_search || !empty($chkV) ? "checked" : "";
                                                ?>


                                                <input type="checkbox" name="chkVuelta" value="1" id="chkVuelta" class="filled-in form-control filter-ctl" <?= $checked_iv . " " . $disabled_iv ?> />
                                                <label for="chkVuelta"><b>Ida y Vuelta</b></label>
                                                <br>
                                                <button id="btnSearch" type="button" class="btn bg-orange waves-effect btn-sm m-t-20">
                                                    <i class="material-icons">search</i>
                                                </button>                                                
                                                <? if ($rol_id == 1) : ?>
                                                    <button id="btnForce" type="button" class="btn bg-purple waves-effect btn-sm m-t-20">
                                                        <i class="material-icons">sync</i>
                                                    </button>
                                                <? endif; ?>
                                                <? if ($from_search) : ?>
                                                    <a href="<?= base_url() . "smiles/my_search" ?>" class="btn bg-red waves-effect btn-sm m-t-20">
                                                        <i class="material-icons">arrow_back</i>
                                                    </a>
                                                <? endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </form>
                        </div>
                        <div class="body">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div id="tabSearchInfo">
                                    </div>
                                    <div class="preloader-generic text-center" id="loadingSearchInfo" style="display: none;">
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
                                </div>
                            </div>
                            <div id="divSearching" style="display: none;">
                                <div class="row clearfix">
                                    <div class="hidden-xs hidden-sm col-md-2">
                                        &nbsp;
                                    </div>
                                    <div class="col-xs-8 col-md-6">
                                        <h4>
                                            <span id="infoOrig">BUE,COR,ROS</span>
                                            <i id="infoIcon" class="material-icons" style="vertical-align: middle; display: inline-block;">arrow_forward</i>
                                            <span id="infoDest">MAD,PAR,LON</span>
                                        </h4>
                                        <h5><span id="infoDesde">24/01/2024</span> - <span id="infoHasta">23/02/2024</span></h5>
                                        <div class="progress m-t-5">
                                            <div id="divBar" class="progress-bar bg-orange progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                <span id="spProgress">0%</span>
                                            </div>
                                        </div>
                                        <div class="preloader-generic text-center" id="searchingFlights">                                            
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
                                            <br>
                                            <b>
                                                <span id="spCurr">&nbsp;</span>
                                            </b>
                                            <br>
                                            <span id="spLeft">Calculando tiempo restante. . .</span>
                                        </div>
                                    </div>
                                    <div class="col-xs-4 col-md-2 m-t-20">
                                        <h4>&nbsp;</h4>
                                        <button id="btnPause" type="button" class="btn bg-amber waves-effect btn-sm">
                                            <i id="ipause" class="material-icons">pause</i>
                                            <i id="iplay" class="material-icons" style="display: none;">play_arrow</i>
                                        </button><span id="spPause" style="display: none;">&nbsp;&nbsp;pausando...</span>
                                    </div>
                                    <div class="hidden-xs hidden-sm col-md-2">
                                        &nbsp;
                                    </div>
                                </div>
                            </div>
                            <div id="divFilters" style="display: none;">
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
                                            <b>Duracion</b>
                                            <input type="number" class="form-control" id="txtDur" name="txtDur" pattern="[0-9]*">
                                        </div>                                        
                                        <div class="col-xs-6 col-sm-4 col-md-1">
                                            <b>Asientos</b>
                                            <input type="number" class="form-control" id="txtAsi" name="txtAsi" pattern="[0-9]*">
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-md-2">
                                            <b>$ Max / Tramo</b>
                                            <input type="number" class="form-control" id="txtTot" name="txtTot" pattern="[0-9]*">
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-md-1">
                                            <b>Milla $</b>
                                            <!-- <input type="number" class="form-control" id="txtMP" name="txtMP" pattern="[0-9]+(\.[0-9]+)?" value="<?= round($this->session->userdata('mile_price'), 2) ?>"> -->
                                            <input type="number" class="form-control" id="txtMP" name="txtMP" pattern="[0-9]+(\.[0-9]+)?" value="2.5">
                                        </div>
                                        <div class="col-xs-6 col-sm-4 col-md-3">
                                            <br>
                                            <button id="btnFilter" type="button" class="btn bg-orange waves-effect btn-sm">
                                                <i class="material-icons">filter_list</i><span>FILTRAR</span>
                                            </button>
                                            &nbsp;
                                            <button id="btnCancel" type="button" class="btn bg-red waves-effect btn-sm">
                                                <i class="material-icons">arrow_upward</i><span>VOLVER</span>
                                            </button>
                                        </div>
                                        <!--<div class="hidden-xs hidden-sm col-md-3">&nbsp;</div>-->
                                    </form>
                                </div>
                                <hr>
                                <div class="row clearfix">
                                    <div class="col-xs-12 col-md-6">
                                        <button id="btnList" type="button" class="btn bg-indigo waves-effect btn-sm btn-list-type" data-value="1" <?=$btn_style?>>
                                            <i class="material-icons">list</i>
                                            <span>LISTA</span>
                                        </button>
                                        <? if ($rol_id == 1) : ?>
                                            &nbsp;<button id="btnCalendar" type="button" class="btn bg-blue waves-effect btn-sm btn-list-type" data-value="3">
                                                <i class="material-icons">calendar_today</i>
                                                <span>CALENDARIO</span>
                                            </button>                                        
                                        <? endif; ?>                              
                                        &nbsp;<button id="btnTrip" type="button" class="btn bg-blue waves-effect btn-sm btn-list-type" data-value="2" <?=$btn_style?>>
                                            <i class="material-icons">work</i>
                                            <span>ARMA TU VIAJE</span>
                                        </button>
                                        <? if ($rol_id != 1) : ?>
                                            &nbsp;<button id="btnView" type="button" class="btn bg-green waves-effect btn-sm" style="display: none;">
                                                <i class="material-icons">assignment</i>
                                                <span>MÁS DETALLE</span>
                                            </button>
                                        <? endif; ?>
                                        <? if ($rol_id == 1) : ?>
                                            <br>
                                            <input type="checkbox" name="chkPrecio" id="chkPrecio" class="filled-in form-control m-t-10" />
                                            <label for="chkPrecio" id="lblPrecio" class="m-t-10" style="display: none;"><b>Total $ / Millas</b></label>
                                            &nbsp;&nbsp;
                                            <input type="checkbox" name="chkSM" id="chkSM" class="filled-in form-control" checked />
                                            <label for="chkSM" id="lblSM" style="display: none;"><b>Mostrar Smiles & Money</b></label>
                                        <? endif; ?>
                                    </div>

                                    <div id="divRef" class="col-xs-12 col-md-6" style="display: none;">
                                        <div class="row clearfix">
                                            <div class="col-xs-4">
                                                <p><b>REFERENCIA</b></p>
                                                <span style="color: #909090;"><b>&#9632;&nbsp;&nbsp;Sin Vuelos</b></span>
                                                <br>
                                                <span style="color: #8ADE83;"><b>&#9632;&nbsp;&nbsp;Resultado Incompleto</b></span>
                                                <br>
                                                <span style="color: #FFb5b4;"><b>&#9632;&nbsp;&nbsp;Pendiente de Búsqueda</b></span>
                                                <br>
                                                <span style="color: #99C8FF;"><b>&#9632;&nbsp;&nbsp;Seleccionado</b></span>
                                            </div>
                                            <div class="col-xs-4">
                                                <p><b>CABINAS</b></p>
                                                <span><i class="material-icons" style="vertical-align: middle; display: inline-block;">airline_seat_recline_normal</i>&nbsp;&nbsp;Economy</span>
                                                <br>
                                                <span><i class="material-icons" style="vertical-align: middle; display: inline-block;">airline_seat_legroom_extra</i>&nbsp;&nbsp;Premium Eco.</span>
                                                <br>
                                                <span><i class="material-icons" style="vertical-align: middle; display: inline-block;">airline_seat_flat</i>&nbsp;&nbsp;Business</span>
                                            </div>
                                            <div class="col-xs-4">
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
    </section>
    <div class="modal fade" id="searchModal" tabindex="-1" role="dialog" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4>Búsqueda de Rangos</h4>
                    <span id="dTitle">Buscando. . .</span>
                </div>
                <div class="modal-body">
                    <div class="row clearfix">
                        <div class="col-xs-12">
                            <div class="progress m-t-5">
                                <div id="divDBar" class="progress-bar bg-orange progress-bar-striped" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                    <span id="spDProgress">0%</span>
                                </div>
                            </div>
                            <div class="preloader-generic text-center" id="loadingDialog">
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
                                <br>
                                <b>
                                    <span id="spDCurr">&nbsp;</span>
                                </b>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">
                            <i class="material-icons">close</i>
                            <span><b>CANCELAR</b></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= base_url() ?>js/admin.js"></script>
    <script>
        var is_search = true;
        var tiempoInicio;
        var stopped = false;
        var finished = false;
        var cancelled = false;
        var fdesde;
        var fhasta;        
        var totalSearch = 0;        
        var ways;
        var retryLoc = 0;
        var retryServer = 0;
        var t_idas = [];
        var t_vueltas = [];
        var max_retry = 3;
        var use_server = '<?= $use_server ?>';
        var inc = <?= $inc ?>;
        var chk_vuelta_orig_st = '<?= $disabled_iv ?>';
        var gForce = 0;
        var currListType = 1;
        var excludeAirlines = '';
        var prefilter = 0;
        var onlyAirlines = '';
        var txtOnly = '';
        var txtExclude = '';
        var currResponse;
        var clInstances = 0;
        var srvInstances = 2;
        var currProgress;
        var currSearchId = 0;

        <? if ($rol_id == 1) : ?>
            var currPriceType = 0;
            var ida_cal_date = null;
            var vta_cal_date = null;
            var selectedIdaDay;
            var selectedVtaDay;
            //ACA IBA EL JS DE DIALOG
            
        <? endif; ?>

        function updateResultPanel() {
            if (currListType != 2) {                
                show_loading_flight_results(true);
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
            
            // if (window.location.hostname == 'my-miles.online') {
            //     clInstances = 1;
            //     srvInstances = 4;
            // }
            console.log("clInstances", clInstances);
            console.log("srvInstances", srvInstances);            
            $('#searchModal').on('hidden.bs.modal', function() {
                stopped = true;
            });

            $('#btnFilter').click(function() {
                updateResultPanel();
            });

            $(document).on('change', '.f-chk', function() {
                var $this = $(this);
                if ($this.is(':checked')) {
                    var id = $this.attr('id');
                    if (id.includes('chkI')) {
                        var siblingId = id.replace('chkI', 'chkV');
                    } else if (id.includes('chkV')) {
                        var siblingId = id.replace('chkV', 'chkI');
                    }
                    $('#' + siblingId).prop('checked', false);
                }
            });

            $(".btn-list-type").click(function() {
                var t = $(this).data("value");
                show_loading_flight_results(true);
                selectListType(t);
                if (stopped || finished) {
                    updateResults();
                }              
            });
            $("#btnView").click(function() {
                //Hacer que /trip sea por get y para fechas a results para que acote todo.
                if (currSearchId > 0) {                    
                    window.location.href = '<?= base_url() ?>view/results/' + currSearchId + '/trip';
                }
            });

            var fechaDesdeInput = $("#fechaDesde");
            var fechaHastaInput = $("#fechaHasta");

            var hoy = new Date();

            fechaDesdeInput.datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy',
                startDate: '+0d', // Solo seleccionable a partir de mañana
                endDate: '+329d', // Límite de 330 días desde mañana    
            }).on('changeDate', function(selected) {
                // Al cambiar la fecha en "Desde", establecemos la fecha en "Hasta"

                var desdeSeleccionado = new Date(selected.date);

                var inicioHasta = new Date(desdeSeleccionado);
                inicioHasta.setDate(inicioHasta.getDate() + 1);


                var hastaLimite30 = new Date(inicioHasta);
                hastaLimite30.setDate(hastaLimite30.getDate() + 30);

                var hastaLimite60 = new Date(inicioHasta);
                hastaLimite60.setDate(hastaLimite60.getDate() + 60);

                var hastaLimite330 = new Date(hoy);
                hastaLimite330.setDate(hastaLimite330.getDate() + 330);

                var hastaLimite;
                var hastaSeleccionado;
                hastaSeleccionado = hastaLimite330 < hastaLimite30 ? hastaLimite330 : hastaLimite30;
                hastaLimite = hastaLimite330 < hastaLimite60 ? hastaLimite330 : hastaLimite60;

                <? if ($rol_id == 1) : ?>
                    hastaLimite = hastaLimite330;
                <? endif; ?>

                hastaFinalLimite = new Date(hastaLimite);
                hastaFinalLimite.setDate(hastaFinalLimite.getDate() + 1);

                fechaHastaInput.datepicker('setStartDate', inicioHasta);
                fechaHastaInput.datepicker('setEndDate', hastaFinalLimite);
                fechaHastaInput.datepicker('setDate', hastaSeleccionado);
            });

            fechaHastaInput.datepicker({
                autoclose: true,
                format: 'dd-mm-yyyy',
                startDate: '+2d', // Solo seleccionable a partir de pasado mañana
                endDate: '<?= $rol_id == 1 ? "+330d" : "+60d" ?>', // 
            });

            // Establecer fechaDesde con la fecha de mañana
            <? if ($fdesde_sel == null) : ?>
                var fechaDesde = new Date(hoy);                
            <? else : ?>
                var fechaDesde = new Date('<?= $fdesde_sel ?>');
                
            <? endif; ?>
            fechaDesde.setDate(fechaDesde.getDate() + 1);
            fechaDesdeInput.datepicker('setDate', fechaDesde);

            // Calcular la fechaHasta sumando 2 días a la fecha de mañana (pasado mañana)
             // Establecer fechaDesde con la fecha de mañana
             <? if ($fhasta_sel == null) : ?>
                var fechaHasta = new Date(fechaDesde);
                fechaHasta.setDate(fechaHasta.getDate() + 30); // Por defecto, 30 días después de "Desde"
            <? else : ?>
                var fechaHasta = new Date('<?= $fhasta_sel ?>');
                fechaHasta.setDate(fechaHasta.getDate() + 1);
            <? endif; ?>
            
            // Establecer fechaHasta con la fecha calculada
            fechaHastaInput.datepicker('setDate', fechaHasta);

            $('.air-dest').change(function() {
                dest = $(this).val();
                dv = $(this).data("value");
                if (dv < 4) {
                    $("#airport_" + (dv + 2)).prop("disabled", false);
                }
                for (i = 0; i < 6; i++) {
                    if (dest != "" && dv != i) {
                        if (dest == $("#airport_" + i).val() && $("#airport_" + i).val() != "" && $("#airport_" + dv).val()) {
                            $(this).val("");
                            swal({
                                title: "Error selección",
                                text: "Las ciudades seleccionadas deben ser diferentes.",
                                icon: "error"
                            });
                            break;
                        }
                    }
                }
            });

            $('#filCabina').change(function() {
                updateResultPanel();
            });

            $("#btnSearch").click(function() {
                checkSearchInfo(0);
            });

            $("#btnPause").click(function() {
                stopped = !stopped;
                if (!stopped) {
                    //PLAY - CONTINUE                    
                    startResumeSearch();                    
                } else {
                    //PAUSE
                    $("#spPause").show();
                }
            });

            $("#btnCancel").click(function() {
                cancelled = true;
                stopped = true;
                if (chk_vuelta_orig_st == "disabled") {
                    $("#chkVuelta").prop("disabled", true);
                }                
                $("#divSearching").hide();
                $("#divFilters").hide();
                $("#tabResults").html("");
                $("#frmFilters").show();
                selectListType(1);
            });

            <? if ($rol_id == 1) : ?>
                $("#btnForce").click(function() {
                    checkSearchInfo(1);
                });

                $('#chkPrecio').change(function() {
                    show_loading_flight_results(true);
                    currPriceType = $(this).prop('checked');
                    if (stopped || finished)
                        updateResults();
                });

                $('#chkSM').change(function() {
                    show_loading_flight_results(true);                    
                    if (stopped || finished)
                        updateResults();
                });
            <? endif; ?>

        });

        function checkSearchInfo(force) {
            $("#tabResults").html("");
            var origenString = construirCadena(0);
            var destinoString = construirCadena(1);

            // Validar que al menos haya un valor completado en cada cadena
            if (origenString === "" || destinoString === "" || $("#fechaDesde").val() === "" || $("#fechaHasta").val() === "") {
                swal({
                    title: "Error selección",
                    text: "Debe completar al menos un origen y un destino y el rango de fechas",
                    icon: "error"
                });
                return;
            }
            $("#hdnOrig").val(origenString);
            $("#hdnDest").val(destinoString);
            gForce = force;
            searchInfo(force);
        }


        function searchInfo(force) {
            fdesde = $("#fechaDesde").val();
            fhasta = $("#fechaHasta").val();
            $("#chkVuelta").prop("disabled", false);
            var data = $("#frmFilters").serializeArray();
            data.push({
                name: "is_ajax",
                value: "1"
            });
            data.push({
                name: "force",
                value: force
            });
            $("#tabSearchInfo").html("");
            show_loading_search_info(true);
            $.ajax({
                type: 'POST',
                data: data,
                dataType: 'json',
                url: '<?= base_url() . "smiles/ax_list_searches" ?>',
                success: function(response) {
                    if (response.nosession) {
                        showNoSessionMsg();
                        return;
                    }
                    
                    var countDays = response.json.daysToUpdate.length;
                    ways = response.json.ways;
                    show_loading_search_info(false);
                    $("#frmFilters").hide();
                    $("#tabSearchInfo").html(response.html);    
                }
            });
        }

        function backSearch() {
            $("#frmFilters").show();
            $("#tabSearchInfo").html("");
            $("#divFilters").hide();
            $("#tabResults").hide();
            if (chk_vuelta_orig_st == "disabled") {
                $("#chkVuelta").prop("disabled", true);
            }
        }

        function startSearch() {
            has_vueltas = $("#chkVuelta").prop('checked');
            retryLoc = 0;
            retryServer = 0;
            is_dialog = 0;
            finished = false;
            ida_cal_date = null;
            vta_cal_date = null;
            selectedIdaDay = null;
            selectedVtaDay = null;
            cancelled = false;            
            
            t_idas = [];
            t_vueltas = [];
            excludeAirlines = $("#selExclude").val();
            txtExclude = $("#txtExclude").val();
            prefilter = $("#chkPrefilter").prop("checked") ? 1 : 0
            onlyAirlines = $("#selOnly").val();
            txtOnly = $("#txtOnly").val();
            
            $("#btnTrip").hide();
            <? if ($rol_id != 1) : ?>
                $("#btnList").hide();
                $("#btnView").hide();
            <? endif; ?>
            
            $('input[type="checkbox"].f-chk:checked').each(function() {
                var w = {};
                var tmp = this.value.substr(1).split('-');
                w.orig = tmp[0];
                w.dest = tmp[1];
                if (this.value.indexOf('I') === 0) {
                    t_idas.push(w);
                } else if (this.value.indexOf('V') === 0) {
                    t_vueltas.push(w);
                }
            });

            if (t_idas.length == 0) {
                swal({
                    title: "Error selección",
                    text: "Debe seleccionar al menos algún vuelo de IDA",
                    icon: "error"
                });
                return;
            }
            if (has_vueltas && t_vueltas.length == 0) {
                swal({
                    title: "Error selección",
                    text: "Debe seleccionar al menos algún vuelo de VUELTA",
                    icon: "error"
                });
                return;
            }

            show_loading_search_info(true);
            if (gForce) {
                delSearchResponse();
            } else {
                do_startSearch();
            }
        }

        function do_startSearch() {
                        
            data = {
                idas: t_idas,
                vueltas: t_vueltas,
                fdesde: fdesde,
                fhasta: fhasta,
                is_ajax: 1                
            };

            $.ajax({
                type: 'POST',
                data: data,
                dataType: 'json',
                url: '<?= base_url() . "smiles/ax_days_to_update" ?>',
                success: function(response) {
                    if (response.nosession) {
                        showNoSessionMsg();
                        return;
                    }
                    show_loading_search_info(false);
                    if (response.status == "ok") {                                                
                        totalSearch = response.daysToUpdate.length;
                        currProgress = totalSearch;
                        currSearchId = response.search_id;
                        $("#divFilters").show();
                        $("#tabSearchInfo").html("");
                        if (totalSearch > 0) {                            
                            currResponse = {search : response.daysToUpdate[0], progress : totalSearch};     
                            <? if ($rol_id == 1) : ?>
                                do_searchFlight();
                            <? else : ?>
                                searchFlight(response.req_need, response.req_left);
                            <? endif; ?>
                        } else {
                            //SOLO CARGA RESULTADOS
                            show_loading_flight_results(true);
                            stopped = true;
                            finished = true;
                            updateResults();
                        }
                    } else if (response.status == "req_exceed") {
                        swal({
                            title: "Excede su cuota de búsqueda",
                            text: "Le quedan " + response.req_left + " créditos y actualmente necesita " + response.req_need + ". Intente acotar el rango de búsqueda",
                            icon: "warning"
                        });
                    } else if (response.status == "oversize") {
                        swal({
                            title: "La búsqueda es demasiado amplia",
                            text: "La búsqueda que intenta realizar es demasiado amplia. Intente acotar los destinos o fechas y realizarla en varios tramos",
                            icon: "warning"
                        });
                    }
                }
            });        
        }

        function searchFlight(req_need, req_left) {
            swal({
                title: "Comenzar Búsqueda",
                text: "Se consumirán " + req_need  + " créditos de los " + req_left + " disponibles del día, en la medida que se vaya avanzando en la búsqueda. ¿Desea Continuar?",
                icon: "info",
                buttons: true
            }).then((willEnable) => {
                if (willEnable) {
                    do_searchFlight();
                }
            });
        }

        function do_searchFlight() {            
            //Total => Used only to check if > 1 to engage server search in case enabled.
            stopped = false;
            $("#divSearching").show();
            $('#infoOrig').html($('#hdnOrig').val());
            $('#infoDest').html($('#hdnDest').val());
            var icon = t_vueltas.length > 0 ? "swap_horiz" : "arrow_forward";
            $('#infoIcon').html(icon);
            $('#infoDesde').html($('#fechaDesde').val().replace(/-/g, "/"));
            $('#infoHasta').html($('#fechaHasta').val().replace(/-/g, "/"));
            $("#spLeft").html("Calculando tiempo restante. . .");
            tiempoInicio = new Date();                        
            startResumeSearch();            
        }

        function startResumeSearch() {               
            updateProgress();
            updateButtons();
            //show_searching_flights(true);
            for (i=0;i<clInstances;i++) {
                readResults(i);
            }
            if (totalSearch > 1 && use_server == 1) {
                for (i=0;i<srvInstances;i++) {
                    readResults_srv(i);
                }
            }
        }

        function readResults(instance) {
            data = {
                idas: t_idas,
                vueltas: t_vueltas,
                fdesde: fdesde,
                fhasta: fhasta,
                is_ajax: 1                
            };
            var tst = new Date().getTime();
            //console.log("client_search " + instance, "started");
            $.ajax({
                type: 'POST',
                data: data,
                dataType: 'json',
                url: '<?= base_url() . "smiles/ax_client_search" ?>',
                success: function(response) {
                    if (response.nosession) {
                        showNoSessionMsg();
                        return;
                    }
                    
                    if (response.search) {
                        console.log(response);                        
                        currResponse = response;
                        var src = currResponse.search;
                        console.log("Client ", src.Orig, src.Dest, src.Fecha);
                        console.log("CLI Progress", response.progress);
                        currProgress = Math.min(response.progress, currProgress);
                        var twait = instance * 2000 + (clInstances - 1) * 4000;                        
                        //var twait = instance * 0 + (clInstances - 1) * 0;                        
                        //console.log("client_search " + instance, "took " + (new Date().getTime() - tst)  / 1000);
                        setTimeout(() => searchSmilesAPI(src.Orig, src.Dest, src.Fecha, instance), twait);
                    } 
                    if (response.status == "finished") {
                        finishSearch();
                        console.log("Client", "status: finished");
                    }
                }
            });
            
        }

        function readResults_srv(instance) {            
            mile_price = $("#txtMP").val();
            var tst = new Date().getTime();
            //console.log("server_search " + instance, "started");
            $.ajax({
                type: 'POST',
                data: {
                    mile_price: mile_price,
                    fdesde : fdesde,
                    fhasta : fhasta,
                    idas: t_idas,
                    vueltas: t_vueltas,                    
                    excludeAirlines : excludeAirlines,
                    txtExclude : txtExclude,
                    prefilter : prefilter,
                    onlyAirlines : onlyAirlines,
                    txtOnly : txtOnly,
                    is_ajax: 1
                },
                dataType: 'json',
                url: '<?= base_url() . "Smiles/ax_do_search" ?>',
                success: function(response) {
                    if (response.nosession) {
                        showNoSessionMsg();
                        return;
                    }
                    if (response.error != null) {
                        retryServer++;
                        console.log("Error SRV", retryServer);
                        if (retryServer < max_retry) {
                            readResults_srv(instance);
                        } else {
                            if (response.error == 403) {
                                showConectionLostError();
                            } else if (response.error == 452) {
                                swal({
                                    title: "Error en el servicio de smiles",
                                    text: response.error + ":  " + response.response.errorMessage,
                                    icon: "warning"
                                });
                                $("#divSearching").hide();
                            }
                            sendErrorLog("SERVER API ERROR", response.error);
                        }
                    } 
                    if (response.search) {
                        retryServer = 0;
                        var src = response.search; 
                        //console.log("server_search " + instance, "took " + (new Date().getTime() - tst) / 1000);
                        console.log("Server", src.Orig, src.Dest, src.Fecha);
                        console.log("SRV Progress", response.progress);
                        currProgress = Math.min(currProgress, response.progress);
                        if (!cancelled) {
                            
                            updateResults(); 
                            updateProgress();
                            
                            if (stopped) {                                
                                updateButtons();
                            } else {
                                readResults_srv(instance);
                            }
                        }
                    }
                    if (response.status == "finished") {
                        console.log("Server", "Status: finished");                        
                        finishSearch();
                    }
                }
            });
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
            $.ajax({
                type: 'POST',
                data: {
                    fdesde: fdesde,
                    fhasta: fhasta,
                    duracion: dur,
                    escalas: esc,
                    asientos: asi,
                    total: tot,
                    cabina: cab,
                    airline: air,
                    mile_price: mile_price,
                    idas: t_idas,
                    sm : sm,
                    vueltas: t_vueltas,
                    is_ajax: 1
                },
                dataType: 'json',
                url: '<?= base_url() . "Smiles/ax_list_results/" ?>' + currListType,
                success: function(response) {
                    if (response.nosession) {
                        showNoSessionMsg();
                        return;
                    }

                    fillAirlines(response.airlines);
                    if (!cancelled) {
                        $("#tabResults").show();
                        $("#tabResults").html(response.html); 
                        $("#spReqUsed").html(response.req_sent);
                        show_loading_flight_results(false);
                        if (finished || stopped) {
                            show_searching_flights(false);
                        }
                        if (finished) {              
                            $("#btnList").show();
                            $("#btnView").show();
                            if (has_vueltas && t_vueltas.length > 0) {
                                $("#btnTrip").show();                                
                            }                            
                        }
                    }
                }
            });
        }

        function processResults(orig, dest, sdate, flightList, instance) {
            //Esta funcion escribe los resultados tambien de ax_list_result
            var dur = $("#txtDur").val();
            var esc = $("#txtEsc").val();
            var asi = $("#txtAsi").val();
            var tot = $("#txtTot").val();
            var cab = $("#filCabina").val();
            var air = $("#filAirline").val();
            var mile_price = $("#txtMP").val();
            var sm = $("#chkSM").prop("checked") ? 1 : 0;
            var tst = new Date().getTime();
            //console.log("process_client " + instance, "started");
            $.ajax({
                url: '<?= base_url() . "smiles/ax_process_search" ?>',
                type: 'POST',
                data: {
                    orig : orig,
                    dest : dest,
                    fecha : sdate,
                    listType : currListType,
                    flightList: JSON.stringify(flightList),
                    fdesde: fdesde,
                    fhasta: fhasta,
                    duracion: dur,
                    escalas: esc,
                    asientos: asi,
                    total: tot,
                    cabina: cab,
                    airline: air,
                    mile_price: mile_price,
                    idas: t_idas,
                    vueltas: t_vueltas,
                    excludeAirlines : excludeAirlines,
                    txtExclude : txtExclude,
                    prefilter : prefilter,
                    onlyAirlines : onlyAirlines,
                    txtOnly : txtOnly,
                    sm : sm,
                    is_ajax: 1
                },
                success: function(response) {
                    if (response.nosession) {
                        showNoSessionMsg();
                        return;
                    }
                    if (response.res == "get_tax") {
                        searchSmilesTaxAPI(response.fl_to_tax[0]);
                    }
                    if (!cancelled) {
                        //console.log("process_client " + instance, "took " + (new Date().getTime() - tst) / 1000);
                        $("#tabResults").html(response.html);
                        $("#spReqUsed").html(response.req_sent);
                        show_loading_flight_results(false);
                        if (response.res < 0) {
                            showConectionLostError();
                        } else {                            
                            doNextClientSearch(instance);
                        }
                    }
                }
            });
        }

        function processTaxResult(tf, tax_value) {
            mile_price = $("#txtMP").val();
            var dur = $("#txtDur").val();
            var esc = $("#txtEsc").val();
            var asi = $("#txtAsi").val();
            var tot = $("#txtTot").val();
            var cab = $("#filCabina").val();
            var air = $("#filAirline").val();
            //si da ok, hace el end y luego el list result.
            $.ajax({
                url: '<?= base_url() . "Smiles/ax_update_tax/" ?>' + currListType,
                type: 'POST',
                dataType: 'json',
                data: {
                    tf: tf,
                    tax_value: tax_value,
                    mile_price: mile_price,
                    idas: t_idas,
                    vueltas: t_vueltas,
                    fdesde: fdesde,
                    fhasta: fhasta,
                    duracion: dur,
                    escalas: esc,
                    asientos: asi,
                    total: tot,
                    cabina: cab,
                    airline: air,
                    is_ajax: 1
                },
                success: function(response) {
                    if (response.res == "get_tax") {
                        searchSmilesTaxAPI(response.fl_to_tax[0]);
                    }
                }
            });
        }

        function searchSmilesTaxAPI(tf) {
            // iI84zGW88ysDWg7xVgGQSHmG39Q5sIytl965Xoi5lvHjqmHr3b20j6
            var url = "https://api-airlines-boarding-tax-prd.smiles.com.br/v1/airlines/flight/boardingtax?adults=1&children=0&infants=0&fareuid=" + tf.FareUid + "&uid=" + tf.Uid + "&type=SEGMENT_1&highlightText=SMILES_CLUB";
            var headers = {
                "Accept": "application/json, text/plain, */*",
                "Accept-Language": "es-US,es;q=0.9,en-US;q=0.8,en;q=0.7,es-419;q=0.6",
                "Authorization": "Bearer <?= $authTax ?>",
                "x-api-key": "aJqPU7xNHl9qN3NVZnPaJ208aPo2Bh2p2ZV844tw",
                "region": "ARGENTINA",
                "channel": "Web",
                "language": "es-ES"
            };
            $.ajax({
                url: url,
                type: 'GET',
                headers: headers,
                success: function(response) {
                    tax_res = JSON.parse(response);
                    tax_value = tax_res.totals.total.money;
                    processTaxResult(tf, tax_value);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log("TAX ERROR " + textStatus, errorThrown);
                    if (jqXHR.status == 403) {
                        sendErrorLog("CLIENT TAX API 403", errorThrown)
                        showConectionLostError();
                    } else {
                        processTaxResult(tf, -2);
                    }
                }
            });
        }

        function doNextClientSearch(instance) {            
            //SI NO ESTA EN DIALOG
            if (!is_dialog) {
                updateProgress();                
                if (stopped) {                    
                    updateButtons();
                }
            } else {
                //DIALOG CODE
                updateProgressDialog();                
            }
            if (!stopped) { 
                readResults(instance);
            }
        }

        function updateProgress() {            
            
            var toSearch = currResponse.search;
            var searchCount = totalSearch - currProgress;
            var tiempoTranscurrido = new Date() - tiempoInicio;
            var tiempoRestante = Math.ceil(tiempoTranscurrido / searchCount) * currProgress;
            percent = searchCount > 0 ? Math.ceil(searchCount / totalSearch * 100) : "0";            
            $('#divBar').attr('aria-valuenow', percent);
            $("#divBar").css("width", percent + "%");            
            var currentDate = new Date(toSearch.Fecha);
            currentDate.setHours(currentDate.getHours() + 19);
            var dia = currentDate.getDate();
            var mes = currentDate.getMonth() + 1; // Nota: los meses comienzan desde 0, por lo que sumamos 1
            var fechaFormateada = (dia < 10 ? '0' : '') + dia + '/' + (mes < 10 ? '0' : '') + mes;
            $('#spProgress').text(fechaFormateada + ' (' + percent + '%)');
            $("#spCurr").html(toSearch.Orig + "-" + toSearch.Dest + " " + fechaFormateada + " (" + searchCount + "/" + totalSearch + ")");
            if (searchCount > 0) {
                $('#spLeft').html('Tiempo restante: <b>' + formatTiempo(tiempoRestante) + '</b>');
            }            
            
        }

        function finishSearch() {
            stopped = true;
            finished = true;
            $("#divSearching").hide();
            $("#tabSearchInfo").html("");            
            updateResults();      
            updateButtons();
            $("#btnList").show();
            $("#btnView").show();
            if (has_vueltas && t_vueltas.length > 0) {
                $("#btnTrip").show();
            }
        }

        function updateButtons() {
            $("#spPause").hide();
            $("#ipause").hide();
            $("#iplay").hide();
            $("#btnPause").removeClass("bg-amber");
            $("#btnPause").removeClass("bg-green");
            show_searching_flights(!stopped);
            if (stopped) { //PAUSED
                $("#iplay").show();
                $("#btnPause").addClass("bg-green");      
                $('#divBar').removeClass("active");          
            } else { //RUNNING
                $("#ipause").show();
                $("#btnPause").addClass("bg-amber");
                $('#divBar').addClass("active");
            }
        }

        function searchSmilesAPI(orig, dest, fecha, instance) {
            
            var url = "https://api-air-flightsearch-green.smiles.com.br/v1/airlines/search?adults=1&cabinType=all&children=0&currencyCode=ARS&departureDate=" +
                fecha + "&destinationAirportCode=" + dest + "&infants=0&isFlexibleDateChecked=false&originAirportCode=" + orig + "&tripType=2&forceCongener=true&r=ar";
            //iI84zGW88ysDWg7xVgGQSHmG39Q5sIytl965Xoi5lvHjqmHr3b20j6

            var headers = {
                "Content-Type": "application/json",
                "Authorization": "Bearer lw9orb0kiur8Yhunkb4y7SNYz25C2iamK5awiF7eWgQI6jK17jqbT9", //<?=$auth?>",
                "x-api-key": "aJqPU7xNHl9qN3NVZnPaJ208aPo2Bh2p2ZV844tw",
                "region": "ARGENTINA",
                "channel": "Web",
                "language": "es-ES"
            };

            $.ajax({
                url: url,
                type: 'GET',
                headers: headers,
                success: function(response) {                    
                    retryLoc = 0;
                    //MODIFICADO 2025
                    //var flights = JSON.parse(response).requestedFlightSegmentList[0].flightList;
                    var flights = response.requestedFlightSegmentList[0].flightList;                    
                    var filteredFlightList = flights.filter(function(item) {
                        return item.tripType && item.tripType.indexOf("Award") !== -1;
                    });
                    processResults(orig, dest, fecha, filteredFlightList, instance);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    retryLoc++;
                    console.log("Error Local", retryLoc);
                    console.error("Error (" + retryLoc + "): " + textStatus, errorThrown);
                    if (retryLoc < max_retry && !stopped) {
                        searchSmilesAPI(orig, dest, fecha);
                    } else if (retryLoc >= max_retry) {
                        sendErrorLog("CLIENT API " + jqXHR.status, errorThrown)
                        showConectionLostError();
                    }
                }
            });
        }

        function delSearchResponse() {            
            data = {
                idas: t_idas,
                vueltas: t_vueltas,
                fdesde: fdesde,
                fhasta: fhasta,
                is_ajax: 1,                
            };

            $.ajax({
                type: 'POST',
                data: data,
                dataType: 'json',
                url: '<?= base_url() . "smiles/ax_del_search_response" ?>',
                success: function(response) {
                    if (response.nosession) {
                        showNoSessionMsg();
                        return;
                    }                    
                    if (response.delete == "ok") {
                        do_startSearch();
                    }
                }
            });
        }

        function sendErrorLog(textStatus, errorThrown) {
            $.ajax({
                type: 'POST',
                data: {
                    ts: textStatus,
                    er: errorThrown,
                    is_ajax: 1
                },
                dataType: 'json',
                url: '<?= base_url() . "Smiles/ax_save_error_log" ?>',
                success: function(response) {
                    console.log("Error sent");
                }
            });
        }

        function construirCadena(p) {
            var cadena = "";

            for (var i = p; i < 6; i = i + 2) {
                var valor = $("#airport_" + i).val();
                if (valor !== "" && valor !== null) {
                    if (cadena !== "") {
                        cadena += ",";
                    }
                    cadena += valor;
                }
            }
            return cadena;
        }

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

        function showConectionLostError() {
            swal({
                title: "Error en el servicio de smiles",
                text: "Se ha perdido el acceso al servidor de smiles o la conexión ha sido temporalmente inhabilitada",
                icon: "error"
            });
            $("#divSearching").hide();

        }

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
        function formatTiempo(tiempoRestante) {
            var segundos = Math.floor(tiempoRestante / 1000);
            var horas = Math.floor(segundos / 3600);
            segundos %= 3600;
            var minutos = Math.floor(segundos / 60);
            segundos %= 60;

            return minutos + "m " + segundos + "s";
        }

        function show_loading_search_info(value) {
            if (value) {
                $("#loadingSearchInfo").show();
            } else {
                $("#loadingSearchInfo").hide();
            }
        }

        function show_searching_flights(value) {
            if (value) {
                $("#searchingFlights").show();
            } else {
                $("#searchingFlights").hide();
            }
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