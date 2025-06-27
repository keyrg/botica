<?php
    if ($_SESSION['type_str']!=="Administrador") {
        echo $lc->force_logoff_controller();
    }
?>
<!-- Vista Compania -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h6 class="header-title">Gestionar empresa</h6>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item breadcrumb-title"><a href="<?= base_url() ?>/dashboard/">Inicio</a></li>
          <li class="breadcrumb-item active breadcrumb-title">empresa</li>
        </ol>
      </div>
    </div>
  </div>
</section>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fa fa-list mr-1"></i>datos de ela empresa
                        </h3>
                    </div>
                    <div id="tabla_empresa" class="card-body p-10">
                        <table id="tablaRespuesta" class="table table-bordered table-hover m-0">
                            <thead>
                            <tr>
                                <th>Acción</th>
                                <th>Logo</th>
                                <th>Nombre</th>
                                <th>R.U.C</th>
                                <th>Celular</th>
                                <th>Dirección</th>
                                <th>Impuesto</th>
                                <th>Moneda</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div id="formulario_empresa" class="card-body p-10">
                        <div class="row">
                            <div class="col-sm-12">
                                <form action="<?= base_url(); ?>/Ajax/empresaAjax.php?op=update" name="form" id="form" method="post" autocomplete="off">
                                    <div class="row">
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="">Nombre<small> (*)</small></label>
                                            <input class="form-control" type="text" name="empresa_id" id="empresa_id" value="2">
                                            <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Nombre">
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="">R.U.C<small> (*)</small></label>
                                            <input class="form-control" type="text" name="ruc" placeholder="RUC" id="ruc" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                                        </div>
                                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                            <label for="">Celular<small> (*)</small></label>
                                            <input class="form-control" type="text" name="celular" id="celular" placeholder="Celular" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="">Dirección(*):</label>
                                            <input class="form-control" type="text" name="direccion" id="direccion" maxlength="256" placeholder="Dirección">
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="">Correo<small> (*)</small></label>
                                            <input class="form-control" type="email" name="correo" id="correo">
                                        </div>
                                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                                            <label for="">Datos Financieros</label>
                                        </div>
                                        <div class="form-group col-lg-3 col-md-3 col-xs-12">
                                            <label for="">Nombre Imp<small> (*)</small></label>
                                            <input class="form-control" type="text" name="nombre_impuesto" id="nombre_impuesto" placeholder="IGV">
                                        </div>
                                        <div class="form-group col-lg-3 col-md-3 col-xs-12">
                                            <label for="">Monto<small> (%)</small></label>
                                            <input class="form-control" type="text" name="monto_impuesto" id="monto_impuesto" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
                                        </div>
                                        <div class="form-group col-lg-3 col-md-3 col-xs-12">
                                            <label for="">Moneda<small> (*)</small></label>
                                            <input class="form-control" type="text" name="moneda" id="moneda" placeholder="SOLES - Dolares">
                                        </div>
                                        <div class="form-group col-lg-3 col-md-3 col-xs-12">
                                            <label for="">Simbolo<small> (*)</small></label>
                                            <input class="form-control" type="text" name="simbolo" id="simbolo" placeholder="s/ - $">
                                        </div>
                                        <div class="col-xs-12 col-sm-12 mt-3">
                                            <button type="submit" id="btnRegistrar" class="btn btn-info btn-sm">Guardar cambios</button>
                                            <button type="button" id="btnCancel" class="btn btn-danger btn-sm" onclick="hideform()" id="btnHide">Cancelar</button>
                                        </div>
                                    </div>         
                                </form>
                                <div id="ajaxAnswer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> 
<!-- /.Vista Compania -->
<!-- Modal Logo  -->               
<div class="modal fade" id="logo">
    <div class="modal-dialog modal-sm">
        <form action="<?= base_url(); ?>/Ajax/empresaAjax.php?op=addLogo" name="formLogo" id="formLogo" method="post" autocomplete="off" enctype="multipart/form-data">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 id="logo_titulo" class="modal-title" ></h6>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="form-group">
                                <img id="logo_actual" src="" class="img-thumbnail" style="width: 100%;height:200px" id="photoView">
                                <label class="photoLabel" for="photo">
                                    <i class="far fa-image"></i>
                                        Cambiar Logo 
                                </label>
                                <input type="file" id="photo" class="photo" name="photo">
                                <input type="text" id="id_logo_pro" name="id_logo_pro">
                                <input type="text" id="nombre_empresa" name="nombre_empresa">
                                <input type="text" id="avatar" name="avatar">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-info">Guardar cambios</button>
                </div>
            </div>
        </form>
        <div id="msjLogo"></div>
    </div>
</div>
<!-- /.Modal Logo -->
<script src="<?= media(); ?>/funciones/empresa.js"></script>