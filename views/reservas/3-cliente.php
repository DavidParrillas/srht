<h2>Asignar Cliente</h2>

<?php if(!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
<?php endif; ?>

<form method="POST" action="index.php?controller=reservaciones&action=asignarCliente">
    
    <input type="hidden" name="idCliente" id="idClienteSeleccionado" value="">

    <div class="form-group">
        <label for="buscarCliente">Buscar Cliente:</label>
        <input type="text" id="buscarCliente" class="form-control" 
               placeholder="Nombre, DUI o correo" autocomplete="off">
    </div>

    <div id="resultadosCliente" class="mt-2 list-group"></div>

    <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modalNuevoCliente">
        Crear Cliente Nuevo
    </button>

    <button type="submit" id="btnSiguiente" class="btn btn-success mt-2" disabled>
        Siguiente →
    </button>
</form>

<div class="modal fade" id="modalNuevoCliente" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Crear Nuevo Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <iframe src="index.php?controller=clientes&action=crear" style="width:100%; height:400px; border:none;"></iframe>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const inputBusqueda = document.getElementById('buscarCliente');
    const resultadosDiv = document.getElementById('resultadosCliente');
    const inputOculto = document.getElementById('idClienteSeleccionado');
    const btnSiguiente = document.getElementById('btnSiguiente');

    // --- 1. BUSCAR CLIENTES ---
    inputBusqueda.addEventListener('input', function() {
        let q = this.value;
        
        // Limpiamos la selección si el usuario borra la búsqueda
        inputOculto.value = "";
        btnSiguiente.disabled = true;
        
        if (q.length < 2) {
            resultadosDiv.innerHTML = "";
            return;
        }

        // APUNTAMOS AL CONTROLADOR 
        fetch('index.php?controller=reservaciones&action=buscarClienteAjax&q=' + q)
            .then(res => res.text())
            .then(html => {
                resultadosDiv.innerHTML = html;
            })
            .catch(err => console.error('Error en búsqueda:', err));
    });

    // --- 2. SELECCIONAR UN CLIENTE ---
    resultadosDiv.addEventListener('click', function(e) {
        // Buscamos el elemento "padre" que sea un resultado
        let resultado = e.target.closest('.cliente-resultado');
        
        if (resultado) {
            // Obtenemos los datos del cliente (de los atributos data-)
            const id = resultado.dataset.id;
            const nombre = resultado.dataset.nombre;
            
            // Ponemos los valores
            inputOculto.value = id;
            inputBusqueda.value = nombre; // Mostramos el nombre en el input
            
            // Habilitamos el botón de siguiente
            btnSiguiente.disabled = false;
            
            // Ocultamos los resultados
            resultadosDiv.innerHTML = ""; 
        }
    });

});
</script>