// frontend/script.js

// Configuración del Back-End
const API_BASE_URL = 'http://localhost/sports-reservation-system/backend/api'; // Reemplaza con la ruta correcta

// Variables de estado
let currentDate = new Date(); // Fecha actual
let reservations = [];
let courts = [];
let selectedCourt = null; // Almacenar la cancha seleccionada

// Inicializar el calendario al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
    // Establecer la semana actual al cargar
    setToStartOfWeek(currentDate);
    updateWeekDisplay();
    fetchCourts();

    // Inicializar el calendario después de obtener las canchas
    // Esto es importante porque necesitamos saber las canchas disponibles
    // para luego obtener las reservas correctamente
    fetchReservations().then(() => {
        generateCalendar();
    });

    // Manejar confirmación de reserva
    document.getElementById('confirmReservation').addEventListener('click', handleReservation);

    // Navegación entre semanas
    document.getElementById('prevWeek').addEventListener('click', () => {
        currentDate.setDate(currentDate.getDate() - 7);
        setToStartOfWeek(currentDate);
        updateWeekDisplay();
        fetchReservations().then(() => {
            generateCalendar();
        });
    });

    document.getElementById('nextWeek').addEventListener('click', () => {
        currentDate.setDate(currentDate.getDate() + 7);
        setToStartOfWeek(currentDate);
        updateWeekDisplay();
        fetchReservations().then(() => {
            generateCalendar();
        });
    });

    // Actualizar el calendario al cambiar la selección de instalación o deporte
    document.getElementById('facilitySelect').addEventListener('change', () => {
        const facilitySelect = document.getElementById('facilitySelect');
        const sportType = document.getElementById('sportType');
        const courtType = document.getElementById('courtType');

        if (facilitySelect.value) {
            // Habilitar los campos si se ha seleccionado una instalación
            sportType.disabled = false;
            courtType.disabled = false;

            // Cargar las canchas correspondientes a la instalación seleccionada
            populateCourtType(facilitySelect.value);
        } else {
            // Deshabilitar los campos si no hay selección
            sportType.disabled = true;
            courtType.disabled = true;
            courtType.innerHTML = '<option value="" selected disabled>-- Selecciona una cancha --</option>';
        }

        fetchReservations().then(() => {
            generateCalendar();
        });
    });

    document.getElementById('sportType').addEventListener('change', () => {
        fetchReservations().then(() => {
            generateCalendar();
        });
    });

    document.getElementById('courtType').addEventListener('change', () => {
        const courtTypeSelect = document.getElementById('courtType');
        const selectedCourtId = courtTypeSelect.value;
        selectedCourt = courts.find(c => c.id == selectedCourtId);

        console.log('Court seleccionado:', selectedCourt); // Log para depuración

        if (selectedCourt) {
            // Mostrar el precio por hora
            document.getElementById('pricePerHour').value = `S/${selectedCourt.price_per_hour.toFixed(2)}`;

            // Calcular y mostrar el precio total si ya hay horas seleccionadas
            const startTime = document.getElementById('reservationStartTime').value;
            const endTime = document.getElementById('reservationEndTime').value;
            if (startTime && endTime) {
                const total = calculateTotalPrice(startTime, endTime, selectedCourt.price_per_hour);
                document.getElementById('totalPrice').value = `$S/{total.toFixed(2)}`;
                console.log('Precio total calculado:', total); // Log para depuración
            } else {
                document.getElementById('totalPrice').value = `S/0.00`;
            }
        } else {
            document.getElementById('pricePerHour').value = '';
            document.getElementById('totalPrice').value = '';
        }

        fetchReservations().then(() => {
            generateCalendar();
        });
    });

    // Actualizar el precio total al cambiar las horas
    document.getElementById('reservationStartTime').addEventListener('change', updateTotalPrice);
    document.getElementById('reservationEndTime').addEventListener('change', updateTotalPrice);

    // Manejar cambios en la selección de método de pago
    document.getElementsByName('paymentMethod').forEach(radio => {
        radio.addEventListener('change', () => {
            const paymentOptions = document.getElementById('paymentOptions');
            const cardPaymentForm = document.getElementById('cardPaymentForm');
            const yapePaymentInfo = document.getElementById('yapePaymentInfo');

            if (radio.value === 'now') {
                paymentOptions.style.display = 'block';
            } else {
                paymentOptions.style.display = 'none';
                cardPaymentForm.style.display = 'none';
                yapePaymentInfo.style.display = 'none';
            }
        });
    });

    // Manejar clic en "Pagar con Tarjeta Visa"
    document.getElementById('payWithCard').addEventListener('click', () => {
        const cardPaymentForm = document.getElementById('cardPaymentForm');
        const yapePaymentInfo = document.getElementById('yapePaymentInfo');
        cardPaymentForm.style.display = 'block';
        yapePaymentInfo.style.display = 'none';
    });

    // Manejar clic en "Pagar con Yape"
    document.getElementById('payWithYape').addEventListener('click', () => {
        const cardPaymentForm = document.getElementById('cardPaymentForm');
        const yapePaymentInfo = document.getElementById('yapePaymentInfo');
        yapePaymentInfo.style.display = 'block';
        cardPaymentForm.style.display = 'none';
    });
});

/**
 * Función para establecer la fecha al inicio de la semana (domingo)
 * @param {Date} date - Objeto Date a ajustar.
 */
function setToStartOfWeek(date) {
    const day = date.getDay(); // 0 (Domingo) a 6 (Sábado)
    const diff = date.getDate() - day;
    date.setDate(diff);
}

/**
 * Actualizar la visualización de la semana actual
 */
function updateWeekDisplay() {
    const weekStart = new Date(currentDate);
    const weekEnd = new Date(currentDate);
    weekEnd.setDate(weekStart.getDate() + 6);

    const options = { month: 'short', day: 'numeric' };
    const formattedStart = weekStart.toLocaleDateString('es-PE', options);
    const formattedEnd = weekEnd.toLocaleDateString('es-PE', options);

    document.getElementById('currentWeek').textContent = `${formattedStart} - ${formattedEnd}, ${weekEnd.getFullYear()}`;

    // Actualizar fechas en el encabezado
    for (let i = 0; i < 7; i++) {
        const date = new Date(weekStart);
        date.setDate(weekStart.getDate() + i);
        document.getElementById(`date${i}`).textContent = date.getDate();
    }
}

/**
 * Fetch canchas desde el Back-End
 */
function fetchCourts() {
    return fetch(`${API_BASE_URL}/get_courts.php`)
        .then(response => response.json())
        .then(data => {
            if (Array.isArray(data)) {
                courts = data;
                console.log('Canchas obtenidas:', courts); // Log para depuración
                populateFacilitySelect();
            } else {
                courts = [];
                console.error('Error al obtener canchas:', data.error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.error || 'Ocurrió un error al obtener las canchas.',
                });
            }
        })
        .catch(err => {
            courts = [];
            console.error('Error al obtener canchas:', err);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al obtener las canchas. Por favor, intenta nuevamente.',
            });
        });
}

/**
 * Popula el select de instalaciones basado en las canchas disponibles
 */
function populateFacilitySelect() {
    const facilitySelect = document.getElementById('facilitySelect');
    const locations = [...new Set(courts.map(court => court.location))];

    // Limpiar opciones existentes
    facilitySelect.innerHTML = '<option value="" selected disabled>Selecciona una instalación</option>';

    // Añadir opciones
    locations.forEach(location => {
        facilitySelect.innerHTML += `<option value="${location}">${location}</option>`;
    });
}

/**
 * Popula el select de tipo de cancha basado en la instalación seleccionada
 * @param {string} location - Ubicación de la instalación seleccionada
 */
function populateCourtType(location) {
    const courtTypeSelect = document.getElementById('courtType');
    courtTypeSelect.innerHTML = '<option value="" selected disabled>Selecciona una cancha</option>';

    const filteredCourts = courts.filter(court => court.location === location);

    filteredCourts.forEach(court => {
        courtTypeSelect.innerHTML += `<option value="${court.id}">${court.name}</option>`;
    });
}

/**
 * Fetch reservas desde el Back-End
 */
function fetchReservations() {
    const location = document.getElementById('facilitySelect').value;
    const dates = getCurrentWeekDates(); // Obtener todas las fechas de la semana actual

    if (!location) {
        reservations = [];
        generateCalendar();
        return Promise.resolve();
    }

    // Iterar sobre cada día de la semana para obtener reservas
    reservations = []; // Reiniciar reservas

    const promises = dates.map(singleDate => {
        return fetch(`${API_BASE_URL}/get_reservations.php?location=${encodeURIComponent(location)}&date=${singleDate}`)
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    reservations = reservations.concat(data);
                } else {
                    reservations = [];
                    console.error('Error al obtener reservas:', data.error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.error || 'Ocurrió un error al obtener las reservas.',
                    });
                }
            })
            .catch(err => {
                reservations = [];
                console.error('Error al obtener reservas:', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al obtener las reservas. Por favor, intenta nuevamente.',
                });
            });
    });

    return Promise.all(promises).then(() => {
        generateCalendar();
    });
}

/**
 * Obtener todas las fechas de la semana actual en formato YYYY-MM-DD
 * @returns {Array} - Array de cadenas de fecha
 */
function getCurrentWeekDates() {
    const weekDates = [];
    const weekStart = new Date(currentDate);
    weekStart.setHours(0, 0, 0, 0);
    for (let i = 0; i < 7; i++) {
        const date = new Date(weekStart);
        date.setDate(weekStart.getDate() + i);
        weekDates.push(getLocalDateStr(date));
    }
    return weekDates;
}

/**
 * Formatea una fecha en el formato YYYY-MM-DD en hora local.
 * @param {Date} date - Objeto Date a formatear.
 * @returns {string} - Fecha formateada como cadena.
 */
function getLocalDateStr(date) {
    const year = date.getFullYear();
    // Los meses en JavaScript van de 0 a 11
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const day = date.getDate().toString().padStart(2, '0');
    return `${year}-${month}-${day}`;
}

/**
 * Generar el calendario con reservas
 */
function generateCalendar() {
    const tbody = document.getElementById('calendarBody');
    tbody.innerHTML = '';

    // Obtener la fecha actual para comparación
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Ignorar la parte de tiempo

    // Generar rangos de horas (7:00 a 23:00)
    for (let hour = 7; hour <= 23; hour++) {
        const row = document.createElement('tr');
        const timeCell = document.createElement('td');
        timeCell.textContent = `${formatHourDisplay(hour)}:00`;
        timeCell.className = 'time-column';
        row.appendChild(timeCell);

        for (let day = 0; day < 7; day++) {
            const cell = document.createElement('td');
            cell.className = 'calendar-cell';
            cell.dataset.hour = hour;
            cell.dataset.day = day;

            // Calcular la fecha correspondiente al casillero
            const weekStart = new Date(currentDate);
            const date = new Date(weekStart);
            date.setDate(weekStart.getDate() + day);
            date.setHours(0, 0, 0, 0);

            // Verificar si la fecha es pasada
            if (date < today) {
                cell.classList.add('disabled');
                cell.style.cursor = 'not-allowed';
                cell.title = 'Fecha pasada no disponible';
            } else {
                const reservation = getReservationForSlot(day, hour);
                if (reservation) {
                    const resDiv = document.createElement('div');
                    resDiv.className = 'reserved';
                    resDiv.textContent = 'Reservado';
                    resDiv.title = `Nombre: ${reservation.customer_name}\nEmail: ${reservation.customer_email}\nTeléfono: ${reservation.customer_phone}\nPrecio Total: $${parseFloat(reservation.total_price).toFixed(2)}`;
                    cell.appendChild(resDiv);

                    // Añadir listener de click para mostrar detalles
                    cell.addEventListener('click', () => showReservationDetails(reservation));
                } else {
                    cell.addEventListener('click', () => openReservationModal(day, hour, date));
                }

                // Resaltar la fecha actual
                if (date.toDateString() === today.toDateString()) {
                    cell.style.border = '2px solid #28a745'; // Verde para resaltar
                }
            }

            row.appendChild(cell);
        }
        tbody.appendChild(row);
    }
}

/**
 * Obtener una reserva específica para un slot
 * @param {number} day - Día de la semana (0-6)
 * @param {number} hour - Hora del día (7-23)
 * @returns {Object|null} - Objeto de reserva o null
 */
function getReservationForSlot(day, hour) {
    const weekStart = new Date(currentDate);
    const date = new Date(weekStart);
    date.setDate(weekStart.getDate() + day);
    const dateStr = getLocalDateStr(date); // YYYY-MM-DD
    const timeStr = `${hour.toString().padStart(2, '0')}:00:00`; // HH:00:00

    return reservations.find(res => {
        return res.date === dateStr && res.start_time <= timeStr && res.end_time > timeStr;
    }) || null;
}

/**
 * Abrir el modal de reserva
 * @param {number} day - Día de la semana (0-6)
 * @param {number} hour - Hora del día (7-23)
 * @param {Date} date - Objeto Date correspondiente al día seleccionado
 */
let reservationModal;

function openReservationModal(day, hour, date) {
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // Verificar que la fecha no sea pasada (seguridad adicional)
    if (date < today) {
        Swal.fire({
            icon: 'warning',
            title: 'Fecha No Disponible',
            text: 'No se pueden realizar reservas en fechas pasadas.',
        });
        return;
    }

    const modalElement = document.getElementById('reservationModal');
    reservationModal = new bootstrap.Modal(modalElement);

    const dateStr = getLocalDateStr(date); // YYYY-MM-DD

    // Prellenar la fecha y la hora de inicio en punto
    document.getElementById('reservationDate').value = dateStr;
    document.getElementById('reservationStartTime').value = formatTimeInput(hour);
    document.getElementById('reservationEndTime').value = formatTimeInput(hour + 1);

    // Limpiar el formulario (excepto la fecha y horas prellenadas)
    document.getElementById('reservationForm').reset();
    document.getElementById('reservationDate').value = dateStr;
    document.getElementById('reservationStartTime').value = formatTimeInput(hour);
    document.getElementById('reservationEndTime').value = formatTimeInput(hour + 1);

    // Limpiar campos de precios
    document.getElementById('pricePerHour').value = '';
    document.getElementById('totalPrice').value = '';

    // Ocultar sub-formularios de pago
    document.getElementById('paymentOptions').style.display = 'none';
    document.getElementById('cardPaymentForm').style.display = 'none';
    document.getElementById('yapePaymentInfo').style.display = 'none';

    reservationModal.show();
}

/**
 * Mostrar detalles de la reserva en una notificación SweetAlert2
 * @param {Object} reservation - Objeto de reserva
 */
function showReservationDetails(reservation) {
    Swal.fire({
        title: 'Detalles de la Reserva',
        html: `
            <p><strong>Cancha:</strong> ${getCourtNameById(reservation.court_id)}</p>
            <p><strong>Fecha:</strong> ${reservation.date}</p>
            <p><strong>Hora de Inicio:</strong> ${reservation.start_time}</p>
            <p><strong>Hora de Fin:</strong> ${reservation.end_time}</p>
            <p><strong>Precio Total:</strong> $${parseFloat(reservation.total_price).toFixed(2)}</p>
            <hr>
            <p><strong>Nombre:</strong> ${reservation.customer_name}</p>
            <p><strong>Email:</strong> ${reservation.customer_email}</p>
            <p><strong>Teléfono:</strong> ${reservation.customer_phone}</p>
        `,
        icon: 'info',
        confirmButtonText: 'Cerrar'
    });
}

/**
 * Obtener el nombre de la cancha por su ID
 * @param {number} court_id - ID de la cancha
 * @returns {string} - Nombre de la cancha
 */
function getCourtNameById(court_id) {
    const court = courts.find(c => c.id == court_id);
    return court ? court.name : 'N/A';
}

/**
 * Formatear la hora para el input de tipo "time"
 * @param {number} hour - Hora del día (0-23)
 * @returns {string} - Hora formateada como cadena (HH:MM)
 */
function formatTimeInput(hour) {
    return hour.toString().padStart(2, '0') + ':00';
}

/**
 * Formatear la hora para la visualización
 * @param {number} hour - Hora del día (0-23)
 * @returns {string} - Hora formateada como cadena
 */
function formatHourDisplay(hour) {
    return hour;
}

/**
 * Actualizar el precio total basado en las horas seleccionadas
 */
function updateTotalPrice() {
    const startTime = document.getElementById('reservationStartTime').value;
    const endTime = document.getElementById('reservationEndTime').value;
    const pricePerHourInput = document.getElementById('pricePerHour').value;

    console.log('Horas seleccionadas:', startTime, endTime);
    console.log('Precio por hora:', pricePerHourInput);

    if (!startTime || !endTime || !selectedCourt) {
        document.getElementById('totalPrice').value = '$0.00';
        return;
    }

    const pricePerHour = parseFloat(pricePerHourInput.replace('$', ''));
    const total = calculateTotalPrice(startTime, endTime, pricePerHour);
    document.getElementById('totalPrice').value = `$${total.toFixed(2)}`;
    console.log('Precio total actualizado:', total); // Log para depuración
}

/**
 * Calcular el precio total basado en el inicio y fin de la reserva
 * @param {string} startTime - Hora de inicio (HH:MM)
 * @param {string} endTime - Hora de fin (HH:MM)
 * @param {number} pricePerHour - Precio por hora
 * @returns {number} - Precio total
 */
function calculateTotalPrice(startTime, endTime, pricePerHour) {
    const start = new Date(`1970-01-01T${startTime}:00`);
    const end = new Date(`1970-01-01T${endTime}:00`);
    let hours = (end - start) / (1000 * 60 * 60);

    if (isNaN(hours) || hours <= 0) {
        return 0.00;
    }

    // Redondear hacia arriba si hay minutos adicionales
    hours = Math.ceil(hours);

    return pricePerHour * hours;
}

/**
 * Manejar la confirmación de reserva
 */
function handleReservation() {
    const court_id = document.getElementById('courtType').value;
    const date = document.getElementById('reservationDate').value;
    const start_time = document.getElementById('reservationStartTime').value;
    const end_time = document.getElementById('reservationEndTime').value;
    const name = document.getElementById('customerName').value;
    const email = document.getElementById('customerEmail').value;
    const phone = document.getElementById('customerPhone').value;
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked') ? document.querySelector('input[name="paymentMethod"]:checked').value : null;
    const total_price = parseFloat(document.getElementById('totalPrice').value.replace('$', '')) || 0.00;

    // Validar que el método de pago esté seleccionado
    if (!paymentMethod) {
        Swal.fire({
            icon: 'error',
            title: 'Error de Método de Pago',
            text: 'Por favor, selecciona un método de pago.',
        });
        return;
    }

    // Obtener información adicional según el método de pago
    let paymentDetails = {};

    if (paymentMethod === 'now') {
        // Determinar el método de pago seleccionado (card o yape)
        const isCard = document.getElementById('cardPaymentForm').style.display === 'block';
        const isYape = document.getElementById('yapePaymentInfo').style.display === 'block';

        if (isCard) {
            // Por el momento, no implementaremos la funcionalidad de pago con tarjeta
            paymentDetails = {
                type: 'card',
                // Detalles adicionales pueden ser añadidos en el futuro
            };
        } else if (isYape) {
            const yapeReference = document.getElementById('yapeReference').value;

            // Validar referencia de Yape
            if (!yapeReference) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error de Pago',
                    text: 'Por favor, ingresa el número de referencia de Yape.',
                });
                return;
            }

            // Añadir detalles de Yape al objeto de pago
            paymentDetails = {
                type: 'yape',
                reference: yapeReference
            };
        }
    }

    // Crear el objeto de reserva
    const reservationData = {
        court_id,
        date,
        start_time,
        end_time,
        customer_name: name,
        customer_email: email,
        customer_phone: phone,
        payment_method: paymentMethod,
        payment_details: paymentDetails // Añadido
        // Nota: total_price se calcula en el backend
    };

    // Enviar la reserva al servidor
    fetch(`${API_BASE_URL}/create_reservation.php`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(reservationData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (paymentMethod === 'now' && paymentDetails.type === 'yape') {
                // Mostrar el código QR de Yape
                Swal.fire({
                    title: 'Pago con Yape',
                    html: `
                        <p>Escanea el siguiente código QR con tu aplicación Yape para completar el pago.</p>
                        <img src="${data.yape_qr || 'assets/images/qr_yape_placeholder.png'}" alt="QR Yape" class="img-fluid">
                        <p>Referencia de Pago: <strong>${data.yape_reference || 'XXXX-XXXX'}</strong></p>
                    `,
                    icon: 'info',
                    confirmButtonText: 'Confirmar Reserva'
                }).then(() => {
                    reservationModal.hide();
                    fetchReservations().then(() => {
                        generateCalendar();   // Regenerar el calendario para reflejar la nueva reserva
                        // Generar y descargar ticket aquí
                        generateTicket(data.reservation, data.total_price);
                    });
                });
            } else {
                // Mostrar mensaje de éxito estándar
                Swal.fire({
                    icon: 'success',
                    title: 'Reserva Confirmada',
                    text: 'Tu reserva ha sido creada exitosamente.',
                }).then(() => {
                    reservationModal.hide();
                    fetchReservations().then(() => {
                        generateCalendar();   // Regenerar el calendario para reflejar la nueva reserva
                        // Generar y descargar ticket aquí
                        generateTicket(data.reservation, data.total_price);
                    });
                });
            }
        } else if (data.error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.error,
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al hacer la reserva.',
            });
        }
    })
    .catch(err => {
        console.error('Error al hacer la reserva:', err);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al hacer la reserva.',
        });
    });
}

/**
 * Generar y descargar ticket como archivo de texto
 * @param {Object} reservation - Datos de la reserva
 * @param {number} total_price - Precio total de la reserva
 */
function generateTicket(reservation, total_price) {
    const courtName = getCourtNameById(reservation.court_id);
    const ticketContent = `
===== TICKET DE RESERVA DE INSTALACIÓN DEPORTIVA =====

Cancha: ${courtName}
Fecha: ${reservation.date}
Hora de Inicio: ${reservation.start_time}
Hora de Fin: ${reservation.end_time}
Método de Pago: ${reservation.payment_method === 'now' ? 'Pagar Ahora' : 'Pagar en el Momento de la Entrada a la Cancha'}
Tipo de Pago: ${reservation.payment_details.type === 'card' ? 'Tarjeta Visa' : (reservation.payment_details.type === 'yape' ? 'Yape' : 'N/A')}
Precio Total: $${parseFloat(total_price).toFixed(2)}

Información del Cliente:
Nombre: ${reservation.customer_name}
Correo Electrónico: ${reservation.customer_email}
Teléfono: ${reservation.customer_phone}

Por favor, llega 15 minutos antes de la hora de tu reserva.
Trae este ticket contigo.

====================================================
    `;

    const blob = new Blob([ticketContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'ticket-reserva.txt';
    a.click();
    window.URL.revokeObjectURL(url);
}
