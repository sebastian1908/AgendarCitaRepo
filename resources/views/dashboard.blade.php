<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
</head>
<body>
    <div class="dashboard-container">
        <div class="container">
            <div class="dashboard-card">
                <div class="dashboard-header">
                    <h1><i class="fas fa-calendar-check"></i> Dashboard de Citas</h1>
                    <p>Gestiona tus citas de manera eficiente</p>
                </div>
                
                <div class="user-info">
                    <div class="user-details">
                        <div class="user-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                        <div>
                            <h5 class="mb-0" id="userName">Usuario</h5>
                            <small id="userRole">Rol</small>
                        </div>
                    </div>
                    <button class="btn btn-logout" onclick="logout()">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </button>
                </div>
                
                <div id="alertContainer"></div>
                
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number" id="totalCitas">0</div>
                            <div class="stats-label">Total Citas</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number" id="citasHoy">0</div>
                            <div class="stats-label">Citas Hoy</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number" id="citasSemana">0</div>
                            <div class="stats-label">Esta Semana</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stats-card">
                            <div class="stats-number" id="citasMes">0</div>
                            <div class="stats-label">Este Mes</div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3><i class="fas fa-list me-2"></i>Mis Citas</h3>
                    <button class="btn btn-create" data-bs-toggle="modal" data-bs-target="#createCitaModal">
                        <i class="fas fa-plus me-2"></i>Nueva Cita
                    </button>
                </div>
                
                <div class="table-container">
                    <table class="table table-hover" id="citasTable">
                        <thead>
                            <tr>
                                <th><i class="fas fa-hashtag me-2"></i>ID</th>
                                <th><i class="fas fa-heading me-2"></i>Título</th>
                                <th><i class="fas fa-align-left me-2"></i>Descripción</th>
                                <th><i class="fas fa-calendar me-2"></i>Fecha</th>
                                <th><i class="fas fa-cogs me-2"></i>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="citasTableBody">
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="loading-spinner"></div> Cargando citas...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createCitaModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Crear Nueva Cita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createCitaForm">
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="citaTitle" class="form-label">Título de la Cita</label>
                                <input type="text" class="form-control" id="citaTitle" name="title" required>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="citaDescription" class="form-label">Descripción</label>
                                <textarea class="form-control" id="citaDescription" name="description" rows="3" required></textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="citaDate" class="form-label">Fecha y Hora</label>
                                <input type="datetime-local" class="form-control" id="citaDate" name="date" required>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="createCita()" id="createCitaBtn">
                        <span id="createCitaBtnText">
                            <i class="fas fa-save me-2"></i>Crear Cita
                        </span>
                        <span id="createCitaBtnSpinner" class="d-none">
                            <span class="loading-spinner me-2"></span>Creando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        let currentUser = null;
        let citasData = [];

        document.addEventListener('DOMContentLoaded', function() {
            checkAuth();
            loadUserInfo();
            loadCitas();
        });

        function checkAuth() {
            const token = localStorage.getItem('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }
            
            try {
                const payload = JSON.parse(atob(token.split('.')[1]));
                currentUser = {
                    username: payload.username,
                    role: payload.role
                };
            } catch (e) {
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
        }

        function loadUserInfo() {
            if (currentUser) {
                document.getElementById('userName').textContent = currentUser.username;
                document.getElementById('userRole').textContent = currentUser.role;
            }
        }

        function showAlert(message, type = 'danger') {
            const alertContainer = document.getElementById('alertContainer');
            const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
            
            alertContainer.innerHTML = `
                <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        function clearAlerts() {
            document.getElementById('alertContainer').innerHTML = '';
        }

        async function loadCitas() {
            try {
                const response = await fetch('/api/citas/listar', {
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('token')}`,
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    citasData = result.result || [];
                    renderCitasTable();
                    updateStats();
                } else if (response.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                } else {
                    showAlert('Error al cargar las citas');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error de conexión al cargar las citas');
            }
        }

        function renderCitasTable() {
            const tbody = document.getElementById('citasTableBody');
            
            if (citasData.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h5>No tienes citas agendadas</h5>
                                <p>¡Crea tu primera cita haciendo clic en "Nueva Cita"!</p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = citasData.map(cita => `
                <tr>
                    <td><code>${cita.id.substring(0, 8)}...</code></td>
                    <td><strong>${cita.title}</strong></td>
                    <td>${cita.description}</td>
                    <td><span class="badge-date">${formatDateTime(cita.date)}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-2" onclick="viewCita('${cita.id}')" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteCita('${cita.id}')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }

        function updateStats() {
            const total = citasData.length;
            document.getElementById('totalCitas').textContent = total;
            const today = new Date();
            const todayStr = today.toISOString().split('T')[0];
            const citasHoy = citasData.filter(cita => cita.date.startsWith(todayStr)).length;
            document.getElementById('citasHoy').textContent = citasHoy;
            const startOfWeek = new Date(today);
            startOfWeek.setDate(today.getDate() - today.getDay());
            const endOfWeek = new Date(today);
            endOfWeek.setDate(today.getDate() + (6 - today.getDay()));
            
            const citasSemana = citasData.filter(cita => {
                const citaDate = new Date(cita.date);
                return citaDate >= startOfWeek && citaDate <= endOfWeek;
            }).length;
            document.getElementById('citasSemana').textContent = citasSemana;

            const citasMes = citasData.filter(cita => {
                const citaDate = new Date(cita.date);
                return citaDate.getMonth() === today.getMonth() && 
                       citaDate.getFullYear() === today.getFullYear();
            }).length;
            document.getElementById('citasMes').textContent = citasMes;
        }

        function formatDateTime(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        async function createCita() {
            const form = document.getElementById('createCitaForm');
            const formData = new FormData(form);
            
            const data = {
                title: formData.get('title'),
                description: formData.get('description'),
                date: formData.get('date')
            };

            if (!data.title || !data.description || !data.date) {
                showAlert('Por favor, completa todos los campos');
                return;
            }

            const createBtn = document.getElementById('createCitaBtn');
            const createBtnText = document.getElementById('createCitaBtnText');
            const createBtnSpinner = document.getElementById('createCitaBtnSpinner');

            createBtn.disabled = true;
            createBtnText.classList.add('d-none');
            createBtnSpinner.classList.remove('d-none');

            try {
                const response = await fetch('/api/citas/agendar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'Authorization': `Bearer ${localStorage.getItem('token')}`
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('createCitaModal'));
                    modal.hide();
                    showAlert('¡Cita creada exitosamente!', 'success');
                    form.reset();                    
                    await loadCitas();
                } else if (response.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = '/login';
                } else {
                    showAlert(result.message || 'Error al crear la cita');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error de conexión al crear la cita');
            } finally {
                createBtn.disabled = false;
                createBtnText.classList.remove('d-none');
                createBtnSpinner.classList.add('d-none');
            }
        }

        function viewCita(citaId) {
            const cita = citasData.find(c => c.id === citaId);
            if (cita) {
                alert(`Detalles de la Cita:\n\nTítulo: ${cita.title}\nDescripción: ${cita.description}\nFecha: ${formatDateTime(cita.date)}`);
            }
        }

        function deleteCita(citaId) {
            if (confirm('¿Estás seguro de que quieres eliminar esta cita?')) {
                showAlert('Funcionalidad de eliminaciónI');
            }
        }

        function logout() {
            if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
                localStorage.removeItem('token');
                window.location.href = '/login';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const dateInput = document.getElementById('citaDate');
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(tomorrow.getDate() + 1);
            dateInput.min = tomorrow.toISOString().slice(0, 16);
        });
    </script>
</body>
</html>
