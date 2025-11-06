document.addEventListener('DOMContentLoaded', function () {

    const tablaHabitaciones = document.querySelector('.tabla-habitaciones');
    let tabla = null;
    if (tablaHabitaciones) {
        tabla = new DataTable(tablaHabitaciones, {
            paging: true,
            searching: true,
            info: true,
            responsive: true,
            ordering: true,
            lengthChange: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
            },
            columnDefs: [
                { orderable: false, targets: [4] }
            ],
            dom: '<"top"t><"bottom"lip>'
        });

        tabla.on('init', function () {
            const buscarInput = document.getElementById('buscarHabitacion');
            const tipoSelect = document.getElementById('tipoHabitacion');
            const estadoSelect = document.getElementById('estadoHabitacion');

            function aplicarFiltros() {
                const numero = buscarInput ? buscarInput.value.trim() : '';
                const tipo = tipoSelect ? tipoSelect.options[tipoSelect.selectedIndex].text.trim().toLowerCase() : '';
                const estado = estadoSelect ? estadoSelect.value.trim().toLowerCase() : '';

                tabla.columns().every(function () {
                    this.search('');
                });

                if (numero) tabla.column(0).search(numero, false, false);
                if (tipo && tipo !== 'todos los tipos') tabla.column(1).search(tipo, false, false);
                if (estado && estado !== 'todos los estados') tabla.column(2).search(estado, false, false);

                tabla.draw();
            }

            if (buscarInput) {
                buscarInput.addEventListener('input', aplicarFiltros);
            }
            if (tipoSelect) {
                tipoSelect.addEventListener('change', aplicarFiltros);
            }
            if (estadoSelect) {
                estadoSelect.addEventListener('change', aplicarFiltros);
            }
        });

    }

    const numeroInput = document.getElementById('numero');
    const errorDiv = document.getElementById('numeroError');
    const tipoSelect = document.getElementById('tipoHabitacion');
    const precioInput = document.getElementById('precio');
    const capacidadInput = document.getElementById('capacidad');

    if (tipoSelect && precioInput && capacidadInput && numeroInput) {
        tipoSelect.addEventListener('change', function () {
            const selected = this.options[this.selectedIndex];
            const precio = selected.getAttribute('data-precio');
            const capacidad = selected.getAttribute('data-capacidad');

            precioInput.value = precio ? precio : '';
            capacidadInput.value = capacidad ? capacidad : '';
        });
    }

    const alertas = document.querySelectorAll('.alert-success');

    if (alertas.length > 0) {
        alertas.forEach(alerta => {
            
            // Verificamos si la alerta está dentro de #seccion-pago
            if (alerta.closest('#seccion-pago')) {
                // Si está, es la alerta de reembolso. No hacemos nada.
                return; 
            }

            // Si no está, es una alerta normal 
            // Aplicamos el temporizador para cerrarla.
            setTimeout(() => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alerta);
                    bsAlert.close();
                } else {
                    alerta.style.transition = "all 0.4s ease";
                    alerta.style.opacity = "0";
                    setTimeout(() => alerta.remove(), 500);
                }
            }, 3000);
        });
    }
});
