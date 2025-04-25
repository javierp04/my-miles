<? if (isset($searches) && count($searches) > 0) : ?>
    <table class="table table-condensed table-bordered table-responsive">
        <thead>
            <tr class="info">
                <th class="text-center">ID</th>
                <th class="text-center">Actualizado al</th>
                <th class="text-center">Desde ($<?= number_format(round($this->session->userdata('mile_price'), 2), 2, ',', '.') ?> / Milla)</th>
                <th class="text-center">Tramos Ida</th>
                <th class="text-center">Tramos Regreso</th>
                <th class="text-center">Rango Disponible</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            <? foreach ($searches as $s) :
                $tr_class = !$s->Valid ? "old-search" : ($s->HasView == 1 ? "has-view" : "");
            ?>
                <tr class="<?= $tr_class ?>">
                    <td class="align-middle text-center">
                        <?= $s->Search_Id ?>
                    </td>

                    <td class="align-middle text-center">
                        <?= date("d/m H:i", strtotime($s->LastUpdate)) ?>
                    </td>
                    <td class="align-middle text-center"><b>                        
                        <? if ($s->Valid || 1 == 1) {
                            echo "$" . number_format($s->MinIda + $s->MinVta, 0, ',', '.');
                        } else {
                            echo "NO DISPONIBLE";
                        } ?>
                        </b>
                    </td>
                    <td>

                        <table style="width: 100%;" border="0" cellpadding="3">
                            <? foreach ($s->Idas as $ida) : ?>
                                <tr>
                                    <td style="text-align: right; width: 45%;"><?= "{$ida->OrigCity} ({$ida->Orig})" ?></td>
                                    <td style="text-align: center; width: 10%;"><i class="material-icons" style="vertical-align: middle; display: inline-block;">arrow_forward</i>
                                    <td style="width: 45%;"><?= "{$ida->DestCity} ({$ida->Dest})" ?></td>
                                </tr>
                            <? endforeach; ?>
                        </table>
                    </td>

                    <td>
                        <table style="width: 100%;" border="0" cellpadding="3">
                            <? foreach ($s->Vueltas as $vta) : ?>
                                <tr>
                                    <td style="text-align: right; width: 45%;"><?= "{$vta->OrigCity} ({$vta->Orig})" ?></td>
                                    <td style="text-align: center; width: 10%;"><i class="material-icons" style="vertical-align: middle; display: inline-block;">arrow_forward</i>
                                    <td style="width: 45%;"><?= "{$vta->DestCity} ({$vta->Dest})" ?></td>
                                </tr>
                            <? endforeach; ?>
                        </table>
                    </td>
                    <td class="align-middle text-center">
                        <?
                        if ($s->Valid) {
                            foreach ($s->Idas as $ida) {
                                foreach ($ida->periods as $p) {
                                    $fi = date("d/m", strtotime($p->fecha_inicio));
                                    $ff = date("d/m", strtotime($p->fecha_fin));
                                    echo "{$ida->Orig}-{$ida->Dest} {$fi} al {$ff}<br>";
                                }
                            }
                            foreach ($s->Vueltas as $vta) {
                                foreach ($vta->periods as $p) {
                                    $fi = date("d/m", strtotime($p->fecha_inicio));
                                    $ff = date("d/m", strtotime($p->fecha_fin));
                                    echo "{$vta->Orig}-{$vta->Dest} {$fi} al {$ff}<br>";
                                }
                            }
                        } else {
                            echo date("d/m/Y", strtotime($s->FDesde)) . " - " . date("d/m/Y", strtotime($s->FHasta));
                            //echo "<b>NO DISPONIBLE</b>";
                        }
                        ?>
                    </td>
                    <td class="align-middle text-center">
                        <? if ($s->Valid && $s->FDesde != null) : ?>
                            <a class="btn bg-orange waves-effect btn-xs " href="<?= base_url() . "view/results/" . $s->Search_Id  . "/trip" ?>">
                                <i class="material-icons">visibility</i>
                            </a>
                        <? endif; ?>
                        <? if ($s->HasView == 0 || $rol_id == 1) : ?>
                            <a class="btn bg-orange waves-effect btn-xs " href="<?= base_url() . "smiles?s=" . $s->Search_Id ?>">
                                <i class="material-icons">search</i>
                            </a>
                        <? endif; ?>
                        <? if ($rol_id == 1) :
                            $bg = $s->HasView ? "bg-pink" : "bg-blue"; ?>
                            <button type="button" class="btn <?= $bg ?> waves-effect btn-xs btn-addview" data-value="<?= $s->Search_Id ?>" data-view="<?= $s->HasView ?>">
                                <i class="material-icons"><?= $s->HasView ? "remove_circle_outline" : "group_add" ?></i>
                            </button>
                            <button type="button" class="btn bg-grey waves-effect btn-xs btn-invalidate" data-value="<?= $s->Search_Id ?>">
                                <i class="material-icons">highlight_off</i>
                            </button>
                            <button type="button" class="btn bg-green waves-effect btn-xs btn-extend" data-value="<?= $s->Search_Id ?>">
                                <i class="material-icons">refresh</i>
                            </button>
                        <? endif; ?>
                        <? if ($rol_id == 1 || $s->HasView == 0) : ?>
                            <button type="button" class="btn bg-red waves-effect btn-xs btn-delete" data-value="<?= $s->Search_Id ?>">
                                <i class="material-icons">delete</i>
                            </button>
                        <? endif; ?>
                    </td>
                </tr>
            <? endforeach ?>
        </tbody>
    </table>
<? else : ?>
    <h5 class="alert alert-info">No se han realizado búsquedas todavía. Haga clic en NUEVA BÚSQUEDA para buscar</h5>
<? endif; ?>