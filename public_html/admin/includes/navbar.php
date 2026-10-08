<?php
// admin/includes/navbar.php - Premium Navbar & Sidebar
// NO session_start() here - it's already started in the parent file

// ✅ Get admin name and email from SESSION
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';
$admin_email = $_SESSION['admin_email'] ?? '';
?>
<style>
    /* ========== LIGHT RED + WHITE THEME (MATCHING LOGO) ========== */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', sans-serif;
        background: #fff5f5;
        color: #111827;
        padding-top: 60px;
    }

    /* ========== SIDEBAR ========== */
    #sidebar {
        height: 100vh;
        width: 280px;
        position: fixed;
        top: 0;
        left: 0;
        background: #ffffff;
        color: #111827;
        padding-top: 20px;
        z-index: 1050;
        transition: left 0.3s ease;
        border-right: 2px solid rgba(220,38,38,0.12);
        overflow-y: auto;
        box-shadow: 2px 0 24px rgba(220,38,38,0.06);
    }

    #sidebar::-webkit-scrollbar {
        width: 4px;
    }
    #sidebar::-webkit-scrollbar-track {
        background: #fff5f5;
    }
    #sidebar::-webkit-scrollbar-thumb {
        background: #dc2626;
        border-radius: 4px;
    }

    .sidebar-header {
        padding: 0 20px 20px;
        margin-bottom: 16px;
        border-bottom: 2px solid rgba(220,38,38,0.12);
        text-align: center;
    }

    .sidebar-logo {
        text-align: center;
        margin-bottom: 20px;
    }

    .sidebar-logo img {
        height: 65px;
        width: auto;
        max-width: 95%;
        object-fit: contain;
        transform: scale(1.9);
    }

    .sidebar-logo span {
        font-size: 1.4rem;
        font-weight: 800;
        color: #111827;
        letter-spacing: 0.5px;
    }

    .sidebar-logo span .gold {
        color: #dc2626;
    }

    .admin-info {
        padding: 10px 14px;
        background: linear-gradient(135deg, #fff5f5, #fee2e2);
        border-radius: 10px;
        border: 1px solid rgba(220,38,38,0.18);
    }

    .admin-name {
        font-weight: 700;
        color: #b91c1c;
        display: block;
        font-size: 0.82rem;
    }

    .admin-name i {
        color: #dc2626;
        margin-right: 6px;
    }

    .admin-email {
        font-size: 0.62rem;
        color: #6e7385;
    }

    /* ===== NAV LINKS ===== */
    #sidebar .nav-link {
        color: #374151;
        font-weight: 500;
        font-size: 0.82rem;
        padding: 10px 18px;
        transition: all 0.3s ease;
        border-radius: 10px;
        margin: 0 10px 2px 10px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        cursor: pointer;
    }

    #sidebar .nav-link i {
        width: 20px;
        font-size: 0.95rem;
        color: #6e7385;
        transition: all 0.3s;
        text-align: center;
    }

    #sidebar .nav-link:hover {
        background: linear-gradient(135deg, #fff5f5, #fee2e2);
        color: #dc2626;
        transform: translateX(4px);
    }

    #sidebar .nav-link:hover i {
        color: #dc2626;
    }

    #sidebar .nav-link.active {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        color: #ffffff;
        box-shadow: 0 4px 16px rgba(220,38,38,0.3);
    }

    #sidebar .nav-link.active i {
        color: #ffffff;
    }

    .sidebar-divider {
        border: none;
        border-top: 1px solid rgba(220,38,38,0.08);
        margin: 10px 20px;
    }

    #closeSidebar {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 1.1rem;
        color: #6e7385;
        display: none;
        cursor: pointer;
        z-index: 1060;
        transition: color 0.3s;
        background: none;
        border: none;
    }

    #closeSidebar:hover {
        color: #dc2626;
    }

    /* ========== TOP NAVBAR ========== */
    .admin-topbar {
        background: rgba(255, 255, 255, 0.98) !important;
        backdrop-filter: blur(12px);
        border-bottom: 2px solid rgba(220,38,38,0.12);
        padding: 10px 24px;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1040;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 60px;
    }

    .admin-topbar .brand {
        color: #111827 !important;
        font-size: 0.95rem;
        font-weight: 700;
        text-decoration: none;
    }

    .admin-topbar .brand i {
        margin-right: 10px;
        color: #dc2626;
    }

    .btn-settings-top {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 30px;
        background: linear-gradient(135deg, #fff5f5, #fee2e2);
        color: #b91c1c;
        font-size: 0.68rem;
        font-weight: 700;
        text-decoration: none;
        border: 1px solid rgba(220,38,38,0.2);
        transition: all 0.3s;
    }

    .btn-settings-top:hover {
        background: #dc2626;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220,38,38,0.3);
    }

    .text-dim {
        color: #6e7385;
    }

    .btn-toggle-sidebar {
        background: none;
        border: none;
        color: #dc2626;
        font-size: 1.2rem;
        padding: 4px 8px;
        cursor: pointer;
        display: none;
    }

    .btn-toggle-sidebar:hover {
        color: #b91c1c;
    }

    /* ========== CONTENT ========== */
    .content {
        margin-left: 280px;
        padding: 20px 24px 30px;
        min-height: 100vh;
    }

    /* ========== RESPONSIVE ========== */
    @media (max-width: 768px) {
        body {
            padding-top: 60px;
        }
        
        #sidebar {
            left: -280px;
        }
        #sidebar.active {
            left: 0;
        }
        .content {
            margin-left: 0;
            padding: 16px 14px 20px;
        }
        #closeSidebar {
            display: block;
        }
        .btn-toggle-sidebar {
            display: block;
        }
        .btn-settings-top {
            padding: 4px 10px;
            font-size: 0.6rem;
        }
        .admin-topbar .brand {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 480px) {
        #sidebar {
            width: 260px;
        }
        .sidebar-logo span {
            font-size: 1rem;
        }
        #sidebar .nav-link {
            font-size: 0.75rem;
            padding: 8px 14px;
        }
        .admin-name {
            font-size: 0.75rem;
        }
    }
</style>

<!-- ===== SIDEBAR ===== -->
<div id="sidebar">
    <button id="closeSidebar" aria-label="Close sidebar"><i class="fas fa-times"></i></button>
    
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="images/logo.png" alt="Arup Enterprise" onerror="this.src='../admin/images/logo.png'">
        </div>
        <div class="admin-info">
            <span class="admin-name">
                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($admin_name); ?>
            </span>
            <span class="admin-email"><?php echo htmlspecialchars($admin_email); ?></span>
        </div>
    </div>

    <ul class="nav flex-column px-2">
        <!-- Dashboard -->
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
        </li>

        <!-- Categories -->
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                <i class="fas fa-tags"></i> Categories
            </a>
        </li>

        <!-- Products -->
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                <i class="fas fa-boxes"></i> Products
            </a>
        </li>

        <!-- Add Product -->
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add_product.php' ? 'active' : ''; ?>" href="add_product.php">
                <i class="fas fa-plus-circle"></i> Add Product
            </a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="announcements.php">
                <i class="fas fa-bullhorn"></i> Announcements
            </a>
        </li>

        <!-- ===== ASSOCIATES / PARTNERS ===== -->
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'associates.php' ? 'active' : ''; ?>" href="associates.php">
                <i class="fas fa-handshake"></i> Associates
            </a>
        </li>

        <hr class="sidebar-divider">


       

        <!-- Contact Messages -->
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'view_contact.php' ? 'active' : ''; ?>" href="view_contact.php">
                <i class="fas fa-envelope"></i> Contact Messages
            </a>
        </li>

      

        <!-- FAQ -->
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'enquiries.php' ? 'active' : ''; ?>" href="enquiries.php">
                <i class="fas fa-question-circle"></i> Enqueries
            </a>
        </li>

        <hr class="sidebar-divider">

        <!-- Settings -->
        <li class="nav-item">
            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                <i class="fas fa-cog"></i> Settings
            </a>
        </li>

        <!-- Logout -->
        <li class="nav-item">
            <a class="nav-link" href="#" onclick="openLogoutModal(event)">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</div>

<!-- ===== TOP NAVBAR ===== -->
<nav class="admin-topbar">
    <div class="d-flex align-items-center gap-2">
        <button class="btn-toggle-sidebar" id="toggleSidebar" aria-label="Toggle sidebar">
            <i class="fas fa-bars"></i>
        </button>
        <span class="brand">
            <i class="fas fa-crown"></i> Admin Panel
        </span>
    </div>
    <div class="d-flex align-items-center gap-3">
        <a href="settings.php" class="btn-settings-top">
            <i class="fas fa-cog"></i> Settings
        </a>
        <span class="text-dim d-none d-md-block" style="font-size:0.7rem;">
            <i class="fas fa-clock me-1"></i> <?php echo date('d M Y, h:i A'); ?>
        </span>
    </div>
</nav>

<!-- ===== LOGOUT MODAL ===== -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #ffffff; border: 2px solid #dc2626; border-radius: 16px;">
            <div class="modal-header" style="border-bottom-color: rgba(220,38,38,0.12); padding: 16px 20px;">
                <h5 class="modal-title" id="logoutModalLabel" style="color: #b91c1c; font-weight: 700;">
                    <i class="fas fa-sign-out-alt me-2"></i> Confirm Logout
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4" style="color: #374151;">
                <i class="fas fa-question-circle fa-3x mb-3" style="color: #dc2626;"></i>
                <p class="mb-0" style="font-size:0.95rem;">Are you sure you want to logout?</p>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 16px 20px;">
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #fee2e2; color: #b91c1c; border-radius: 30px; padding: 8px 24px; border: none; font-weight:600; font-size:0.8rem;">Cancel</button>
                <a href="logout.php" class="btn" style="background: #dc2626; color: white; border-radius: 30px; padding: 8px 24px; border: none; font-weight:600; font-size:0.8rem; text-decoration: none;">
                    <i class="fas fa-sign-out-alt"></i> Yes, Logout
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    // ===== SIDEBAR TOGGLE =====
    const toggleBtn = document.getElementById("toggleSidebar");
    const closeBtn = document.getElementById("closeSidebar");
    const sidebar = document.getElementById("sidebar");

    if (toggleBtn) {
        toggleBtn.addEventListener("click", function(event) {
            event.preventDefault();
            event.stopPropagation();
            sidebar.classList.toggle("active");
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", function(event) {
            event.preventDefault();
            event.stopPropagation();
            sidebar.classList.remove("active");
        });
    }

    // Close sidebar on outside click (mobile)
    document.addEventListener('click', function(event) {
        if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('active')) {
            const isClickInside = sidebar.contains(event.target);
            const isToggleBtn = toggleBtn && (toggleBtn === event.target || toggleBtn.contains(event.target));
            if (!isClickInside && !isToggleBtn) {
                sidebar.classList.remove('active');
            }
        }
    });

    // ===== LOGOUT MODAL FUNCTION =====
    function openLogoutModal(event) {
        event.preventDefault();
        if (typeof bootstrap !== 'undefined') {
            var myModal = new bootstrap.Modal(document.getElementById('logoutModal'));
            myModal.show();
        } else {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }
    }

    // ===== CLOSE MODAL ON ESCAPE KEY =====
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            var modal = document.getElementById('logoutModal');
            if (modal && typeof bootstrap !== 'undefined') {
                var modalInstance = bootstrap.Modal.getInstance(modal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            }
        }
    });
</script>