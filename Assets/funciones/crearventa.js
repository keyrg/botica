var tabla;
function init()
{
	fecha_actual();
	mostrar_impuesto();
	list_produc();
	nombre_impuesto();
	ShowComprobante();
	cart();
	Cliente();
	Comprobante();

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
			url:base_url+"/Ajax/ventasAjax.php?op=listProduct",
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
function fecha_actual(){
	var now = new Date();
	var day =("0"+now.getDate()).slice(-2);
	var month=("0"+(now.getMonth()+1)).slice(-2);
	var today=now.getFullYear()+"-"+(month)+"-"+(day);
	$("#fecha").val(today);
}
/*=============================================================================
=============================================================================*/
function Cliente(){
	$.post(base_url+"/Ajax/ventasAjax.php?op=selectCliente", function(r){
		$("#cliente").html(r);
		$("#cliente").select2(
			{theme: 'bootstrap4'}
		);
	});
}
/*=============================================================================
=============================================================================*/
function Comprobante(){
	$.post(base_url+"/Ajax/ventasAjax.php?op=selectComprobante", function(r){
		$("#tipo_comprobante").html(r);
		$("#tipo_comprobante").select2(
			{theme: 'bootstrap4'}
		);
	});
}
/*=============================================================================
=============================================================================*/
function ShowComprobante(){
	mostrar_impuesto();	
	serie_comp();
	numero_comp();
}
/*=============================================================================
=============================================================================*/
function serie_comp(){
	var tipo_comprobante = $("#tipo_comprobante").val();
	if(tipo_comprobante == null )
	{
		tipo_comprobante = 'UjBSNEpBQTZXN2l2RkttUndVL3g5Zz09'
	}

	$.post(base_url+"/Ajax/empresaAjax.php?op=mostrar_serie",{tipo_comprobante : tipo_comprobante},
		function(data,status)
		{
			data=JSON.parse(data);
			$("#serie_comprobante").val(data.comprobante_letraSerie + ('000' + data.comprobante_serie).slice(-3) ); 
		});
}
/*=============================================================================
=============================================================================*/
function numero_comp(){
	var tipo_comprobante = $("#tipo_comprobante").val();
	if(tipo_comprobante == null )
	{
		tipo_comprobante = 'UjBSNEpBQTZXN2l2RkttUndVL3g5Zz09'
	}
	$.ajax({
		url: base_url+"/Ajax/empresaAjax.php?op=mostrar_numero",
		data:{tipo_comprobante:tipo_comprobante},
		type:'get',
		dataType:'json',
		success: function(d){
			num_comp=d.numero;
			$("#num_comprobante").val( ('0000000' + num_comp).slice(-7) ); // "0001"
			$("#nFacturas").html( ('0000000' + num_comp).slice(-7) ); // "0001"
		}
	});
}
/*=============================================================================
=============================================================================*/
no_aplica=0;
/*=============================================================================
=============================================================================*/
function mostrar_impuesto(){

	$.ajax({
	url: base_url+"/Ajax/empresaAjax.php?op=mostrar_impuesto",
	type:'get',
	dataType:'json',
	success: function(i){
		 impuesto=i.empresa_impuestoValor;
		 sin_imp=0;
		 var checkbox=document.querySelector('#aplicar_impuesto');
		 checkbox.addEventListener('change', verificarEstado, false);
		 function verificarEstado(e){
		 if (e.target.checked) {
		 	$("#impuesto").val(impuesto);
		 	no_aplica=impuesto;
		 	calculateTotals();
		 	nombre_impuesto();
		 	
		 }else{
		 	$("#impuesto").val(sin_imp);
		 	no_aplica=0;
		 	calculateTotals();
		 	nombre_impuesto();
		 }
		}

	}

	});
}
/*=============================================================================
=============================================================================*/
var cont=0;
var details=0;
$("#btnRegistrar").hide();
/*=============================================================================
=============================================================================*/
function addDetail(id,producto,concentracion,adicional,precio,cantidad){
	var stock=cantidad;
	var numero_cantidad=1;
	var descuento=0;
	var price = precio;

	if (id!="") {
		var subtotal=cantidad*price;
		var fila='<tr class="filas" id="fila'+cont+'">'+
		'<td class="text-center"><div class="btn-group"><button type="button" class="btn btn-danger btn-sm" href="#" onclick="deleteDetail('+cont+')"><i class="fa fa-trash fa-xs"></i></button></div></td>'+
        '<td><input type="hidden" name="prod_id[]" value="'+id+'">'+producto+' '+concentracion+' '+adicional+'</td>'+
		'<td class="text-center"><input class="form-control text_fondo cantidad" type="number" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" min="1" max="'+stock+'" onchange="ver_stock(this.value,'+stock+')" name="cantidad[]" id="cantidad[]" value="'+numero_cantidad+'"></td>'+
        '<td class="text-center"><input class="form-control text_fondo" type="text" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="modifySubtotals()" name="precio_venta[]" id="precio_venta[]" step="0.01" value="'+price+'"></td>'+
        '<td class="text-center"><input class="form-control text_fondo" type="number" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="modifySubtotals()" name="descuento[]" step="0.01" value="'+descuento+'"></td>'+
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
function addDetailcart(id,producto,concentracion,adicional,precio,cantidad,data){
	var stock=cantidad;
	var numero_cantidad=1;
	var descuento=0;
	var price = precio;
	if(typeof precio === 'string'){
		let lol = precio.slice(3, precio.length);
		price = lol;
	}

	if (id!="") {
		var subtotal=cantidad*price;
		var fila='<tr class="filas" id="fila'+cont+'">'+
		`<td class="text-center"><div class="btn-group"><button type="button" name='${JSON.stringify(data)}' class="btn btn-danger btn-sm" href="#" onclick="deleteDetailCart(${cont}, this)"><i class="fa fa-trash fa-xs"></i></button></div></td>`+
        '<td><input type="hidden" name="prod_id[]" value="'+id+'">'+producto+' '+concentracion+' '+adicional+'</td>'+
		'<td class="text-center"><input class="form-control text_fondo cantidad" type="number" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" min="1" max="'+stock+'" onchange="ver_stock(this.value,'+stock+')" name="cantidad[]" id="cantidad[]" value="'+numero_cantidad+'"></td>'+
        '<td class="text-center"><input class="form-control text_fondo" type="text" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="modifySubtotals()" name="precio_venta[]" id="precio_venta[]" step="0.01" value="'+price+'"></td>'+
        '<td class="text-center"><input class="form-control text_fondo" type="number" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" onchange="modifySubtotals()" name="descuento[]" step="0.01" value="'+descuento+'"></td>'+
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
function ver_stock(valor,cantidad){
	//alert(cantidad);
	
	valor = parseInt(valor);
	if (valor>cantidad) {
		Swal.fire({
			title: 'Error!',
			text: 'La cantidad ingresada supera el stock',
			icon: 'error',
			showConfirmButton: false,
			timer: 1500
		});	
		 $("#btnRegistrar").hide();
	}else{
	$("#btnRegistrar").show();
	modifySubtotals();
    }

}
/*=============================================================================
=============================================================================*/
function modifySubtotals(){
	var cant=document.getElementsByName("cantidad[]");
	var prev=document.getElementsByName("precio_venta[]");
	var desc=document.getElementsByName("descuento[]");
	var sub=document.getElementsByName("subtotal");


	for (var i = 0; i < cant.length; i++) {
		var inpV=cant[i];
		var inpP=prev[i];
		var inpS=sub[i];
		var des=desc[i];


		inpS.value=(inpV.value*inpP.value)-des.value;
		document.getElementsByName("subtotal")[i].innerHTML=inpS.value.toFixed(2);
	}

	calculateTotals();

}
/*=============================================================================
=============================================================================*/
function calculateTotals(){

	var sub = document.getElementsByName("subtotal");
	var total=0.0;
	var simbolo="";
	buttonSwitch()

	if(sub.length){
	for (var i = 0; i < sub.length; i++) {
		total += document.getElementsByName("subtotal")[i].value;
		var igv=total*(no_aplica/100);
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
			 $("#total_venta").val(total_monto.toFixed(2));
		
			 $("#most_total").html(simbolo + total_monto.toFixed(2));
			 $("#most_imp").html(simbolo + igv_dec);
			 var tpagado=$("#tpagado").val();
			 var totalvuelto=0;
			 
			if (tpagado>0) {
					totalvuelto=tpagado-total_monto;
					 $("#vuelto").html(simbolo +' '+ totalvuelto.toFixed(2));
		
			}else{
				totalvuelto=0.0;
					$("#vuelto").html(simbolo +' '+ totalvuelto.toFixed(2));
			}
				 
			evaluate();
		}
	});}
		$("#total").html('0.00')
		$("#total_venta").val('0.00')
		$("#most_imp").html('0.00')
		$("#most_total").html('0.00')
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
		 var valor_impuesto=no_aplica;
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
	details--;
	if(details <= 0)
	{
		evaluate();
	}
}
/*=============================================================================
=============================================================================*/
function deleteDetailCart(indice, element){
	$("#fila"+indice).remove();
	calculateTotals();
	remove(element);
	details--;
	if(details <= 0)
	{
		evaluate();
	}
}
/*=============================================================================
=============================================================================*/
$(document).on('submit', '#form', function(e){
	e.preventDefault();  

	var cliente = $('#cliente').val();
	var fecha = $('#fecha').val();
	var tipo_comprobante = $('#tipo_comprobante').val();
	var serie_comprobante = $('#serie_comprobante').val();
	var num_comprobante = $('#num_comprobante').val();
	var impuesto = $('#impuesto').val();
	if(cliente != '0' && fecha != '' && tipo_comprobante != '' && serie_comprobante != '' && num_comprobante !='' && impuesto != ''){

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
				console.log(data);
				const json = JSON.parse(data)
				
				$("#ajaxAnswer").html(json['alert']);
				list_produc();
				if(json['comprobante']=="3"){
					//if(json.id[0].length){
						window.open(base_url+'/Reports/ticket.php?id='+json['id'], '_blank');
						cleanCart();
					//}
				}else{
					//if(json.id[0].length){
						window.open(base_url+'/Reports/voucher.php?id='+json['id'], '_blank');
						cleanCart();
					//}
				} 
				
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
function cleanCart() {
	$.ajax({
	  url: base_url + "/Ajax/cartAjax.php?op=clean",
	  type: "get",
	  dataType: "json"
	});
}
/*=============================================================================
=============================================================================*/
$(document).on('submit', '#formCliente', function(e){
	e.preventDefault();  

	var nombre = $('#nombre').val();
	var dni = $('#dni').val();
	var direccion = $('#direccion').val();
	var celular = $('#celular').val();
	var correo = $('#correo').val();


	if(nombre != '' && dni != '' && direccion != '' && celular != '' && correo != ''){

		var form=$($("#formCliente")[0]);

		var method=form.attr('method');
		var action=form.attr('action');
		
		var msjError="<script>swal(title: 'Ocurrió un error inesperado',text: 'Por favor recarga la página',icon: 'error',showConfirmButton: false,timer: 1500);</script>";

		var formdata = new FormData($("#formCliente")[0]);

		$.ajax({
			type: method,
			url: action,
			data: formdata ? formdata : form.serialize(),
			cache: false,
			contentType: false,
			processData: false,
			success: function (data) {
				$('#Modalcliente').modal('hide');
				fecha_actual();
				serie_comp();
				numero_comp();
				Cliente();

		
				$("#msjCliente").html(data);
			},
			error: function(){
				$('#Modalcliente').modal('hide');
				$("#msjCliente").html(msjError);
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
function buttonSwitch(){
	$('tbody tr[role="row"] #btnAgregarP').prop("disabled",false);
	for (let i = 0; i < $('.filas').length; i++) {
		const element = $('input[name="prod_id[]"]').get(i)
		for (let f = 0; f < $('tbody tr[role="row"]').length; f++) {
			const button = $('tbody tr[role="row"] #btnAgregarP').get(f)
			if(button['name'] === element['value']){
				button['disabled'] = true
			}
		}
	}
}
/*=============================================================================
=============================================================================*/
$('body').barcodeListener().on('barcode.valid', function(e, code){
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
				})
		
			}else{
				if(resp[0].cantidad !== null || resp[0].cantidad !== 0){
					for (let i = 0; i < $('.filas').length; i++){
						const element = $('input[name="prod_id[]"]').get(i)
						
						if(element['value'] === resp[0].id)
						{
							Swal.fire({
								title: 'Oops',
							text: 'Este producto ya esta agregado',
							icon: 'info',
							showConfirmButton: false,
							timer: 1500
							})
							return
						}
					}
					addDetail(resp[0].id,resp[0].producto,resp[0].concentracion,resp[0].adicional,resp[0].precio,resp[0].cantidad)
					return
				}else{ 
					Swal.fire({
					title: 'Oops',
					text: '¡No hay stock de este producto!',
					icon: 'info',
					showConfirmButton: false,
					timer: 1500
					})
				}		
			}
		}
	})
})
/*=============================================================================
=============================================================================*/
function cart(){
    $.ajax({
        url: base_url + "/Ajax/cartAjax.php?op=list",
        type: "get",
        success: function (resp) {
            const productos = JSON.parse(resp)
            productos.forEach(obj => {
              if(obj !== null){
				  const item = JSON.parse(obj)
				  addDetailcart(item.id,item.producto,item.concentracion,item.adicional,item.precio,item.stock,item)
                }
              });
        },
      });
}
/*=============================================================================
=============================================================================*/
function remove(element){
	$.ajax({
	  url: base_url + "/Ajax/cartAjax.php?op=remove",
	  data: { element: element['name'] },
	  type: "post",
	  dataType: "json",
	  success: function(resp){
	  }
	});
  
}
/*=============================================================================
=============================================================================*/
$('#btnBuscar').click(function(){
	buttonSwitch()
});
/*=============================================================================
=============================================================================*/
init();