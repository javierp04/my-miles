var is_dialog;

            function searchDialogRange(f_sel, d_ways, info) {
                info = info.substring(0, info.indexOf('(')).trim();
                $('#searchModal').modal('show');
                $('#sp_dlg_fecha').html(f_sel);
                $('#dTitle').html(info);
                $.ajax({
                    type: 'POST',
                    data: {
                        idas: d_ways,
                        fdesde: f_sel,
                        dialog: 1,
                        is_ajax: 1
                    },
                    dataType: 'json',
                    url: '<?= base_url() . "smiles/ax_days_to_update" ?>',
                    success: function(response) {
                        if (response.nosession) {
                            showNoSessionMsg();
                            return;
                        }
                        daysToUpdate = response.daysToUpdate;
                        totalSearch = daysToUpdate.length;
                        currSearch = 0;
                        searchCount = 0;
                        lastDate = daysToUpdate[daysToUpdate.length - 1].Fecha;
                        dd = moment(f_sel).format("DD/MM/YYYY");
                        hh = moment(lastDate).format("DD/MM/YYYY");

                        info_new = info + "<br>" + dd + " al " + hh + "</h5>";

                        $('#dTitle').html(info_new);
                        updateProgressDialog();
                        if (totalSearch > 0) {
                            is_dialog = 1;
                            stopped = false;
                            readResults();
                        }
                    }
                });
            }

            function updateProgressDialog() {
                percent = searchCount > 0 ? Math.ceil(searchCount / totalSearch * 100) : "0";
                if (searchCount == totalSearch) {
                    stopped = true;
                    $("#searchModal").modal('hide');
                    swal({
                        title: "Búsqueda Actualizada",
                        text: "Se han actualizado las búsquedas para el rango seleccionado.",
                        icon: "info"
                    });
                } else {
                    $('#divDBar').attr('aria-valuenow', percent);
                    $("#divDBar").css("width", percent + "%");
                    var toSearch = daysToUpdate[searchCount];
                    var currentDate = new Date(toSearch.Fecha);
                    currentDate.setHours(currentDate.getHours() + 19);
                    var dia = currentDate.getDate();
                    var mes = currentDate.getMonth() + 1; // Nota: los meses comienzan desde 0, por lo que sumamos 1
                    var fechaFormateada = (dia < 10 ? '0' : '') + dia + '/' + (mes < 10 ? '0' : '') + mes;
                    $('#spDProgress').text(fechaFormateada + ' (' + percent + '%)');
                    $("#spDCurr").html(toSearch.Orig + "-" + toSearch.Dest + " " + fechaFormateada);
                }
            }


            //POPULATE SEARCH DE ADMIN ANTERIOR
            if ($s->HasView || $rol_id == 1) {
				//SI ES VIEW O ES ADMINISTRADOR PREPARA EL LISTADO DE ESTA MANERA				
				$p = $this->getAdminLongestPeriod($c->Orig, $c->Dest);
				$s->Valid = false;
				if ($p != null) {
					$s->Valid = true;
					if ($p->duracion < $shortest_duration) {
						$shortest_duration = $p->duracion;
						$shortest_period = $p;
					}
					if ($p->duracion > $longest_duration) {
						$longest_duration = $p->duracion;
						$longest_period = $p;
					}
					//TOMA EL PERIODO MAS CORTO COMO REFERENCIA
					$start_date = $longest_period->fecha_inicio;
					$end_date = $longest_period->fecha_fin;

					//TOMA EL PERIODO MAS LARGO
					//$start_date = $longest_period->fecha_inicio;
					//$end_date = $longest_period->fecha_fin;
					$s->Valid = $s->Valid && $p->duracion > 0;
					$fl_min_miles = $this->db->query("SELECT Min(Millas * {$mile_price} + Tasas) As MinPrice from am_flight_result 
												where Orig = '{$c->Orig}' and Dest = '$c->Dest' and Fecha >= '{$start_date}' and Fecha <= '{$end_date}'")->row();
					$fl_min_sm = $this->db->query("SELECT Min(SM * {$mile_price} + Tasas + Money) as MinSM from am_flight_result 
												where Orig = '{$c->Orig}' and Dest = '$c->Dest' and Fecha >= '{$start_date}' and Fecha <= '{$end_date}'")->row();
					$cheapest[$c->Tipo] = min($fl_min_miles->MinPrice, $fl_min_sm->MinSM, $cheapest[$c->Tipo]);
					$fl_dates = $this->db->query("SELECT Min(Fecha) as FFirst, Max(Fecha) as FLast, Min(Datelog) as LastUpdate from am_flight_result where Orig = '{$c->Orig}' and Dest = '$c->Dest' 
												and Fecha >= '{$start_date}' and Fecha <= '{$end_date}'")->row();
					$first_flight = min($fl_dates->FFirst, $first_flight);
					$last_flight = max($fl_dates->FLast, $last_flight);
					$last_update = min($fl_dates->LastUpdate, $last_update);
				}
			} else {