<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicia sesión o regístrate</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    
</head>
<body>
    <div class="auth-container">
        <div class="logo">
            <img src="/img/logo.png" alt="Logo">
        </div>
        <button class="switch-btn" id="switchMode">SignUp</button>
        <div id="loginForm" class="show">
            <div class="form-title">Inicia sesión en BeatHive</div>
            <form method="POST" action="">
                @csrf
                <div class="input-group">
                    <label for="login">Correo electrónico o nombre de usuario</label><br>
                    <input type="text" id="login" name="login" autocomplete="username" placeholder="">
                </div>
                <div class="input-group">
                    <label for="password">Contraseña</label><br>
                    <input type="password" id="password" name="password" autocomplete="current-password">
                </div>
                @if(session('error'))
                    <div class="error-message">{{ session('error') }}</div>
                @endif
                <button type="submit" class="submit-btn">Continuar</button>
            </form>
        </div>
        <div id="registerForm" class="fade">
            <div class="form-title">Regístrate en BeatHive</div>
            <form method="POST" action="" id="registerFormElement">
                @csrf
                <div class="row">
                    <div class="col">
                        <div class="input-group">
                            <label for="username">Nombre de usuario</label><br>
                            <input type="text" id="username" name="username" autocomplete="username">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label for="name">Nombre</label><br>
                            <input type="text" id="name" name="name" autocomplete="given-name">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="input-group">
                            <label for="surname">Apellidos</label>
                            <input type="text" id="surname" name="surname" autocomplete="family-name">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label for="email">Correo electrónico</label>
                            <input type="email" id="email" name="email" autocomplete="email">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="input-group">
                            <label for="password_reg">Contraseña</label>
                            <input type="password" id="password_reg" name="password" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="col">
                        <div class="input-group">
                            <label for="password_confirmation">Repite la contraseña</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>
                </div>
                @if($errors->any())
                    <div class="error-message">
                        @foreach($errors->all() as $error)
                            {{ $error }}<br>
                        @endforeach
                    </div>
                @endif
                <button type="submit" class="submit-btn">Registrarse</button>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/login/login.js') }}"></script>
</body>
</html>
