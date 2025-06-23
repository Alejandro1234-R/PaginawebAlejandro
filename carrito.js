const carritoBtn = document.getElementById('carrito-btn');
const carrito = document.getElementById('carrito');
const contenedorCarrito = document.getElementById('lista-carrito-tbody');
const vaciarCarritoBtn = document.getElementById('vaciar-carrito');
const mensajeConfirmacion = document.getElementById('mensaje-confirmacion');
const contenidoPrincipal = document.querySelector('main'); // Seleccionamos el contenido principal
let carritoArray = [];

// Cargar carrito desde localStorage
document.addEventListener("DOMContentLoaded", () => {
    carritoArray = JSON.parse(localStorage.getItem("carrito")) || [];
    actualizarCarrito();
});

// Evento para agregar productos al carrito
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('btn-add-to-cart')) {
        const agregaProducto = e.target;
        leerDatos(agregaProducto);
    }
});

// Evento para eliminar productos del carrito
contenedorCarrito.addEventListener("click", eliminarProducto);

// Evento para vaciar el carrito
vaciarCarritoBtn.addEventListener('click', () => {
    carritoArray = [];
    actualizarCarrito();
});

// Función para eliminar un producto del carrito
function eliminarProducto(e) {
    if (e.target.classList.contains("borrar-producto")) {
        const id = e.target.getAttribute("data-id");
        // Buscar el producto en el carrito
        const producto = carritoArray.find(producto => producto.id == id);
        if (producto) {
            if (producto.cantidad > 1) {
                producto.cantidad--; // Decrementar la cantidad
            } else {
                // Eliminar el producto si la cantidad es 1
                carritoArray = carritoArray.filter(producto => producto.id != id);
            }
        }
        actualizarCarrito();
    }
}

// Función para leer los datos del producto
function leerDatos(agregaProducto) {
    const color = document.getElementById('colour').value; // Obtener el color seleccionado
    const size = document.getElementById('size').value; // Obtener la talla seleccionada
    const cantidad = parseInt(document.querySelector('.input-quantity').value); // Obtener la cantidad seleccionada

    // Verificar si la talla está seleccionada
    if (!size) {
        alert("Por favor, selecciona una opción."); // Alerta si no se selecciona talla
        return; // Salir de la función si no hay talla
    }

    const productoSeleccionado = {
        imagen: agregaProducto.getAttribute('data-imagen'),
        nombre: agregaProducto.getAttribute('data-nombre'),
        precio: agregaProducto.getAttribute('data-precio'),
        id: Date.now(), // Generar un ID único
        cantidad: cantidad, // Usar la cantidad seleccionada
        color: color, // Agregar color
        size: size // Agregar talla
    };

    const existe = carritoArray.some(producto => producto.nombre === productoSeleccionado.nombre && producto.color === productoSeleccionado.color && producto.size === productoSeleccionado.size);
    if (existe) {
        carritoArray.forEach(producto => {
            if (producto.nombre === productoSeleccionado.nombre && producto.color === productoSeleccionado.color && producto.size === productoSeleccionado.size) {
                producto.cantidad += productoSeleccionado.cantidad; // Incrementar la cantidad
            }
        });
    } else {
        carritoArray.push(productoSeleccionado);
    }

    actualizarCarrito();
    mostrarMensajeConfirmacion(); // Mostrar mensaje de confirmación
}

// Función para mostrar el mensaje de confirmación
function mostrarMensajeConfirmacion() {
    mensajeConfirmacion.style.display = 'block'; // Mostrar el mensaje
    setTimeout(() => {
        mensajeConfirmacion.style.display = 'none'; // Ocultar el mensaje después de 3 segundos
    }, 3000);
}

// Función para actualizar el HTML del carrito
function actualizarCarrito() {
    limpiarHtml();
    carritoArray.forEach(producto => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><img src="${producto.imagen}" width="50"></td>
            <td>${producto.nombre}</td>
            <td>${producto.precio}</td>
            <td>${producto.cantidad}</td>
            <td>${producto.color}</td> <!-- Mostrar color -->
            <td>${producto.size}</td> <!-- Mostrar talla -->
            <td><button class="borrar-producto btn btn-danger" data-id="${producto.id}">Eliminar</button></td>
        `;
        contenedorCarrito.appendChild(row);
    });

    sincronizarStorage();
}

// Función para sincronizar el carrito con localStorage
function sincronizarStorage() {
    localStorage.setItem('carrito', JSON.stringify(carritoArray));
}

// Función para limpiar el HTML del carrito
function limpiarHtml() {
    contenedorCarrito.innerHTML = "";
}

// Evento para mostrar/ocultar el carrito
carritoBtn.addEventListener('click', () => {
    // Ocultar el contenido principal
    contenidoPrincipal.style.display = 'none';
    // Mostrar el carrito
    carrito.style.display = 'block';
});

// Agregar un evento para volver al contenido principal
document.getElementById('volver').addEventListener('click', () => {
    // Mostrar el contenido principal
    contenidoPrincipal.style.display = 'block';
    // Ocultar el carrito
    carrito.style.display = 'none';
});

// Incrementar y decrementar la cantidad
document.querySelector('.btn-increment').addEventListener('click', () => {
    const inputCantidad = document.querySelector('.input-quantity');
    let cantidadActual = parseInt(inputCantidad.value);
    inputCantidad.value = cantidadActual + 1; // Incrementar la cantidad
});

document.querySelector('.btn-decrement').addEventListener('click', () => {
    const inputCantidad = document.querySelector('.input-quantity');
    let cantidadActual = parseInt(inputCantidad.value);
    if (cantidadActual > 1) {
        inputCantidad.value = cantidadActual - 1; // Decrementar la cantidad
    }
});