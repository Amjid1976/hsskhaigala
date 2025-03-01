<?php

require_once "config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - School Management System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Stat Cards Styles */
        .admin-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            position: relative;
            padding: 20px;
            border-radius: 10px;
            overflow: hidden;
            min-height: 120px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
        }

        .stat-card.students {
            background: linear-gradient(45deg, #4099ff, #73b4ff);
        }

        .stat-card.teachers {
            background: linear-gradient(45deg, #2ed8b6, #59e0c5);
        }

        .stat-card.classes {
            background: linear-gradient(45deg, #FFB64D, #ffcb80);
        }

        .stat-card.parents {
            background: linear-gradient(45deg, #FF5370, #ff869a);
        }

        .stat-card .icon {
            position: absolute;
            right: 10px;
            bottom: 10px;
            font-size: 70px;
            opacity: 0.2;
            transform: rotate(15deg);
        }

        .stat-card h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            position: relative;
            z-index: 1;
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            margin-top: 10px;
            position: relative;
            z-index: 1;
        }

        .stat-card .trend {
            font-size: 14px;
            margin-top: 5px;
            color: rgba(255, 255, 255, 0.9);
        }

        /* Quick Action Cards */
        .admin-menu {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .menu-item {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid #eee;
        }

        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .menu-item h4 {
            color: #333;
            margin: 0 0 10px 0;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-item p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }

        .menu-item .icon-bg {
            position: absolute;
            right: -20px;
            bottom: -20px;
            font-size: 100px;
            opacity: 0.05;
            transform: rotate(15deg);
        }

        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            width: 260px;
            position: fixed;
            left: 0;
            top: 0;
            background: #2c3e50;
            padding-top: 60px;
            transition: all 0.3s ease;
            z-index: 100;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            left: -260px;
        }

        .sidebar .logo-details {
            height: 60px;
            width: 100%;
            display: flex;
            align-items: center;
            padding: 0 15px;
            position: absolute;
            top: 0;
            background: #1a2634;
        }

        .sidebar .logo-details i {
            font-size: 30px;
            color: #fff;
            margin-right: 10px;
        }

        .sidebar .logo-details .logo_name {
            color: #fff;
            font-size: 20px;
            font-weight: 600;
        }

        .sidebar .nav-links {
            height: 100%;
            padding: 0;
            margin: 0;
            overflow-y: auto;
        }

        .sidebar .nav-links::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar .nav-links::-webkit-scrollbar-track {
            background: #1a2634;
        }

        .sidebar .nav-links::-webkit-scrollbar-thumb {
            background: #3498db;
            border-radius: 10px;
        }

        .sidebar .nav-links li {
            position: relative;
            list-style: none;
        }

        .sidebar .nav-links li.active {
            background: #1a2634;
        }

        .sidebar .nav-links li:hover {
            background: #1a2634;
        }

        .sidebar .nav-links li .icon-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar .nav-links li i {
            height: 50px;
            min-width: 50px;
            text-align: center;
            line-height: 50px;
            color: #fff;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .sidebar .nav-links li a {
            display: flex;
            align-items: center;
            text-decoration: none;
            width: 100%;
            padding: 10px 15px;
        }

        .sidebar .nav-links li a .link_name {
            font-size: 15px;
            font-weight: 400;
            color: #fff;
            transition: all 0.3s ease;
        }

        .sidebar .nav-links li .sub-menu {
            padding: 6px 6px 14px 80px;
            margin-top: -10px;
            background: #1a2634;
            display: none;
        }

        .sidebar .nav-links li.showMenu .sub-menu {
            display: block;
        }

        .sidebar .nav-links li .sub-menu a {
            color: #fff;
            font-size: 14px;
            padding: 7px 0;
            white-space: nowrap;
            opacity: 0.8;
            transition: all 0.3s ease;
        }

        .sidebar .nav-links li .sub-menu a:hover {
            opacity: 1;
        }

        /* Navbar Styles */
        .navbar {
            position: fixed;
            top: 0;
            left: 260px;
            right: 0;
            height: 60px;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            padding: 0 20px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            z-index: 99;
        }

        .navbar.sidebar-collapsed {
            left: 0;
        }

        .navbar .toggle-btn {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            cursor: pointer;
            border-radius: 50%;
            border: none;
            background: #f8f9fa;
            color: #2c3e50;
            transition: all 0.3s ease;
        }

        .navbar .toggle-btn:hover {
            background: #e9ecef;
        }

        .navbar .nav-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-left: 20px;
        }

        .navbar .nav-left {
            display: flex;
            align-items: center;
        }

        .navbar .nav-center {
            display: flex;
            align-items: center;
        }

        .navbar .nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .navbar .search-box {
            position: relative;
            height: 40px;
            max-width: 250px;
            width: 100%;
            margin: 0 20px;
        }

        .navbar .search-box input {
            height: 100%;
            width: 100%;
            outline: none;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 20px;
            padding: 0 15px 0 40px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .navbar .search-box i {
            position: absolute;
            height: 40px;
            width: 40px;
            line-height: 40px;
            text-align: center;
            top: 50%;
            left: 5px;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .navbar .profile-img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
        }

        .navbar .notification-icon {
            position: relative;
            font-size: 1.2rem;
            color: #6c757d;
            cursor: pointer;
        }

        .navbar .notification-icon .badge {
            position: absolute;
            top: -5px;
            right: -5px;
            height: 15px;
            width: 15px;
            background: #ff5370;
            border-radius: 50%;
            font-size: 10px;
            font-weight: 600;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .navbar .user-info .user-name {
            font-size: 14px;
            font-weight: 500;
            color: #2c3e50;
        }

        .navbar .user-info .user-role {
            font-size: 12px;
            color: #6c757d;
        }

        /* Main Content Adjustment */
        .main-content {
            margin-left: 260px;
            padding: 80px 20px 20px;
            transition: all 0.3s ease;
        }

        .main-content.sidebar-collapsed {
            margin-left: 0;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="logo-details">
            <i class="fas fa-school"></i>
            <span class="logo_name">PSchool Admin</span>
        </div>
        <ul class="nav-links">
            <li class="active">
                <a href="dashboard.php">
                    <i class="fas fa-home"></i>
                    <span class="link_name">Dashboard</span>
                </a>
            </li>
            <li>
                <div class="icon-link">
                    <a href="#">
                        <i class="fas fa-users"></i>
                        <span class="link_name">User Management</span>
                    </a>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="sub-menu">
                    <li><a href="manage_teachers.php">Manage Teachers</a></li>
                    <li><a href="manage_students.php">Manage Students</a></li>
                    <li><a href="manage_parents.php">Manage Parents</a></li>
                </ul>
            </li>
            <li>
                <div class="icon-link">
                    <a href="#">
                        <i class="fas fa-chalkboard"></i>
                        <span class="link_name">Academics</span>
                    </a>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="sub-menu">
                    <li><a href="manage_classes.php">Manage Classes</a></li>
                    <li><a href="manage_subjects.php">Manage Subjects</a></li>
                    <li><a href="assign_subjects.php">Assign Subjects</a></li>
                </ul>
            </li>
            <li>
                <a href="student_enrollment.php">
                    <i class="fas fa-user-graduate"></i>
                    <span class="link_name">Student Enrollment</span>
                </a>
            </li>
            <li>
                <div class="icon-link">
                    <a href="#">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="link_name">Attendance</span>
                    </a>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="sub-menu">
                    <li><a href="mark_attendance.php">Mark Attendance</a></li>
                    <li><a href="attendance_report.php">Attendance Report</a></li>
                </ul>
            </li>
            <li>
                <div class="icon-link">
                    <a href="#">
                        <i class="fas fa-file-alt"></i>
                        <span class="link_name">Examination</span>
                    </a>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="sub-menu">
                    <li><a href="exam_schedule.php">Exam Schedule</a></li>
                    <li><a href="manage_marks.php">Manage Marks</a></li>
                    <li><a href="generate_results.php">Generate Results</a></li>
                </ul>
            </li>
            <li>
                <div class="icon-link">
                    <a href="#">
                        <i class="fas fa-money-bill-wave"></i>
                        <span class="link_name">Finance</span>
                    </a>
                    <i class="fas fa-chevron-down arrow"></i>
                </div>
                <ul class="sub-menu">
                    <li><a href="fee_structure.php">Fee Structure</a></li>
                    <li><a href="collect_fees.php">Collect Fees</a></li>
                    <li><a href="fee_reports.php">Fee Reports</a></li>
                </ul>
            </li>
            <li>
                <a href="notifications.php">
                    <i class="fas fa-bell"></i>
                    <span class="link_name">Notifications</span>
                </a>
            </li>
            <li>
                <a href="settings.php">
                    <i class="fas fa-cog"></i>
                    <span class="link_name">Settings</span>
                </a>
            </li>
            <li>
                <a href="../logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="link_name">Logout</span>
                </a>
            </li>
        </ul>
    </div>
    <nav class="navbar">
        <button class="toggle-btn" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="nav-content">
            <div class="nav-left">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search...">
                </div>
            </div>
            
            <div class="nav-center">
                <h4>School Management System</h4>
            </div>
            
            <div class="nav-right">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="badge">3</span>
                </div>
                
                <div class="user-info">
                    <img src="../assets/images/default-profile.png" alt="Profile" class="profile-img">
                    <div>
                        <div class="user-name">
                                  <a href="index.php" class="btn btn-warning">Logout</a>
                        </div>
                        <div class="user-role">Administrator</div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="main-content" id="mainContent">
            <h2>Admin Dashboard</h2>
            <!-- Statistics Section -->
            <div class="admin-stats">
                <?php
                // Get total students
                $sql = "SELECT COUNT(*) as count FROM students";
                $result = mysqli_query($conn, $sql);
                $students_count = mysqli_fetch_assoc($result)['count'];
                
                // Get total teachers
                $sql = "SELECT COUNT(*) as count FROM users WHERE role = 'teacher'";
                $result = mysqli_query($conn, $sql);
                $teachers_count = mysqli_fetch_assoc($result)['count'];
                
                // Get total classes
                $sql = "SELECT COUNT(*) as count FROM classes";
                $result = mysqli_query($conn, $sql);
                $classes_count = mysqli_fetch_assoc($result)['count'];

                // Get total parents
                $sql = "SELECT COUNT(*) as count FROM users WHERE role = 'parent'";
                $result = mysqli_query($conn, $sql);
                $parents_count = mysqli_fetch_assoc($result)['count'];
                ?>
                
                <div class="stat-card students">
                    <div class="icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h3>Total Students</h3>
                    <div class="stat-number"><?php echo $students_count; ?></div>
                    <div class="trend">
                        <i class="fas fa-arrow-up"></i> Active Learners
                    </div>
                </div>
                
                <div class="stat-card teachers">
                    <div class="icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <h3>Total Teachers</h3>
                    <div class="stat-number"><?php echo $teachers_count; ?></div>
                    <div class="trend">
                        <i class="fas fa-arrow-up"></i> Professional Educators
                    </div>
                </div>
                
                <div class="stat-card classes">
                    <div class="icon">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <h3>Total Classes</h3>
                    <div class="stat-number"><?php echo $classes_count; ?></div>
                    <div class="trend">
                        <i class="fas fa-arrow-up"></i> Active Classes
                    </div>
                </div>

                <div class="stat-card parents">
                    <div class="icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Total Parents</h3>
                    <div class="stat-number"><?php echo $parents_count; ?></div>
                    <div class="trend">
                        <i class="fas fa-arrow-up"></i> Registered Parents
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions Section -->
            <h3>Quick Actions</h3>
            <div class="admin-menu">
                <a href="manage_users.php" class="menu-item">
                    <div class="icon-bg">
                        <i class="fas fa-users"></i>
                    </div>
                    <h4>
                        <i class="fas fa-users" style="color: #2ed8b8;"></i>
                        Manage Users
                    </h4>
                </a>
                <a href="manage_classes.php" class="menu-item">
                    <div class="icon-bg">
                        <i class="fas fa-chalkboard"></i>
                    </div>
                    <h4>
                        <i class="fas fa-chalkboard" style="color: #2ed8b6;"></i>
                        Manage Classes
                    </h4>
                </a>
                
                <a href="manage_subject.php" class="menu-item">
                    <div class="icon-bg">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4>
                        <i class="fas fa-book" style="color: #FFB64D;"></i>
                        Manage Subjects
                    </h4>
                </a>
                
                <a href="assign_job.php" class="menu-item">
                    <div class="icon-bg">
                        <i class="fas fa-book"></i>
                    </div>
                    <h4>
                        <i class="fas fa-book" style="color: #FFB64D;"></i>
                        Assign a Job
                    </h4>
                </a>
                <a href="create_student.php" class="menu-item">
                    <div class="icon-bg">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <h4>
                        <i class="fas fa-user-graduate" style="color: #FF5370;"></i>
                        Student Enrollment
                    </h4>
                </a>
                
                <a href="attendance_report.php" class="menu-item">
                    <div class="icon-bg">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h4>
                        <i class="fas fa-calendar-check" style="color: #4099ff;"></i>
                        Attendance Report
                    </h4>
                </a>
                <a href="dairy.php" class="menu-item">
                    <div class="icon-bg">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>
                        <i class="fas fa-file-alt" style="color: #2ed8b6;"></i>
                        Daily Dairy
                    </h4>
                </a>
            <a href="syllabus.php" class="menu-item">
                    <div class="icon-bg">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>
                        <i class="fas fa-file-alt" style="color: #2ed8b6;"></i>
                        Syllubas Division
                    </h4>
                </a>
                <a href="daily_topic.php" class="menu-item">
                    <div class="icon-bg">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>
                        <i class="fas fa-file-alt" style="color: #2ed8b6;"></i>
                        Daily Topic 
                    </h4>
                </a>
                <a href="exam_management.php" class="menu-item">
                    <div class="icon-bg">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h4>
                        <i class="fas fa-file-alt" style="color: #2ed8b6;"></i>
                        Exam Management
                    </h4>
                </a>
            </div>
        </div>
    </div>
    <!-- Add JavaScript for sidebar functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('toggleSidebar');
            const navbar = document.querySelector('.navbar');
            const arrows = document.querySelectorAll(".arrow");

            // Toggle sidebar
            toggleBtn.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('sidebar-collapsed');
                navbar.classList.toggle('sidebar-collapsed');
            });

            // Handle submenu toggles
            arrows.forEach(arrow => {
                arrow.addEventListener("click", (e) => {
                    const arrowParent = e.target.parentElement.parentElement;
                    arrowParent.classList.toggle("showMenu");
                });
            });

            // Handle responsive behavior
            function handleResize() {
                if (window.innerWidth <= 768) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('sidebar-collapsed');
                    navbar.classList.add('sidebar-collapsed');
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('sidebar-collapsed');
                    navbar.classList.remove('sidebar-collapsed');
                }
            }

            // Initial check and add event listener for window resize
            handleResize();
            window.addEventListener('resize', handleResize);
        });
    </script>
</body>
</html>