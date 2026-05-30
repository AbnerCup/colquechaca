<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Libro Diario - Registro de Ingresos y Egresos</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bs-body-font-family: 'Inter', sans-serif;
            --primary-gradient: linear-gradient(135deg, #4f46e5, #3b82f6);
            --success-gradient: linear-gradient(135deg, #10b981, #059669);
            --danger-gradient: linear-gradient(135deg, #ef4444, #dc2626);
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
        }

        .summary-card {
            border: none;
            border-radius: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            position: relative;
        }

        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }

        .summary-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0.08;
            pointer-events: none;
        }

        .card-ingresos::before {
            background: var(--success-gradient);
        }

        .card-egresos::before {
            background: var(--danger-gradient);
        }

        .card-saldo::before {
            background: var(--primary-gradient);
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .main-container {
            max-width: 1280px;
            margin: 0 auto;
        }

        .data-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }

        .table th {
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            background-color: #f1f5f9;
            color: #64748b;
            padding: 14px 16px;
        }

        .table td {
            padding: 16px;
            vertical-align: middle;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #fafbfc;
        }

        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-primary-custom {
            background: var(--primary-gradient);
            color: white;
            border: none;
        }

        .btn-primary-custom:hover {
            opacity: 0.9;
            color: white;
            transform: translateY(-1px);
        }

        .modal-content {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            border-bottom: 1px solid #f1f5f9;
            background-color: #f8fafc;
            border-top-left-radius: 16px;
            border-top-right-radius: 16px;
        }
    </style>
</head>
<body class="py-4">

    <div class="container main-container">
        
        <header class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h1 class="h2 fw-bold text-slate-800 mb-1">
                    <i class="bi bi-journal-bookmark-fill text-primary"></i> Libro Diario
                </h1>
                <p class="text-muted mb-0">Control de ingresos, egresos y saldos acumulados</p>
            </div>
            <div>
                <button type="button" class="btn btn-primary-custom px-4 py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrear">
                    <i class="bi bi-plus-circle-fill me-2"></i> Nueva Transacción
                </button>
            </div>
        </header>

        <section class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card summary-card card-ingresos h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="card-icon bg-success-subtle text-success me-3">
                            <i class="bi bi-arrow-up-circle-fill"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small fw-medium">Ingresos del Mes</span>
                            <span class="h3 fw-bold text-success mb-0" id="resumen-ingresos">Bs. 0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card summary-card card-egresos h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="card-icon bg-danger-subtle text-danger me-3">
                            <i class="bi bi-arrow-down-circle-fill"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small fw-medium">Egresos del Mes</span>
                            <span class="h3 fw-bold text-danger mb-0" id="resumen-egresos">Bs. 0.00</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card summary-card card-saldo h-100">
                    <div class="card-body d-flex align-items-center p-4">
                        <div class="card-icon bg-primary-subtle text-primary me-3">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div>
                            <span class="text-muted d-block small fw-medium">Saldo del Mes</span>
                            <span class="h3 fw-bold text-primary mb-0" id="resumen-saldo">Bs. 0.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="card data-card mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3 small text-uppercase text-secondary">Filtros de Búsqueda</h5>
                <form id="form-filtros" class="row g-3">
                    <div class="col-12 col-md-4">
                        <label for="filtro-search" class="form-label small fw-semibold text-muted">Buscar</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="filtro-search" class="form-control border-start-0 bg-light-subtle" placeholder="Código o descripción...">
                        </div>
                    </div>
                    <div class="col-12 col-md-2">
                        <label for="filtro-tipo" class="form-label small fw-semibold text-muted">Tipo</label>
                        <select id="filtro-tipo" class="form-select bg-light-subtle">
                            <option value="todos">Todos</option>
                            <option value="ingreso">Ingreso</option>
                            <option value="egreso">Egreso</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="filtro-fecha-desde" class="form-label small fw-semibold text-muted">Desde</label>
                        <input type="date" id="filtro-fecha-desde" class="form-control bg-light-subtle">
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="filtro-fecha-hasta" class="form-label small fw-semibold text-muted">Hasta</label>
                        <input type="date" id="filtro-fecha-hasta" class="form-control bg-light-subtle">
                    </div>
                </form>
            </div>
        </div>

        <div class="card data-card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Fecha</th>
                                <th style="width: 120px;">Código</th>
                                <th>Descripción</th>
                                <th style="width: 160px;">Categoría</th>
                                <th class="text-end" style="width: 140px;">Monto</th>
                                <th class="text-center" style="width: 120px;">Tipo</th>
                                <th class="text-end" style="width: 160px;">Saldo Acumulado</th>
                                <th class="text-center" style="width: 150px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-cuerpo">
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold border-top">
                                <td colspan="5" class="text-end text-secondary py-2">Total Ingresos Filtrados:</td>
                                <td colspan="2" class="text-end text-secondary py-2" id="total-ingresos">Bs. 0.00</td>
                                <td></td>
                            </tr>
                            <tr class="fw-bold">
                                <td colspan="5" class="text-end text-secondary py-2">Total Egresos Filtrados:</td>
                                <td colspan="2" class="text-end text-secondary py-2" id="total-egresos">Bs. 0.00</td>
                                <td></td>
                            </tr>
                            <tr class="fw-bold border-top">
                                <td colspan="5" class="text-end text-dark py-2">Saldo Neto (Filtrado):</td>
                                <td colspan="2" class="text-end text-secondary py-2" id="total-neto">Bs. 0.00</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <div class="modal fade" id="modalCrear" tabindex="-1" aria-labelledby="modalCrearLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-crear">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalCrearLabel"><i class="bi bi-plus-circle text-primary me-2"></i> Registrar Transacción</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="crear-fecha" class="form-label fw-semibold small text-muted">Fecha <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" id="crear-fecha" class="form-control" required>
                                <div class="invalid-feedback" id="err-crear-fecha"></div>
                            </div>
                            
                            <div class="col-12">
                                <label for="crear-codigo" class="form-label fw-semibold small text-muted">Código de Transacción</label>
                                <input type="text" id="crear-codigo" class="form-control bg-light" placeholder="Autogenerado al guardar..." disabled readonly>
                            </div>

                            <div class="col-12">
                                <label for="crear-descripcion" class="form-label fw-semibold small text-muted">Descripción <span class="text-danger">*</span></label>
                                <input type="text" name="descripcion" id="crear-descripcion" class="form-control" placeholder="Ej: Pago de alquiler del local" required>
                                <div class="invalid-feedback" id="err-crear-descripcion"></div>
                            </div>

                            <div class="col-12">
                                <label for="crear-monto" class="form-label fw-semibold small text-muted">Monto (Bs.) <span class="text-danger">*</span></label>
                                <input type="number" name="monto" id="crear-monto" step="0.01" min="0.01" class="form-control" placeholder="0.00" required>
                                <div class="invalid-feedback" id="err-crear-monto"></div>
                            </div>

                            <div class="col-12">
                                <label for="crear-tipo" class="form-label fw-semibold small text-muted">Tipo <span class="text-danger">*</span></label>
                                <select name="tipo" id="crear-tipo" class="form-select" required>
                                    <option value="ingreso" selected>Ingreso</option>
                                    <option value="egreso">Egreso</option>
                                </select>
                                <div class="invalid-feedback" id="err-crear-tipo"></div>
                            </div>

                            <div class="col-12">
                                <label for="crear-categoria" class="form-label fw-semibold small text-muted">Categoría <span class="text-danger">*</span></label>
                                <select name="categoria_id" id="crear-categoria" class="form-select" required>
                                </select>
                                <div class="invalid-feedback" id="err-crear-categoria_id"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-3 bg-light-subtle">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom px-4">Guardar Transacción</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="modalEditarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form id="form-editar">
                    <input type="hidden" id="editar-id">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="modalEditarLabel"><i class="bi bi-pencil-square text-primary me-2"></i> Modificar Transacción</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold small text-muted">Tipo de Transacción</label>
                                <input type="text" id="editar-tipo-texto" class="form-control bg-light" disabled readonly>
                            </div>

                            <div class="col-12">
                                <label for="editar-descripcion" class="form-label fw-semibold small text-muted">Descripción <span class="text-danger">*</span></label>
                                <input type="text" name="descripcion" id="editar-descripcion" class="form-control" required>
                                <div class="invalid-feedback" id="err-editar-descripcion"></div>
                            </div>

                            <div class="col-12">
                                <label for="editar-monto" class="form-label fw-semibold small text-muted">Monto (Bs.) <span class="text-danger">*</span></label>
                                <input type="number" name="monto" id="editar-monto" step="0.01" min="0.01" class="form-control" required>
                                <div class="invalid-feedback" id="err-editar-monto"></div>
                            </div>

                            <div class="col-12">
                                <label for="editar-categoria" class="form-label fw-semibold small text-muted">Categoría <span class="text-danger">*</span></label>
                                <select name="categoria_id" id="editar-categoria" class="form-select" required>
                                </select>
                                <div class="invalid-feedback" id="err-editar-categoria_id"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 p-3 bg-light-subtle">
                        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom px-4">Actualizar Transacción</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="modalConfirmarEliminarLabel">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <input type="hidden" id="eliminar-id">
                    <p class="mb-2">¿Estás seguro de que deseas eliminar la transacción <strong id="eliminar-codigo-texto" class="text-danger"></strong>?</p>
                    <p class="text-muted small mb-0">Esta acción no se puede deshacer y los totales se recalcularán de inmediato.</p>
                </div>
                <div class="modal-footer border-top-0 p-3 bg-light-subtle">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" id="btn-confirmar-eliminar-guardar" class="btn btn-danger px-4">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalCrearInstancia = new bootstrap.Modal(document.getElementById('modalCrear'));
            const modalEditarInstancia = new bootstrap.Modal(document.getElementById('modalEditar'));
            const modalEliminarInstancia = new bootstrap.Modal(document.getElementById('modalConfirmarEliminar'));

            const tablaCuerpo = document.getElementById('tabla-cuerpo');
            const totalIngresos = document.getElementById('total-ingresos');
            const totalEgresos = document.getElementById('total-egresos');
            const totalNeto = document.getElementById('total-neto');
            
            const resumenIngresos = document.getElementById('resumen-ingresos');
            const resumenEgresos = document.getElementById('resumen-egresos');
            const resumenSaldo = document.getElementById('resumen-saldo');

            const formFiltros = document.getElementById('form-filtros');
            const filtroSearch = document.getElementById('filtro-search');
            const filtroTipo = document.getElementById('filtro-tipo');
            const filtroFechaDesde = document.getElementById('filtro-fecha-desde');
            const filtroFechaHasta = document.getElementById('filtro-fecha-hasta');

            const formCrear = document.getElementById('form-crear');
            const crearTipo = document.getElementById('crear-tipo');
            const crearCategoria = document.getElementById('crear-categoria');

            const formEditar = document.getElementById('form-editar');
            const editarCategoria = document.getElementById('editar-categoria');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function fetchTransactions() {
                const params = new URLSearchParams({
                    search: filtroSearch.value,
                    tipo: filtroTipo.value,
                    fecha_desde: filtroFechaDesde.value,
                    fecha_hasta: filtroFechaHasta.value
                });

                fetch(`/transacciones/data?${params.toString()}`)
                    .then(response => response.json())
                    .then(data => {
                        tablaCuerpo.innerHTML = data.html;

                        const ingresosVal = parseFloat(data.totales.ingresos);
                        totalIngresos.textContent = 'Bs. ' + ingresosVal.toLocaleString('es-BO', { minimumFractionDigits: 2 });
                        totalIngresos.className = 'text-end py-2 ' + (ingresosVal > 0 ? 'text-success' : 'text-secondary');

                        const egresosVal = parseFloat(data.totales.egresos);
                        totalEgresos.textContent = 'Bs. ' + egresosVal.toLocaleString('es-BO', { minimumFractionDigits: 2 });
                        totalEgresos.className = 'text-end py-2 ' + (egresosVal > 0 ? 'text-danger' : 'text-secondary');
                        
                        const neto = parseFloat(data.totales.saldo_neto);
                        totalNeto.textContent = 'Bs. ' + neto.toLocaleString('es-BO', { minimumFractionDigits: 2 });
                        if (neto > 0) {
                            totalNeto.className = 'text-end py-2 text-success';
                        } else if (neto < 0) {
                            totalNeto.className = 'text-end py-2 text-danger';
                        } else {
                            totalNeto.className = 'text-end py-2 text-secondary';
                        }

                        resumenIngresos.textContent = 'Bs. ' + parseFloat(data.resumen_mes.ingresos).toLocaleString('es-BO', { minimumFractionDigits: 2 });
                        resumenEgresos.textContent = 'Bs. ' + parseFloat(data.resumen_mes.egresos).toLocaleString('es-BO', { minimumFractionDigits: 2 });
                        
                        const saldoMes = parseFloat(data.resumen_mes.saldo);
                        resumenSaldo.textContent = 'Bs. ' + saldoMes.toLocaleString('es-BO', { minimumFractionDigits: 2 });
                        resumenSaldo.className = 'h3 fw-bold mb-0 ' + (saldoMes >= 0 ? 'text-primary' : 'text-danger');
                    })
                    .catch(error => console.error('Error al cargar transacciones:', error));
            }

            filtroSearch.addEventListener('input', fetchTransactions);
            filtroTipo.addEventListener('change', fetchTransactions);
            filtroFechaDesde.addEventListener('change', fetchTransactions);
            filtroFechaHasta.addEventListener('change', fetchTransactions);

            function populateCategories(tipo, selectElement, callback) {
                fetch(`/categorias-por-tipo/${tipo}`)
                    .then(response => response.json())
                    .then(categorias => {
                        selectElement.innerHTML = '';
                        categorias.forEach(cat => {
                            const option = document.createElement('option');
                            option.value = cat.id;
                            option.textContent = cat.nombre;
                            selectElement.appendChild(option);
                        });
                        if (callback) callback();
                    })
                    .catch(error => console.error('Error al cargar categorías:', error));
            }

            crearTipo.addEventListener('change', function () {
                populateCategories(this.value, crearCategoria);
            });

            document.getElementById('modalCrear').addEventListener('show.bs.modal', function () {
                formCrear.reset();
                formCrear.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                document.getElementById('crear-fecha').value = new Date().toISOString().split('T')[0];
                crearTipo.value = 'ingreso';
                populateCategories('ingreso', crearCategoria);
            });

            formCrear.addEventListener('submit', function (e) {
                e.preventDefault();
                formCrear.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                const formData = new FormData(this);

                fetch('/transacciones', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(result => {
                        if (result.status === 422) {
                            Object.keys(result.body.errors).forEach(key => {
                                const input = formCrear.querySelector(`[name="${key}"]`);
                                const errDiv = document.getElementById(`err-crear-${key}`);
                                if (input && errDiv) {
                                    input.classList.add('is-invalid');
                                    errDiv.textContent = result.body.errors[key][0];
                                }
                            });
                        } else if (result.status === 200) {
                            modalCrearInstancia.hide();
                            formCrear.reset();
                            fetchTransactions();
                        }
                    })
                    .catch(error => console.error('Error al crear transacción:', error));
            });

            tablaCuerpo.addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-edit')) {
                    const btn = e.target;
                    const id = btn.getAttribute('data-id');
                    const descripcion = btn.getAttribute('data-descripcion');
                    const monto = btn.getAttribute('data-monto');
                    const tipo = btn.getAttribute('data-tipo');
                    const categoriaId = btn.getAttribute('data-categoria-id');

                    document.getElementById('editar-id').value = id;
                    document.getElementById('editar-tipo-texto').value = tipo.charAt(0).toUpperCase() + tipo.slice(1);
                    document.getElementById('editar-descripcion').value = descripcion;
                    document.getElementById('editar-monto').value = monto;

                    formEditar.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                    populateCategories(tipo, editarCategoria, function () {
                        editarCategoria.value = categoriaId;
                        modalEditarInstancia.show();
                    });
                }
            });

            formEditar.addEventListener('submit', function (e) {
                e.preventDefault();
                formEditar.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                const id = document.getElementById('editar-id').value;
                const data = {
                    descripcion: document.getElementById('editar-descripcion').value,
                    monto: document.getElementById('editar-monto').value,
                    categoria_id: editarCategoria.value
                };

                fetch(`/transacciones/${id}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json().then(data => ({ status: response.status, body: data })))
                    .then(result => {
                        if (result.status === 422) {
                            Object.keys(result.body.errors).forEach(key => {
                                const input = formEditar.querySelector(`[name="${key}"]`);
                                const errDiv = document.getElementById(`err-editar-${key}`);
                                if (input && errDiv) {
                                    input.classList.add('is-invalid');
                                    errDiv.textContent = result.body.errors[key][0];
                                }
                            });
                        } else if (result.status === 200) {
                            modalEditarInstancia.hide();
                            fetchTransactions();
                        }
                    })
                    .catch(error => console.error('Error al editar transacción:', error));
            });

            tablaCuerpo.addEventListener('click', function (e) {
                if (e.target.classList.contains('btn-delete')) {
                    const btn = e.target;
                    const id = btn.getAttribute('data-id');
                    const codigo = btn.getAttribute('data-codigo');

                    document.getElementById('eliminar-id').value = id;
                    document.getElementById('eliminar-codigo-texto').textContent = codigo;
                    modalEliminarInstancia.show();
                }
            });

            document.getElementById('btn-confirmar-eliminar-guardar').addEventListener('click', function () {
                const id = document.getElementById('eliminar-id').value;

                fetch(`/transacciones/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        modalEliminarInstancia.hide();
                        fetchTransactions();
                    })
                    .catch(error => console.error('Error al eliminar transacción:', error));
            });

            fetchTransactions();
        });
    </script>
</body>
</html>