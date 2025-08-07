<div class="top-header">
    <div class="d-flex justify-content-between align-items-center">
        <div class="header-left">
            <button class="btn btn-link sidebar-toggle d-lg-none">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <div class="header-right">
            <div class="dropdown">
                <button class="btn btn-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle"></i>
                    <?php echo htmlspecialchars($currentUser['username'] ?? 'User'); ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="profile.php">
                            <i class="fas fa-user"></i> My Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="logout.php" 
                           onclick="return confirm('Are you sure you want to logout?')">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Session timeout warning
let sessionTimeout;
let warningTimeout;

function resetSessionTimer() {
    clearTimeout(sessionTimeout);
    clearTimeout(warningTimeout);
    
    // Warning 5 minutes before session expires
    warningTimeout = setTimeout(function() {
        if (confirm('Your session will expire in 5 minutes. Do you want to stay logged in?')) {
            // Make an AJAX request to extend session
            fetch('session_extend.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            resetSessionTimer();
        }
    }, 19 * 60 * 1000); // 19 minutes (5 minutes before 24 hour expiry)
    
    // Auto logout after 24 hours
    sessionTimeout = setTimeout(function() {
        alert('Your session has expired. You will be redirected to the login page.');
        window.location.href = 'logout.php';
    }, 24 * 60 * 60 * 1000); // 24 hours
}

// Initialize session timer
resetSessionTimer();

// Reset timer on user activity
document.addEventListener('click', resetSessionTimer);
document.addEventListener('keypress', resetSessionTimer);
document.addEventListener('mousemove', resetSessionTimer);
</script>
