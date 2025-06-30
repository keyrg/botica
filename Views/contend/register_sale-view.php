<!-- Formulario Ventas -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h6 class="header-title">Gestionar venta</h6>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item breadcrumb-title"><a href="<?= base_url() ?>/dashboard/">Inicio</a></li>
          <li class="breadcrumb-item active breadcrumb-title">venta</li>
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
              <i class="fa fa-cart-plus mr-1"></i>Registrar venta
            </h3>
          </div>
          <div id="formulario_ventas" class="card-body p-10">
            <div class="row">
              <div class="col-sm-12">
                <form action="<?= base_url(); ?>/Ajax/ventasAjax.php?op=add" name="form" id="form" method="post" autocomplete="off">
                  <div class="row">
                    <div class="col-xs-12 col-sm-8">
                      <div class="form-group">
                        <label for="" class="label-control">Cliente<small> (*)</small></label>
                        <div class="input-group">
                          <select name="cliente" class="form-control select2" style="width: 100%;" id="cliente" readonly></select>
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                      <div class="form-group">
                        <label for="" class="label-control">Fecha<small> (*)</small></label>
                        <div class="input-group">
                          <input type="text" class="form-control" name="fecha" id="fecha" readonly>
                          <div class="input-group-append">
                            <span class="btn btn-info">
                              <i class="fas fa-calendar fa-xs"></i>
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                      <div class="form-group">
                        <label for="" class="label-control">Tipo de comprobante<small> (*)</small></label>
                        <select onchange="ShowComprobante()" name="tipo_comprobante" id="tipo_comprobante" class="form-control select2" style="width: 100%;"></select>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-2">
                      <div class="form-group">
                        <label for="" class="label-control">Serie<small> (*)</small></label>
                        <input class="form-control" type="text" name="serie_comprobante" id="serie_comprobante" maxlength="7" readonly required>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-4">
                      <div class="form-group">
                        <label for="" class="label-control">Número<small> (*)</small></label>
                        <input class="form-control" type="text" name="num_comprobante" id="num_comprobante" maxlength="10" readonly required>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-2">
                      <div class="form-group">
                        <label for="" class="label-control">Impuesto<small> (%)</small></label>
                        <div class="input-group">
                          <div class="input-group-append">
                            <label for="aplicar_impuesto" class="btn btn-info">
                              <input class="flat-red" type="checkbox" name="aplicar_impuesto" id="aplicar_impuesto">
                            </label>
                          </div>
                          <input type="text" class="form-control" name="impuesto" id="impuesto" value="0" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" readonly>
                        </div>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-12">
                      <div class="form-group">
                        <button id="btnBuscar" type="button" class="btn btn-info btn-icon-split btn-sm" data-toggle="modal" data-target="#lista_productos">
                          <span class="icon"><i class="fa fa-search fa-xs"></i></span>
                          <span class="text">Buscar producto</span>
                        </button>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-12">
                      <div class="table-responsive">
                        <table id="tableDetails" class="table table-hover table-bordered" style="width:100%" cellspacing="0">
                          <thead class="bg-info">
                            <tr>
                              <th>ACCIÓN</th>
                              <th>DESCRIPCIÓN</th>
                              <th class="text-center">CANT.UND</th>
                              <th class="text-center">P.U</th>
                              <th class="text-center">DESCUENTO</th>
                              <th class="text-center">IMPORTE</th>
                            </tr>
                          </thead>
                          <tbody></tbody>
                        </table>
                      </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 ">
                      <ul class="nav nav-pills flex-column table-sale">
                        <li class="nav-item" style="border-top:1px solid rgba(0,0,0,.125);">
                          <a class="nav-link"><label>SUBTOTAL</label><span id="total" class="badge badge-primary float-right">0.00</span></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link"><label id="valor_impuesto">IGV 0%</label><span id="most_imp" class="badge badge-success float-right">0.00</span></a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link"><label>TOTAL</label><span id="most_total" class="badge badge-danger float-right">0.00</span>
                            <input type="hidden" step="0.01" name="total_venta" id="total_venta">
                          </a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link"><label>CANT.PAGADA</label>
                            <input class="float-right form-control text_fondo" onchange="modifySubtotals()" type="number" step="0.01" name="tpagado" value="0" id="tpagado">
                          </a>
                        </li>
                        <li class="nav-item" style="border-bottom:1px solid rgba(0,0,0,.125);">
                          <a class="nav-link"><label>VUELTO</label><span id="vuelto" class="badge badge-info float-right">0.00</span></a>
                        </li>
                      </ul>
                    </div>
                    <div class="col-xs-12 col-sm-12 mt-3">
                      <button type="submit" id="btnRegistrar" class="btn btn-info btn-sm">Guardar cambios</button>
                      <a href="<?= base_url() ?>/sales_list/" id="btnCancel" class="btn btn-danger btn-sm" onclick="hideform()">Cancelar</a>
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

<!-- Modal Productos -->
<div class="modal fade" id="lista_productos" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content">
      <div class="modal-header d-flex align-items-center">
        <h6 class="modal-title">Lista de productos</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body table-responsive">
        <table id="productos" class="table table-bordered table-hover" style="width:100%">
          <thead>
            <tr>
              <th scope="col">Imagen</th>
              <th scope="col">Descripción</th>
              <th scope="col">Stock</th>
              <th scope="col">Precio</th>
              <th scope="col">Laboratorio</th>
              <th scope="col">Categoria</th>
              <th scope="col">Presentación</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- Modal cliente -->
<div class="modal fade" id="Modalcliente" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-default">
    <form action="<?= base_url(); ?>/Ajax/clienteAjax.php?op=saveandedit" name="formCliente" id="formCliente" method="post" autocomplete="off">
      <div class="modal-content">
        <div class="modal-header">
          <h6 id="titleP" class="modal-title">Registrar Cliente</h6>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12 col-sm-12">
              <div class="form-group">
                <label for="" class="label-control">Cliente<small> (*)</small></label>
                <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre del cliente">
              </div>
            </div>
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label for="" class="label-control">Dni<small> (*)</small></label>
                <input name="dni" id="dni" type="text" class="form-control" placeholder="Dni" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
              </div>
            </div>
            <div class="col-xs-12 col-sm-6">
              <div class="form-group">
                <label for="" class="label-control">Celular<small> (*)</small></label>
                <input type="text" class="form-control" name="celular" id="celular" placeholder="Celular" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12">
              <div class="form-group">
                <label for="" class="label-control">Correo<small> (*)</small></label>
                <input type="email" class="form-control" name="correo" id="correo" placeholder="Correo">
              </div>
            </div>
            <div class="col-xs-12 col-sm-12">
              <div class="form-group">
                <label for="" class="label-control">Direccion<small> (*)</small></label>
                <input type="text" class="form-control" name="direccion" id="direccion" placeholder="Direccion">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </div>
      </div>
    </form>
    <div id="msjCliente"></div>
  </div>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 CSS (poner en tu <head> si no está) -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
<!-- Plugin que permite leer códigos de barras -->
<script src="https://unpkg.com/jquery-scanner-detection/jquery.scannerdetection.min.js"></script>

<!-- Script propio -->
<script src="<?= media(); ?>/funciones/crearventa.js"></script>