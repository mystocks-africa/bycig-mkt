<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login / Sign Up</title>
</head>
<body>
    <div class="auth-container">
        <!-- Login Form -->
        <div id="loginContainer" class="form-container">
            <h2>Login</h2>
            <form id="loginForm">
                <div class="form-group">
                    <label for="loginEmail">Email:</label>
                    <input type="email" id="loginEmail" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="loginPassword">Password:</label>
                    <input type="password" id="loginPassword" name="password" required>
                </div>
                
                <button type="submit" class="auth-btn" id="loginBtn">Login</button>
            </form>
            
            <div id="loginMessage" class="message" style="display: none;"></div>
            
            <p class="switch-form">
                Don't have an account? 
                <a href="#" id="showSignup">Sign up here</a>
            </p>
        </div>

        <!-- Sign Up Form -->
        <div id="signupContainer" class="form-container" style="display: none;">
            <h2>Sign Up</h2>
            <form id="signupForm">
                <div class="form-group">
                    <label for="signupName">Full Name:</label>
                    <input type="text" id="signupName" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="signupEmail">Email:</label>
                    <input type="email" id="signupEmail" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="signupPassword">Password:</label>
                    <input type="password" id="signupPassword" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="confirmPassword">Confirm Password:</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div>
                
                <button type="submit" class="auth-btn" id="signupBtn">Sign Up</button>
            </form>
            
            <div id="signupMessage" class="message" style="display: none;"></div>
            
            <p class="switch-form">
                Already have an account? 
                <a href="#" id="showLogin">Login here</a>
            </p>
        </div>
    </div>

    <script>
        // Form switching functionality
        document.getElementById('showSignup').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('loginContainer').style.display = 'none';
            document.getElementById('signupContainer').style.display = 'block';
            clearMessages();
        });

        document.getElementById('showLogin').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('signupContainer').style.display = 'none';
            document.getElementById('loginContainer').style.display = 'block';
            clearMessages();
        });

        // Login form handler
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const loginBtn = document.getElementById('loginBtn');
            const messageDiv = document.getElementById('loginMessage');
            
            // Show loading state
            loginBtn.disabled = true;
            loginBtn.textContent = 'Logging in...';
            showMessage('Authenticating...', 'loading', 'loginMessage');
            
            try {
                const response = await fetch('json-api/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'login',
                        email: email,
                        password: password
                    })
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    showMessage('Login successful!', 'success', 'loginMessage');
                    
                    // Store token if provided
                    if (data.token) {
                        localStorage.setItem('authToken', data.token);
                    }
                    
                    // Redirect or perform other actions
                    setTimeout(() => {
                        // window.location.href = 'dashboard.html';
                        console.log('Login successful, redirect here');
                    }, 1500);
                    
                } else {
                    showMessage(data.message || 'Login failed. Please try again.', 'error', 'loginMessage');
                }
                
            } catch (error) {
                console.error('Login error:', error);
                showMessage('Network error. Please check your connection and try again.', 'error', 'loginMessage');
            } finally {
                // Reset button state
                loginBtn.disabled = false;
                loginBtn.textContent = 'Login';
            }
        });

        // Sign up form handler
        document.getElementById('signupForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const name = document.getElementById('signupName').value;
            const email = document.getElementById('signupEmail').value;
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const signupBtn = document.getElementById('signupBtn');
            
            // Validate passwords match
            if (password !== confirmPassword) {
                showMessage('Passwords do not match.', 'error', 'signupMessage');
                return;
            }
            
            // Show loading state
            signupBtn.disabled = true;
            signupBtn.textContent = 'Creating Account...';
            showMessage('Creating your account...', 'loading', 'signupMessage');
            
            try {
                const response = await fetch('json-api/auth.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'signup',
                        name: name,
                        email: email,
                        password: password
                    })
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    showMessage('Account created successfully! Please log in.', 'success', 'signupMessage');
                    
                    // Clear form
                    document.getElementById('signupForm').reset();
                    
                    // Switch to login form after delay
                    setTimeout(() => {
                        document.getElementById('signupContainer').style.display = 'none';
                        document.getElementById('loginContainer').style.display = 'block';
                        clearMessages();
                    }, 2000);
                    
                } else {
                    showMessage(data.message || 'Sign up failed. Please try again.', 'error', 'signupMessage');
                }
                
            } catch (error) {
                console.error('Sign up error:', error);
                showMessage('Network error. Please check your connection and try again.', 'error', 'signupMessage');
            } finally {
                // Reset button state
                signupBtn.disabled = false;
                signupBtn.textContent = 'Sign Up';
            }
        });
        
        function showMessage(text, type, messageId) {
            const messageDiv = document.getElementById(messageId);
            messageDiv.textContent = text;
            messageDiv.className = 'message ' + type;
            messageDiv.style.display = 'block';
        }
        
        function clearMessages() {
            document.getElementById('loginMessage').style.display = 'none';
            document.getElementById('signupMessage').style.display = 'none';
        }
        
        // Clear messages when user starts typing
        document.getElementById('loginEmail').addEventListener('input', () => clearMessages());
        document.getElementById('loginPassword').addEventListener('input', () => clearMessages());
        document.getElementById('signupName').addEventListener('input', () => clearMessages());
        document.getElementById('signupEmail').addEventListener('input', () => clearMessages());
        document.getElementById('signupPassword').addEventListener('input', () => clearMessages());
        document.getElementById('confirmPassword').addEventListener('input', () => clearMessages());
    </script>
</body>
</html>