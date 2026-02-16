<?php
$userData = session()->get('user_data');
if (!empty($userData)){
    $userRole = $userData['role'];
    $sessionData = session()->get('user_permissions') ?? [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= getappdata('company_name') ?></title>
    <link rel="stylesheet" href="<?=base_url('public/assets/css/bootstrap.min.css');?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.40.0/dist/apexcharts.css">
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
         <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="<?=base_url('public/assets/js/fullcalendar.js');?>"></script>

    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?=base_url('public/assets/css/daterangepicker.css');?>" />
    <link rel="stylesheet" href="<?= base_url('public/assets/src/output.css') ?>">




    <style>
        :root {
            --primary: #0F52BA;
            --primary-light: #3980e8;
            --primary-dark: #0a3d8a;
            --secondary: #20B2AA;
            --secondary-light: #3ed8d1;
            --secondary-dark: #178f89;
            --accent: #FF8C00;
            --accent-light: #ffad4d;
            --accent-dark: #cc7000;
            --success: #4CAF50;
            --success-light: #6bc46e;
            --success-dark: #3d8c40;
            --warning: #FFC107;
            --warning-light: #ffcf3d;
            --warning-dark: #cc9a06;
            --danger: #F44336;
            --danger-light: #f77066;
            --danger-dark: #c3352b;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --gray-400: #ced4da;
            --gray-500: #adb5bd;
            --gray-600: #6c757d;
            --gray-700: #495057;
            --gray-800: #343a40;
            --gray-900: #212529;
        }
a{
    text-decoration: none;
}
        body {
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
            background-color: var(--gray-100);
            color: var(--gray-800);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .sidebar {
            background-color: white;
            width: 280px;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .main-content {
            margin-left: 280px;
            flex: 1;
            transition: all 0.3s ease;
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
        
        .logo {
            padding: 24px;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .logo a {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        
        .logo a i {
            margin-right: 10px;
            font-size: 1.8rem;
            color: var(--primary);
            width: 40px;
            height: 40px;
            background: #f1f1f1;
            text-align: center;
            border-radius: 50%;
            display: flex        ;
            align-items: center;
            justify-content: space-evenly;
            border: 1px #d1d1d1 solid;
        }
        
        .sidebar-nav {
            padding: 16px 0;
        }
        
        .sidebar-nav .nav-item {
            position: relative;
        }
        
        .sidebar-nav .nav-link {
            padding: 12px 24px;
            color: var(--gray-700);
            font-weight: 500;
            display: flex;
            align-items: center;
            border-radius: 0;
            transition: all 0.2s ease;
        }
        
        .sidebar-nav .nav-link:hover {
            background-color: rgba(15, 82, 186, 0.05);
            color: var(--primary);
        }
        
        .sidebar-nav .nav-link.active {
            background-color: rgba(15, 82, 186, 0.1);
            color: var(--primary);
            font-weight: 600;
            border-left: 4px solid var(--primary);
        }
          .sidebar-nav .side-active {
            background-color: rgba(15, 82, 186, 0.1);
            color: var(--primary);
            font-weight: 600;
            border-left: 4px solid var(--primary);
        }

        .sidebar-nav .nav-link:not(.collapsed) .bi-chevron-down {
                transform: rotate(180deg);
            }
        
        .sidebar-nav .nav-link i, .sidebar-nav .nav-link svg {
            margin-right: 10px;
            font-size: 1.2rem;
            width: 25px;
            text-align: center;
        }
        
        .topbar {
            height: 70px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }
        
        .topbar .toggle-sidebar {
            font-size: 1.5rem;
            color: var(--gray-700);
            cursor: pointer;
            display: none;
        }
        
        @media (max-width: 768px) {
            .topbar .toggle-sidebar {
                display: block;
            }
        }
        
        .topbar .user-profile {
            display: flex;
            align-items: center;
        }
        
        .topbar .user-profile .profile-dropdown {
            margin-left: 10px;
        }
        
        .topbar .user-profile .dropdown-toggle::after {
            display: none;
        }
        
        .topbar .notification-icon {
            position: relative;
            margin-right: 20px;
            font-size: 1.3rem;
            color: var(--gray-700);
        }
        
        .topbar .notification-icon .badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background-color: var(--danger);
            color: white;
            font-size: 0.7rem;
            font-weight: 600;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        
        .notification-dropdown {
            width: 320px;
            padding: 0;
            overflow: hidden;
        }
        
        .notification-dropdown .notification-header {
            padding: 12px 16px;
            background-color: var(--primary);
            color: white;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
        }
        
        .notification-dropdown .notification-items {
            max-height: 320px;
            overflow-y: auto;
        }
        
        .notification-dropdown .notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--gray-200);
            transition: all 0.2s ease;
            cursor: pointer;
        }
        
        .notification-dropdown .notification-item:hover {
            background-color: var(--gray-100);
        }
        
        .notification-dropdown .notification-item .notification-title {
            font-weight: 600;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }
        
        .notification-dropdown .notification-item .notification-text {
            font-size: 0.85rem;
            color: var(--gray-700);
            margin-bottom: 4px;
        }
        
        .notification-dropdown .notification-item .notification-time {
            font-size: 0.75rem;
            color: var(--gray-600);
        }
        
        .notification-dropdown .notification-footer {
            padding: 8px 16px;
            background-color: var(--gray-100);
            text-align: center;
        }
        
        .notification-dropdown .notification-footer a {
            color: var(--primary);
            font-size: 0.9rem;
            font-weight: 500;
            text-decoration: none;
        }
        
        .card {
            border: none;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        
        .card-statistic {
            padding: 24px;
        }
        
        .card-statistic .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 16px;
        }
        
        .card-statistic.primary .card-icon {
            background-color: rgba(15, 82, 186, 0.1);
            color: var(--primary);
        }
        
        .card-statistic.success .card-icon {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success);
        }
        
        .card-statistic.warning .card-icon {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }
        
        .card-statistic.danger .card-icon {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger);
        }
        
        .card-statistic .card-title {
            font-size: 0.9rem;
            color: var(--gray-700);
            margin-bottom: 8px;
        }
        
        .card-statistic .card-value {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 4px;
        }
        
        .card-statistic .card-subtitle {
            font-size: 0.85rem;
            color: var(--gray-600);
        }
        
        .task-card {
            padding: 20px;
            margin-bottom: 16px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
        
        .task-card.high-priority {
            border-left: 4px solid var(--danger);
        }
        
        .task-card.medium-priority {
            border-left: 4px solid var(--warning);
        }
        
        .task-card.low-priority {
            border-left: 4px solid var(--success);
        }
        
        .task-card .task-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }
        
        .task-card .task-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 6px;
        }
        
        .task-card .task-meta {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .task-card .task-meta .task-branch,
        .task-card .task-meta .task-deadline,
        .task-card .task-meta .task-creator {
            margin-right: 16px;
            font-size: 0.85rem;
            color: var(--gray-700);
            display: flex;
            align-items: center;
            margin-bottom: 6px;
        }
        
        .task-card .task-meta i {
            margin-right: 6px;
            font-size: 1rem;
        }
        
        .task-card .task-description {
            font-size: 0.9rem;
            color: var(--gray-700);
            margin-bottom: 16px;
        }
        
        .task-card .task-progress {
            margin-bottom: 16px;
        }
        
        .task-card .task-progress .progress-text {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
            margin-bottom: 6px;
        }
        
        .task-card .task-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        
        .task-card .badge-priority,
        .task-card .badge-status {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 5px 10px;
            border-radius: 30px;
        }
        
        .task-card .badge-priority.high {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger);
        }
        
        .task-card .badge-priority.medium {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }
        
        .task-card .badge-priority.low {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success);
        }
        
        .task-card .badge-status.pending {
            background-color: rgba(15, 82, 186, 0.1);
            color: var(--primary);
        }
        
        .task-card .badge-status.in-progress {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning);
        }
        
        .task-card .badge-status.completed {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success);
        }
        
        .progress {
            height: 8px;
            border-radius: 4px;
            background-color: var(--gray-200);
        }
        
        .progress-bar {
            border-radius: 4px;
        }
        
        .btn-primary {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        .btn-success {
            background-color: var(--success);
            border-color: var(--success);
        }
        
        .btn-success:hover {
            background-color: var(--success-dark);
            border-color: var(--success-dark);
        }
        
        .btn-warning {
            background-color: var(--warning);
            border-color: var(--warning);
        }
        
        .btn-warning:hover {
            background-color: var(--warning-dark);
            border-color: var(--warning-dark);
        }
        
        .btn-danger {
            background-color: var(--danger);
            border-color: var(--danger);
        }
        
        .btn-danger:hover {
            background-color: var(--danger-dark);
            border-color: var(--danger-dark);
        }
        
        .paginate-arrows {
            padding: 8px 16px;
            background-color: white;
            border-radius: 0.25rem;
            color: var(--gray-800);
            border: 1px solid var(--gray-300);
            margin: 0 5px;
            transition: all 0.2s ease;
        }
        
        .paginate-arrows:hover {
            background-color: var(--gray-100);
            color: var(--primary);
        }
        
        .page-link {
            color: var(--primary);
        }
        
        .page-item.active .page-link {
            background-color: var(--primary);
            border-color: var(--primary);
        }
        
        /* Animations */
        .fade-in {
            animation: fadeIn 0.3s ease-in forwards;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .scale-in {
            animation: scaleIn 0.3s ease-in forwards;
        }
        
        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-control {
            border-radius: 0.375rem;
            border: 1px solid var(--gray-300);
            padding: 0.5rem 0.75rem;
        }
        
        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.2rem rgba(15, 82, 186, 0.25);
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .required-indicator {
            color: var(--danger);
            margin-left: 4px;
        }
        
        /* Alert customization */
        .alert {
            border-radius: 10px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border: none;
        }
        
        .alert-success {
            background-color: rgba(76, 175, 80, 0.1);
            color: var(--success-dark);
        }
        
        .alert-danger {
            background-color: rgba(244, 67, 54, 0.1);
            color: var(--danger-dark);
        }
        
        .alert-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: var(--warning-dark);
        }
        
        .alert-info {
            background-color: rgba(15, 82, 186, 0.1);
            color: var(--primary-dark);
        }
        
        /* Custom table styles */
        .table-container {
            overflow-x: auto;
            margin-bottom: 1.5rem;
        }
        
        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }
        
        .table th {
            background-color: var(--gray-100);
            font-weight: 600;
            padding: 1rem;
            font-size: 0.9rem;
        }
        
        .table td {
            padding: 1rem;
            vertical-align: middle;
            font-size: 0.95rem;
        }
        
        .table tbody tr {
            transition: all 0.2s ease;
        }
        
        .table tbody tr:hover {
            background-color: rgba(15, 82, 186, 0.05);
        }
        
        .table-bordered {
            border: 1px solid var(--gray-200);
        }
        
        .table-bordered th,
        .table-bordered td {
            border: 1px solid var(--gray-200);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="logo">
            <a href="<?= base_url('dashboard') ?>">
                <?= (getappdata('logo') ? :  '<i class="">'.substr(getappdata('company_name'), 0, 1) ).'</i>'; ?>
                <span><?= getappdata('company_name') ?></span>
            </a>
        </div>
        <div class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= uri_string() == 'dashboard' ? 'active' : '' ?>" href="<?= base_url('dashboard') ?>">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <?php  if(haspermission('','supperadmin')) { ?>
                <li class="nav-item disp">
                    <a class="nav-link <?= strpos(uri_string(), 'appointments') === 0 && strpos(uri_string(), 'appointments') === false ? 'active' : '' ?>" href="<?= base_url('appointments') ?>">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar "><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                        <span>Appoinments</span>
                    </a>
                </li>
                <?php
                 if(haspermission('','view_services')) { ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'services-list') === 0 && strpos(uri_string(), 'appointments') === false ? 'active' : '' ?>" href="<?= base_url('services-list') ?>">
                    <span class="flex-shrink-0"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles "><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path><path d="M5 3v4"></path><path d="M19 17v4"></path><path d="M3 5h4"></path><path d="M17 19h4"></path></svg></span>
                        <span>Services</span>
                    </a>
                </li>
                <?php }  
                }
                if(haspermission('','view_staff')) {  ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'staff') === 0 && strpos(uri_string(), 'appointments') === false ? 'active' : '' ?>" href="<?= base_url('staff') ?>">
                    <span class="flex-shrink-0"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-user "><circle cx="12" cy="12" r="10"></circle><circle cx="12" cy="10" r="3"></circle><path d="M7 20.662V19a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v1.662"></path></svg></span>
                        <span>Team</span>
                    </a>
                </li>
                <?php 
                }
                if(haspermission('','task_view')) { 
                        if( $userRole == 1) {
                 ?>
                  <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'master-task') === 0 && strpos(uri_string(), 'master-task') === false ? 'active' : '' ?>" href="<?= base_url('master-task') ?>">
                        <i class="bi bi-list-check"></i>
                        <span> Task Master</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'tasks') === 0 && strpos(uri_string(), 'tasks') === false ? 'active' : '' ?>" href="<?= base_url('tasks') ?>">
                        <i class="bi bi-list-check"></i>
                        <span>Activity Tasks </span>
                    </a>
                </li>
                <?php }  if( $userRole != 1) { ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'tasks/my-tasks') === 0 ? 'active' : '' ?>" href="<?= base_url('tasks/my-tasks') ?>">
                        <i class="bi bi-person-check"></i>
                        <span>My Tasks</span>
                    </a>
                </li>
                <?php } 
            }?>

             <?php
                 if(haspermission('','view_activity_task')) { ?>
                <li class="nav-item">
                    <a class="nav-link "  href="<?=base_url('task-activity-task');?>">
                    <span class="flex-shrink-0"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles "><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"></path><path d="M5 3v4"></path><path d="M19 17v4"></path><path d="M3 5h4"></path><path d="M17 19h4"></path></svg></span>
                        <span>Activity Task</span>
                    </a>
                </li>


                <?php 
                 }
                if(haspermission('', 'view_clients')) { ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'clients') === 0 && strpos(uri_string(), 'appointments') === false ? 'active' : '' ?>" href="<?= base_url('clients') ?>">
                    <span class="flex-shrink-0"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-users "><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg></span>
                        <span>Clients</span>
                    </a>
                </li>

                <?php }
                if(haspermission('','role_master')) { ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'role-master') === 0 ? 'active' : '' ?>" href="<?= base_url('role-master') ?>">
                        <i class="bi bi-shop"></i>
                        <span>Role Master</span>
                    </a>
                </li>
                <?php } ?>
                
                <?php  if(hasPermission('','view_project_unit') || hasPermission('','create_project_unit') ) { ?>

                 <li class="nav-item">
                    <a class="nav-link " href="<?= base_url('project-unit') ?>">
                        <i class="bi bi-shop"></i>
                        <span>Project Unit</span>
                    </a>
                </li>
                
                <?php } if (session()->get('role') == 'admin' || session()->get('role') == 'zone_manager'): ?>
                <li class="nav-item d-none hidden">
                    <a class="nav-link <?= strpos(uri_string(), 'dashboard/branches') === 0 ? 'active' : '' ?>" href="<?= base_url('dashboard/branches') ?>">
                        <i class="bi bi-shop"></i>
                        <span>Branches</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (session()->get('role') == 'admin'): ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'dashboard/users') === 0 ? 'active' : '' ?>" href="<?= base_url('dashboard/users') ?>">
                        <i class="bi bi-people"></i>
                        <span>Users</span>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if(haspermission('','view_report')) {  ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'dashboard/reports') === 0 ? 'active' : '' ?>" href="<?= base_url('dashboard/reports') ?>">
                        <i class="bi bi-bar-chart"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'dashboard/notifications') === 0 ? 'active' : '' ?>" href="<?= base_url('notifications') ?>">
                        <i class="bi bi-bell"></i>
                        <span>Notifications</span>
                        <span class="notification-count badge bg-danger rounded-pill ms-auto" id="sidebar-notification-count"></span>
                    </a>
                </li>
               <?php 
                 if(haspermission('','view_branch')) : ?>
                <li class="nav-item d-none">
                    <a class="nav-link <?= strpos(uri_string(), 'branches') === 0 ? 'active' : '' ?>" href="<?= base_url('branches') ?>">
                        <i class="bi bi-shop-window"></i>
                        <span>Branches</span>
                    </a>
                </li>

                 <li class="nav-item">
                    <a class="nav-link <?= strpos(uri_string(), 'dashboard/notifications') === 0 ? 'active' : '' ?>" href="<?= base_url('settings/projects') ?>">
                        <i class="bi bi-diagram-3-fill"></i>
                        <span>Projects</span>
                        <span class="notification-count badge bg-danger rounded-pill ms-auto" id="sidebar-notification-count"></span>
                    </a>
                </li>
                
               
                <?php if ($userRole ==1): ?>
                    <!-- nav -->
                     <div class="relative mb-0 nav-item  <?= strpos(uri_string(), 'settings') === 0 ? 'side-active' : '' ?>" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center w-full   rounded-lg transition-all duration-200 
                                text-gray-700 hover:bg-gray-100">
                            <div class="flex items-center justify-between w-full nav-link ">
                            <div class="flex items-center">
                                <div class="mr-3">
                                  <i class="bi bi-gear"></i>
                                </div>
                                <span class="font-medium">Settings</span>
                            </div>
                            <div :class="{ 'rotate-180': open }" class="transition-transform duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-chevron-down text-gray-500">
                                <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </div>
                            </div>
                        </button>

                        <div class="ml-4 mt-1 space-y-1" x-show="open" x-transition>
                            <a class="flex items-center w-full px-3 py-2 rounded-lg transition-all duration-200 text-sm text-gray-700 hover:bg-gray-100" href="<?= base_url('settings') ?>">
                            Settings
                            </a>
                            <?php endif;
                            if(haspermission('','view_category')) : ?>
                                <a class="flex items-center w-full px-3 py-2 rounded-lg transition-all duration-200 text-sm text-gray-700 hover:bg-gray-100 <?= strpos(uri_string(), 'categories') === 0 ? 'active' : '' ?>" href="<?= base_url('categories') ?>">
                                    <!-- <i class="bi bi-diagram-3"></i> -->
                                    <span>Staff Capability</span>
                                </a>
                            <?php endif; 
                            if(haspermission('','view_projects')) : ?>
                                <a class="flex items-center w-full px-3 py-2 rounded-lg transition-all duration-200 text-sm text-gray-700 hover:bg-gray-100 <?= strpos(uri_string(), 'settings/projects') === 0 ? 'active' : '' ?>" href="<?= base_url('settings/projects') ?>">
                                    <!-- <i class="bi bi-diagram-3"></i> -->
                                    <span>Projects</span>
                                </a>
                            <?php endif; ?>

                        <?php
                        if(haspermission('','permissions_view')) { ?>
                            <a class="flex items-center w-full px-3 py-2 rounded-lg transition-all duration-200 text-sm text-gray-700 hover:bg-gray-100 <?= strpos(uri_string(), 'permisions') === 0 ? 'active' : '' ?>" href="<?= base_url('permisions') ?>">
                                <!-- <i class="bi bi-building-lock"></i> -->
                                <span>Permissions</span>
                            </a>
                        <?php } ?>
                        
                        </div>
                        </div>

                         <li class="nav-item">
                            <a class="nav-link <?= strpos(uri_string(), 'dashboard/reports') === 0 ? 'active' : '' ?>" href="<?= base_url('reports') ?>">
                                <i class="bi bi-file-earmark-medical"></i>
                                <span>Reports</span>
                                <span class="notification-count badge bg-danger rounded-pill ms-auto" id="sidebar-notification-count"></span>
                            </a>
                        </li>

                     <!-- close nav -->
             
                <?php endif;?>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div class="toggle-sidebar">
                <i class="bi bi-list"></i>
            </div>
            
            <div class="user-profile ms-auto">
                <div class="dropdown">
                    <a href="#" class="notification-icon dropdown-toggle" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell"></i>
                        <span class="badge" id="notification-badge"></span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                        <div class="notification-header">
                            <span>Notifications</span>
                            <a href="<?= base_url('dashboard/notifications/mark-all-as-read') ?>" class="text-white small">Mark all as read</a>
                        </div>
                        <div class="notification-items" id="notification-items">
                            <div class="text-center py-3">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mb-0 mt-2 small">Loading notifications...</p>
                            </div>
                        </div>
                        <div class="notification-footer">
                            <a href="<?= base_url('notifications') ?>">View all notifications</a>
                        </div>
                    </div>
                </div>
                
                <div class="dropdown profile-dropdown">
                    <a class="dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar me-2">
                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: var(--primary-light); color: white; font-weight: 600;">
                            <?= substr(session('user_data')['username'], 0, 1) ?>
                            </div>
                        </div>
                        <div>
                            <div class="text-xs text-gray-500"><?= session()->get('user_data')['username'] ?></div>
                            <div class="small text-muted"><?= ucfirst(str_replace('_', ' ', session()->get('user_data')['type'])) ?></div>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <!-- <li><a class="dropdown-item" href="<?= base_url('/settings/profile') ?>"><i class="bi bi-person me-2"></i> Profile</a></li> -->
                         <?php
                           if(haspermission('','settings')) : ?>
                        <li><a class="dropdown-item" href="<?= base_url('settings') ?>"><i class="bi bi-gear me-2"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <?php endif; ?>
                        <li><a class="dropdown-item" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-4 md:p-6">
            <div class="space-y-6">
            <?php if (session()->has('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= session('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->has('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (session()->has('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?= $this->renderSection('content') ?>
            </div>
        </main>
    </div>
    
    <?= view('layout/footer_link') ?>
   
    <?= $this->renderSection('scripts') ?>
</body>
</html>