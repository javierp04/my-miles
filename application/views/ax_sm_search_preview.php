<table class="table table-striped table-hover table-condensed table-bordered">
    <thead>
        <tr class="info">
            <th class="text-center">Origen</th>
            <th class="text-center">Destino</th>
            <th class="text-center">Desde</th>
            <th class="text-center">Hasta</th>
            <th class="text-center">Créditos Necesarios</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <? foreach ($ways as $w) :
            $id = $w["orig"] . "-" . $w["dest"];
            $info = "";
            if (!empty($chkI)) {
                $ci_check = strpos($chkI, $id) !== false ? "checked" : "";
            } else {
                $ci_check = in_array($w["orig"], $departs) || !$has_ret ? "checked" : "";
            }
            if ($has_ret) {
                if (!empty($chkV)) {
                    $cv_check = strpos($chkV, $id) !== false ? "checked" : "";
                } else {
                    $cv_check = in_array($w["dest"], $departs) ? "checked" : "";
                }
            }
            $show = $ci_check != "" || $has_ret && $cv_check != "";

            if ($show) : ?>

                <tr>
                    <td class="align-middle text-center">
                        <?= $w["orig"] ?>
                    </td>
                    <td class="align-middle text-center">
                        <?= $w["dest"] ?>
                    </td>

                    <td class="align-middle text-center">
                        <?= $desde ?>
                    </td>
                    <td class="align-middle text-center">
                        <?= $hasta ?>
                    </td>
                    <td class="align-middle text-center">
                        <?= $w["cant"] ?>
                    </td>
                    <td class="align-middle text-center">
                        <input type="checkbox" id="chkI<?= $id ?>" class="filled-in f-chk" value="I<?= $id ?>" <?= $ci_check . " " . (!empty($chkI) ? "disabled" : "") ?> />
                        <label for="chkI<?= $id ?>">Ida</label>
                        <? if ($has_ret) : ?>
                            &nbsp;&nbsp;
                            <input type="checkbox" id="chkV<?= $id ?>" class="filled-in f-chk" value="V<?= $id ?>" <?= $cv_check . " " . (!empty($chkI) ? "disabled" : "") ?> />
                            <label for="chkV<?= $id ?>">Vuelta</label>
                        <? endif; ?>
                    </td>
                </tr>
        <? endif;
        endforeach ?>
    </tbody>
</table>
<div class="row">
    <div class="col-xs-12 col-md-6">
        <? if ($rol_id == 1) : ?>
            <div class="row">                
                <div class="col-xs-6">
                    <span><b>Solamente</b></span>
                    <select id="selOnly" class="form-control">
                        <option value="">-- Seleccione --</option>
                        <option value="AR">Aerolíneas Argentinas</option>
                        <option value="AM">Aeroméxico</option>
                        <option value="UX">Air Europa</option>
                        <option value="AF">Air France</option>
                        <option value="AA">American Airlines</option>                        
                        <option value="AV">Avianca</option>
                        <option value="KL">KLM</option>
                        <option value="TK">Turkish</option>
                    </select>
                    <br>
                    <input type="text" id="txtOnly" name="txtOnly" class="form-control" />
                </div>
                <div class="col-xs-6">
                    <span><b>Excluir</b></span>
                    <select id="selExclude" class="form-control">
                        <option value="">-- Seleccione --</option>
                        <option value="AR">Aerolíneas Argentinas</option>
                        <option value="AM">Aeroméxico</option>                        
                        <option value="UX">Air Europa</option>
                        <option value="AF">Air France</option>
                        <option value="AA">American Airlines</option>
                        <option value="AV">Avianca</option>
                        <option value="KL">KLM</option>
                        <option value="TK">Turkish</option>
                    </select>
                    <br>
                    <input type="text" id="txtExclude" name="txtExclude" class="form-control" />
                </div>                
            </div>
        <? else : ?>
            &nbsp;
        <? endif; ?>
    </div>
    <div class="col-xs-12 col-md-6 align-right">
        <? if ($req_left < $cant_search && !empty($chkI)) : ?>
            <button type="button" class="btn bg-grey m-t-10" disabled title="Necesita <?= $cant_search ?> pero le quedan <?= $req_left ?>">
                <i class="material-icons">close</i><span><b>CRÉDITOS EXCEDIDOS</b></span>
            </button>
        <? elseif ($cant_search == 0) : ?>
            <button id="btnDoSearch" type="button" class="btn bg-green waves-effect m-t-10" onclick="startSearch();">
                <i class="material-icons">done</i><span><b>VER RESULTADOS</b></span>
            </button>
        <? else : ?>
            <? if ($rol_id == 1 || 1 == 1) : ?>
                <input type="checkbox" name="chkPrefilter" id="chkPrefilter" class="filled-in form-control" />
                <label for="chkPrefilter" id="lblPrefilter"><b>Pre-filtrar</b></label>
                &nbsp;&nbsp;
            <? endif; ?>
            <button id="btnDoSearch" type="button" class="btn bg-orange waves-effect m-t-10" onclick="startSearch();">
                <i class="material-icons">search</i><span><b>INICIAR BÚSQUEDA</b></span>
            </button>
        <? endif; ?>
        &nbsp;&nbsp;
        <button id="btnBack" type="button" class="btn bg-red waves-effect m-t-10" onclick="backSearch();">
            <i class="material-icons">arrow_back</i><span><b>VOLVER</b></span>
        </button>
    </div>
</div>