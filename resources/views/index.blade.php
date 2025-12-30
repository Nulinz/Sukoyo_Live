<!DOCTYPE html>
<html>
<head>
    <title>Sukoyo</title>
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" sizes="32*32" type="image/png">
    
    <!-- Bootstrap CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    
    <!-- Font / Icons -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap" rel="stylesheet">
    
    <!-- jQuery UI -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
    
    <!-- Custom styles for popup -->
    <style>
        .error-popup {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            display: none;
            z-index: 9999;
        }
        
        .popup-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            animation: popupSlide 0.3s ease-out;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        
        @keyframes popupSlide {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .popup-content h4 {
            color: #dc3545;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .popup-content p {
            color: #666;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .popup-close-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.3s ease;
        }
        
        .popup-close-btn:hover {
            background: #c82333;
        }
        
        .case-sensitive-note {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }
    </style>
</head>

<body>
    <!-- Loader -->
    @include('loader')
    
    <!-- Error Popup -->
    <div class="error-popup" id="errorPopup">
        <div class="popup-content">
            <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #dc3545; margin-bottom: 15px;"></i>
            <h4>Login Failed</h4>
            <p>The employee code or password you entered is incorrect. Please check your credentials and try again.</p>
            <button class="popup-close-btn" onclick="closeErrorPopup()">OK</button>
        </div>
    </div>
    
    <div class="main-body">
        <div class="signup">
            <div class="logo d-flex justify-content-center align-items-center">
                <img src="{{ asset('assets/images/login_logo.png') }}" height="50px" alt="">
            </div>
            
            <div class="form-div">
                <h3 class="mb-0 text-center">Login</h3>
                <form action="{{ route('login.submit') }}" method="POST" id="loginForm">
                    @csrf
                    <div class="row">
                        <div class="col-sm-12 col-md-12 mb-4">
                            <label for="empcode">Employee Code</label>
                            <div class="inpflex">
                                <img src="{{ asset('assets/images/login_user.png') }}" height="20px" class="d-flex mx-auto" alt="">
                                <input type="text" class="form-control border-0" name="empcode" id="empcode" autofocus>
                            </div>
                            <div class="case-sensitive-note">
                                <i class="fas fa-info-circle"></i> Employee code is case-sensitive
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-12 mb-4">
                            <label for="password">Password</label>
                            <div class="inpflex">
                                <img src="{{ asset('assets/images/login_lock.png') }}" height="20px" class="d-flex mx-auto" alt="">
                                <input type="password" class="form-control border-0" name="password" id="password">
                                <i class="fa-solid fa-eye-slash" id="passHide"
                                    onclick="togglePasswordVisibility('password', 'passShow', 'passHide')"
                                    style="display:none; cursor:pointer;"></i>
                                <i class="fa-solid fa-eye" id="passShow"
                                    onclick="togglePasswordVisibility('password', 'passShow', 'passHide')"
                                    style="cursor:pointer;"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12 d-flex justify-content-center align-items-center mt-3">
                        <button type="submit" class="loginbtn w-100">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<script>
    function togglePasswordVisibility(inputId, showId, hideId) {
        let $input = $('#' + inputId);
        let $passShow = $('#' + showId);
        let $passHide = $('#' + hideId);
        
        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $passShow.hide();
            $passHide.show();
        } else {
            $input.attr('type', 'password');
            $passShow.show();
            $passHide.hide();
        }
    }
    
    function closeErrorPopup() {
        $('#errorPopup').fadeOut(300);
    }
    
    // Show popup if there's an error from Laravel session
    @if(session('error'))
        $(document).ready(function() {
            $('#errorPopup').fadeIn(300);
        });
    @endif
    
    // Close popup when clicking outside the content
    $(document).on('click', '.error-popup', function(e) {
        if (e.target === this) {
            closeErrorPopup();
        }
    });
    
    // Close popup with Escape key
    $(document).keydown(function(e) {
        if (e.keyCode === 27) { // Escape key
            closeErrorPopup();
        }
    });
</script>

</html>