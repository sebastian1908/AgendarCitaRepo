<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema de Citas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-calendar-check"></i> Sistema de Citas</h1>
                <p>Inicia sesión para acceder a tu cuenta</p>
            </div>
            
            <div id="alertContainer"></div>
            
            <form id="loginForm">
                @csrf
                <div class="form-floating position-relative">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required>
                    <label for="username">Usuario</label>
                </div>
                
                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                    <label for="password">Contraseña</label>
                    <button type="button" class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="passwordToggleIcon"></i>
                    </button>
                </div>
                
                <button type="submit" class="btn btn-login" id="loginBtn">
                    <span id="loginBtnText">
                        <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                    </span>
                    <span id="loginBtnSpinner" class="d-none">
                        <span class="spinner-border spinner-border-sm me-2"></span>Iniciando sesión...
                    </span>
                </button>
            </form>
            
            <div class="register-link">
                <p>¿No tienes cuenta? <a href="#" onclick="showRegisterModal()">Regístrate aquí</a></p>
            </div>
        </div>
    </div>

    <div class="modal fade" id="registerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Registro de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm">
                        <div class="mb-3">
                            <label for="registerUsername" class="form-label">Usuario</label>
                            <input type="text" class="form-control" id="registerUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="registerPassword" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="registerPassword" name="password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="registerUser()">Registrarse</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('passwordToggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        function showRegisterModal() {
            const modal = new bootstrap.Modal(document.getElementById('registerModal'));
            modal.show();
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

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const loginBtn = document.getElementById('loginBtn');
            const loginBtnText = document.getElementById('loginBtnText');
            const loginBtnSpinner = document.getElementById('loginBtnSpinner');
            
            loginBtn.disabled = true;
            loginBtnText.classList.add('d-none');
            loginBtnSpinner.classList.remove('d-none');
            
            clearAlerts();
            
            const formData = new FormData(this);
            const data = {
                username: formData.get('username'),
                password: formData.get('password')
            };
            
            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    localStorage.setItem('token', result.result.token);
                    showAlert('¡Inicio de sesión exitoso! Redirigiendo...', 'success');
                    
                    setTimeout(() => {
                        window.location.href = '/dashboard';
                    }, 1500);
                } else {
                    showAlert(result.message || 'Error al iniciar sesión');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error de conexión. Por favor, intenta de nuevo.');
            } finally {
                loginBtn.disabled = false;
                loginBtnText.classList.remove('d-none');
                loginBtnSpinner.classList.add('d-none');
            }
        });

        async function registerUser() {
            const username = document.getElementById('registerUsername').value;
            const password = document.getElementById('registerPassword').value;
            
            if (!username || !password) {
                showAlert('Por favor, completa todos los campos');
                return;
            }
            
            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ username, password })
                });
                
                const result = await response.json();
                
                if (response.ok) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                    modal.hide();
                    showAlert('¡Usuario registrado exitosamente! Ahora puedes iniciar sesión.', 'success');                    
                    document.getElementById('registerForm').reset();
                } else {
                    showAlert(result.message || 'Error al registrar usuario');
                }
            } catch (error) {
                console.error('Error:', error);
                showAlert('Error de conexión. Por favor, intenta de nuevo.');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('token');
            if (token) {
                fetch('/api/verify-token', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (response.ok) {
                        window.location.href = '/dashboard';
                    } else {
                        localStorage.removeItem('token');
                    }
                })
                .catch(() => {
                    localStorage.removeItem('token');
                });
            }
        });
    </script>
</body>
</html>
