var tabla;
function init()
{
	showform(false);
	list();
	Proveedor();
}
function showform(flag)
{
	clean();
	if(flag){
        $("#tabla_compras").hide();
		$('#btnShows').hide();
		$('.card-title').html('<i class="fa fa-cart-plus mr-1"></i>Registrar compra');
		$('#btnRegistrar').hide();
		$("#btnBuscar").show();
		$("#btnCancel").show();
		$("#formulario_compras").show();
		details=0;
		list_produc();
	}else{
		$('#btnShows').show();
		$('.card-title').html('<i class="fa fa-list mr-1"></i>Lista de compras');
		$("#tabla_compras").show();
		$("#formulario_compras").hide();
	}
}
function clean(){
	$("#form").trigger("reset");
	$("#tipo_documento").val("Boleta");
	$(".filas").remove();
	$("#total").html('0.00')
	$("#total_buy").val('0.00')
	$("#most_total").html('0.00')
	$("#most_imp").html('0.00')
	$("#valor_impuesto").html("IGV 0%");

	//obtenemos la fecha actual
	var now = new Date();
	var day =("0"+now.getDate()).slice(-2);
	var month=("0"+(now.getMonth()+1)).slice(-2);
	var today=now.getFullYear()+"-"+(month)+"-"+(day);
	$("#fecha").val(today);
}
/*=============================================================================
=============================================================================*/
function hideform(){
	clean();
	showform(false);
}
/*=============================================================================
=============================================================================*/
function list_produc(){
	
	tabla=$('#productos').dataTable({
		"aProcessing": true,
		"aServerSide": true,
		dom: 'Bfrtip',
		"ajax":
		{
			url:base_url+"/Ajax/comprasAjax.php?op=listProduct",
			type: "GET",
			dataType : "json",
			error:function(e){
				console.log(e.responseText);
			}
		},
		"bDestroy":true,
        "ordering": false,
        "info": true,
		"iDisplayLength":6,
		"order":[[0,"desc"]],
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad"
            }
        }
	}).DataTable();
}
/*=============================================================================
=============================================================================*/
function Proveedor(){
	$.post(base_url+"/Ajax/comprasAjax.php?op=selectPoveedor", function(r){
		$("#proveedor").html(r);
		$("#proveedor").select2(
			{theme: 'bootstrap4'}
		);
    });
}
/*=============================================================================
=============================================================================*/
var tax=18;
var cont=0;
var details=0;
$("#btnRegistrar").hide();
$("#tipo_documento").change(markTax);
/*=============================================================================
=============================================================================*/
function markTax(){
	var typeDocument=$("#tipo_documento option:selected").text();
	if (typeDocument=='Factura') {
		$("#igv").val(tax);
		modifySubtotals();
	}else{
		$("#igv").val("0");
		modifySubtotals();
	}
}
/*=============================================================================
=============================================================================*/
function addDetail(prod_id,prod_nombre,prod_concentracion,prod_adicional){

	var quantity=1;
	var purchase_price=1;
	//obtenemos la fecha actual
	var now = new Date();
	var day =("0"+now.getDate()).slice(-2);
	var month=("0"+(now.getMonth()+1)).slice(-2);
	var today=now.getFullYear()+"-"+(month)+"-"+(day);

	if (prod_id!="") {
		var subtotal=quantity*purchase_price;
		var fila='<tr class="filas" id="fila'+cont+'">'+
		'<td class="text-center"><div class="btn-group"><button type="button" class="btn btn-danger btn-sm" href="#" onclick="deleteDetail('+cont+')"><i class="fa fa-trash fa-xs"></i></button></div></td>'+
        '<td><input type="hidden" name="prod_id[]" value="'+prod_id+'">'+prod_nombre+' '+prod_concentracion+' '+prod_adicional+'</td>'+
        '<td class="text-center"><input class="form-control text_fondo" type="number" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="modifySubtotals()" name="quantity[]" id="quantity[]" min="1" value="'+quantity+'"></td>'+
        '<td class="text-center"><input class="form-control text_fondo" type="number" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="modifySubtotals()" name="purchase_price[]" id="purchase_price[]" step="0.01" value="'+purchase_price+'"></td>'+
        '<td class="text-center"><input class="form-control text_fondo" style="width:100%" type="date" name="fechaV[]" value="'+today+'"></td>'+
		'<td class="text-center">'+simbolo+'<span id="subtotal'+cont+'" name="subtotal">'+subtotal+'</span></td>'+
		'</tr>';
		cont++;
		details++;
		$('#tableDetails').append(fila);
		modifySubtotals();
	}else{
		alert("Error when entering the detail, review the data of the article");
	}
}
/*=============================================================================
=============================================================================*/
function modifySubtotals(){
	var cant=document.getElementsByName("quantity[]");
	var prec=document.getElementsByName("purchase_price[]");
	var sub=document.getElementsByName("subtotal");

	for (var i = 0; i < cant.length; i++) {
		var inpC=cant[i];
		var inpP=prec[i];
		var inpS=sub[i];

		inpS.value=inpC.value*inpP.value;
		document.getElementsByName("subtotal")[i].innerHTML=inpS.value.toFixed(2);
	}

	calculateTotals();
}
/*=============================================================================
=============================================================================*/
function calculateTotals(){
	var sub = document.getElementsByName("subtotal");
	var total=0.00;
	var simbolo="";
	buttonSwitch()

	if(sub.length){
		for (var i = 0; i < sub.length; i++) {
			total += document.getElementsByName("subtotal")[i].value;
			igv=total*($("#igv").val()/100);
			var total_monto=total+igv;
			var igv_dec=igv.toFixed(2);
		}

		$.ajax({
			url: base_url+"/Ajax/empresaAjax.php?op=mostrar_simbolo",
			type:'get',
			dataType:'json', 
			success: function(sim){
				simbolo=sim.empresa_simbolo;
				$("#total").html(simbolo +' '+ total.toFixed(2)); 
				$("#total_buy").val(total_monto.toFixed(2));
				$("#most_total").html(simbolo + total_monto.toFixed(2));
				$("#most_imp").html(simbolo + igv_dec);
	
				evaluate();
			}
		});
		nombre_impuesto();
	}else{
		$("#total").html('0.00')
		$("#total_buy").val('0.00')
		$("#most_total").html('0.00')
		$("#most_imp").html('0.00')
		$("#valor_impuesto").html("IGV 0%");
		/*$("#igv").val("0");
		$("#tipo_documento").val("Boleta");*/
	}
}
/*=============================================================================
=============================================================================*/
function nombre_impuesto(){
	$.ajax({
	url: base_url+"/Ajax/empresaAjax.php?op=nombre_impuesto",
	type:'get',
	dataType:'json',
	success: function(n){
		 nomp=n.empresa_impuesto;
		 var valor_impuesto=$("#igv").val();
		 $("#valor_impuesto").html(nomp+' '+ valor_impuesto +"%");
			 
		}
	});
}
/*=============================================================================
=============================================================================*/
function evaluate(){

	if (details>0)
	{
		$("#btnRegistrar").show();
	}
	else
	{
		$("#btnRegistrar").hide();
		cont=0;
	}
}
/*=============================================================================
=============================================================================*/
function deleteDetail(indice){
	$("#fila"+indice).remove();
	calculateTotals();
	details=details-1;
	if(details <= 0)
	{
		evaluate();
	}	
}
/*=============================================================================
=============================================================================*/
function list(){
	tabla=$('#tablaRespuesta').dataTable({
		"aProcessing": true,
		"aServerSide": true,
		dom: 'Bfrtip',
		"ajax":
		{
			url:base_url+"/Ajax/comprasAjax.php?op=list",
			type: "get",
			dataType : "json",
			error:function(e){
				console.log(e.responseText);
			}
		},
		"paging": true,
        "lengthChange": false,
        "autoWidth": false,
        "responsive": true,
		"bDestroy":true,
        "ordering": false, 
        "info": true,
		"iDisplayLength":6,
		"order":[[0,"desc"]],
        "language": {
            "sProcessing":     "Procesando...",
            "sLengthMenu":     "Mostrar _MENU_ registros",
            "sZeroRecords":    "No se encontraron resultados",
            "sEmptyTable":     "Ningún dato disponible en esta tabla",
            "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
            "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
            "sInfoPostFix":    "",
            "sSearch":         "Buscar:",
            "sUrl":            "",
            "sInfoThousands":  ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst":    "Primero",
                "sLast":     "Último",
                "sNext":     "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            },
            "buttons": {
                "copy": "Copiar",
                "colvis": "Visibilidad"
            }
        }
	}).DataTable();
}
/*=============================================================================
=============================================================================*/
$(document).on('submit', '#form', function(e){
	e.preventDefault();  

	var proveedor = $('#proveedor').val();
	var fecha = $('#fecha').val();
	var serie = $('#num_serie').val();
	var tipo_documento = $('#tipo_documento').val();
	var num_documento = $('#num_documento').val();
	var igv = $('#igv').val();
	if(proveedor != '0' && fecha != '' && tipo_documento != '' && num_documento != '' && serie !='' && igv != ''){

		var form=$($("#form")[0]);

		var method=form.attr('method');
		var action=form.attr('action');
		
		var msjError="<script>swal(title: 'Ocurrió un error inesperado',text: 'Por favor recarga la página',icon: 'error',showConfirmButton: false,timer: 1500);</script>";

		var formdata = new FormData($("#form")[0]);

		$.ajax({
			type: method,
			url: action,
			data: formdata ? formdata : form.serialize(),
			cache: false,
			contentType: false,
			processData: false,
			success: function (data) {
				$("#ajaxAnswer").html(data);
				showform(false);
				list();
			},
			error: function(){
				$("#ajaxAnswer").html(msjError);
			}       
		});

	}else{
		Swal.fire({
			title: 'Oops',
			text: 'Por favor,rellene todos los campos',
			icon: 'info',
			showConfirmButton: false,
			timer: 1500
		});			
	}
}); 
/*=============================================================================
=============================================================================*/
$(document).on("click", ".anular", function () {
	var compra_id = $(this).attr("id");
	$.ajax({
	  url: base_url + "/Ajax/comprasAjax.php?op=anular",
	  method: "POST",
	  data: { compra_id: compra_id },
	  success: function (data) {
		$("#ajaxAnswer").html(data);
		list();
	  },
	});
});
/*=============================================================================
=============================================================================*/
$(document).on('click', '.edit', function(){
	var compra_id = $(this).attr("id");
	$.ajax({
		url: base_url + "/Ajax/comprasAjax.php?op=show",
		method:"POST",
		data:{compra_id:compra_id},
		dataType:"json",
		success:function(data) 
		{
			$("#detalle_compra").modal('show');
			$('#idcompra').val(data.compra_id);
			$('#proveedorm').val(data.proved_nombre);

			$('#fecha_horam').val(data.compra_fecha);
			$('#tipo_comprobantem').val(data.compra_tipoComprobante);
			$('#serie_comprobantem').val(data.compra_serie);
			$('#num_comprobantem').val(data.compra_numComprobante);
			$('#impuestom').val(data.compra_impuesto);


		}
	});
	$.post(base_url + "/Ajax/comprasAjax.php?op=showDetail&id="+compra_id,function(r){
		$("#detallesm").html(r);
	});
});
/*=============================================================================
=============================================================================*/
function buttonSwitch(){
	$('#content tr[role="row"] #btnAgregarP').prop("disabled",false);
	for (let i = 0; i < $('.filas').length; i++) {
		const element = $('input[name="prod_id[]"]').get(i)
		for (let f = 0; f < $('#content tr[role="row"]').length; f++) {
			const button = $('#content tr[role="row"] #btnAgregarP').get(f)
			if(button['name'] === element['value']){
				button['disabled'] = true
			}
		}
	}
}
/*=============================================================================
=============================================================================*/
$('body').scannerDetection({
  timeBeforeScanTest: 200, // tiempo para detectar un código completo
  avgTimeByChar: 40, // promedio de tiempo entre caracteres
  preventDefault: true,
  endChar: [13], // Enter
  onComplete: function(code){
    // Cuando el escáner lee correctamente:
    $.ajax({
      url: base_url+"/Ajax/ventasAjax.php?op=barcode",
      data:{barcode:code},
      type:'post',
      dataType:'json',
      success: function(resp){
        if(resp.length === 0){
          Swal.fire({
            title: 'Oops',
            text: 'El producto no existe',
            icon: 'info',
            showConfirmButton: false,
            timer: 1500
          });
        } else {
          if(resp[0].cantidad !== null || resp[0].cantidad !== 0){
            for (let i = 0; i < $('.filas').length; i++){
              const element = $('input[name="prod_id[]"]').get(i)
              if(element['value'] === resp[0].id){
                Swal.fire({
                  title: 'Oops',
                  text: 'Este producto ya está agregado',
                  icon: 'info',
                  showConfirmButton: false,
                  timer: 1500
                });
                return;
              }
            }
            addDetail(resp[0].id,resp[0].producto,resp[0].concentracion,resp[0].adicional,resp[0].precio,resp[0].cantidad);
          } else {
            Swal.fire({
              title: 'Oops',
              text: '¡No hay stock de este producto!',
              icon: 'info',
              showConfirmButton: false,
              timer: 1500
            });
          }
        }
      }
    });
  }
});

/*=============================================================================
=============================================================================*/
init();