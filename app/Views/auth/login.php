<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Management System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #0F52BA;
            --primary-light: #3980e8;
            --primary-dark: #0a3d8a;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Display", "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 30px;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: var(--primary);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .logo i {
            font-size: 3rem;
            color: var(--primary);
        }
        
        .logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-top: 10px;
            margin-bottom: 5px;
        }
        
        .logo p {
            font-size: 1rem;
            color: #6c757d;
        }
        
        .form-control {
            height: 48px;
            border-radius: 8px;
            padding-left: 15px;
            box-shadow: none !important;
            border: 1px solid #ced4da;
        }
        
        .form-control:focus {
            border-color: var(--primary);
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
        }
        
        .btn-primary {
            height: 48px;
            border-radius: 8px;
            font-weight: 600;
            background-color: var(--primary);
            border-color: var(--primary);
            width: 100%;
        }
        
        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
        }
        
        .login-footer {
            text-align: center;
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 20px;
        }
        
        .input-group-text {
            background-color: white;
            border-right: none;
            border-radius: 8px 0 0 8px;
        }
        
        .input-group .form-control {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }
        
        .input-group-text i {
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="bi bi-grid-1x2-fill"></i>
            <h1>TaskManager</h1>
            <p>Supermarket Task Management System</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0 text-center">Sign In</h4>
            </div>
            <div class="card-body p-4">
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger">
                        <?= session('error') ?>
                    </div>
                <?php endif; ?>
                
              
                
                <form action="<?= base_url('login') ?>" method="post">
                <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" value="<?= old('password') ?>" name="password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Sign In</button>
                </form>
            </div>
        </div>
        
        <div class="login-footer">
            &copy; <?= date('Y') ?> TaskManager. All Rights Reserved.
        </div>
    </div>
    <!-- alert -->
  <!-- Right Side Alert -->
  <?php if (session()->has('errors')): ?>
   
        <ul class="mb-0">
            <?php foreach (session('errors') as $error): ?>
                <!-- <li></li> -->
                <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055;">
                    <div class="alert alert-danger bg-danger text-light border-0 alert-dismissible fade show" role="alert">
                        <strong>Error!</strong><?= $error ?>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    </div>
            <?php endforeach; ?>
        </ul>
    
<?php endif; ?>
   

     <!-- close Alert -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            var alert = document.querySelector('.alert');
            if(alert){
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
            }
        }, 3000);
    </script>

</body>
</html>