var tabla;
function init()
{
	showform(false); 
	listar();
	hidden();
}
/*=============================================================================
=============================================================================*/
function showform(flag)
{
	clean();
	if(flag){
        $("#tabla_laboratorio").hide();
		$('#btnShows').hide();
		$('.card-title').html('<i class="fa fa-flask mr-1"></i>Registrar laboratorio');
        $("#formulario_laboratorio").show();
	}else{
		$('#btnShows').show();
		$('.card-title').html('<i class="fa fa-list mr-1"></i>Lista de laboratorios');
		$("#tabla_laboratorio").show();
		$("#formulario_laboratorio").hide();
	}
}
/*=============================================================================
=============================================================================*/
function hidden(){
	$('#lab_id').hide();
}
/*=============================================================================
=============================================================================*/
function clean()
{
    $('#form')[0].reset();
}
/*=============================================================================
=============================================================================*/
function hideform()
{
	clean();
	showform(false);
}
/*=============================================================================
=============================================================================*/
function listar(){
	tabla=$('#tablaRespuesta').dataTable({
		"aProcessing": true,
		"aServerSide": true,
		dom: 'Bfrtip',
		"ajax":
		{
			url:base_url+'/Ajax/laboratorioAjax.php?op=list',
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

	var laboratorio = $('#laboratorio').val();

	if(laboratorio != ''){

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
				tabla.ajax.reload();
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
$(document).on('click', '.delete', function(){
	var lab_id = $(this).attr("id");
	Swal.fire({
		title: "Eliminar",
		text: "¿Seguro que desea eliminar?",
		icon: "warning",
		showCancelButton: true,
		confirmButtonColor: '#7cd1f9',
		cancelButtonColor: '#f27474',
		confirmButtonText: 'Aceptar',
		cancelButtonText: ' Cancelar'
	}).then(result => {
		if (result.value) {
			$.ajax({
				url:base_url+"/Ajax/laboratorioAjax.php?op=delete",
				method:"POST",
				data:{lab_id:lab_id},
				success:function(data)
				{
					$("#ajaxAnswer").html(data);
					tabla.ajax.reload();
				}
			});
		}else{
			Swal.fire("Cancelado!", "Operacion cancelada", "error");
		}
	});
});
/*=============================================================================
=============================================================================*/
$(document).on('click', '.edit', function(){
	var lab_id = $(this).attr("id");
	$.ajax({
		url:base_url+"/Ajax/laboratorioAjax.php?op=show",
		method:"POST",
		data:{lab_id:lab_id},
		dataType:"json",
		success:function(data)
		{
			showform(true);
			$('.card-title').html('<i class="fa fa-flask mr-1"></i>Actualizar laboratorio');
			$('#lab_id').val(data.lab_id);
			$('#laboratorio').val(data.lab_nombre);
	
		}
	})
});
/*=============================================================================
=============================================================================*/
init();