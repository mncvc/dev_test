<!DOCTYPE html>
<html lang="kor">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #333;
        }
        .login-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        .login-title {
            font-size: 1.75rem;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #4a90e2;
        }
        .error-message {
            color: #e53e3e;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }
        .input-field {
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 1rem;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }
        .input-field:focus {
            border-color: #4a90e2;
            outline: none;
            box-shadow: 0 0 0 1px rgba(74, 144, 226, 0.3);
        }
        /* 버튼 스타일 */
        .submit-button {
            width: 100%;
            padding: 0.75rem;
            background-color: #4a90e2;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .submit-button:hover {
            background-color: #357abd;
        }

        .submit-button:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.5);
        }
    </style>

    <script language="javascript">
        function showPopup() { window.open("createQr", "a", "width=400, height=300, left=100, top=50"); }
    </script>

</head>
<body>
<div class="login-container">
    <h1 class="login-title">KMS 관리자 OTP </h1>
    <form action="otpAuth" method="POST">
        @csrf
        <div>
            <label for="otp">OTP 인증 코드 :</label>

            <input type="number" name="otp" id="otp" class="input-field">

        </div>
        <button type="submit" class="submit-button">인증</button>
    </form>
    
    @php
//        echo dd(session()->all()) .  "\n";

        if(session('google_auth_flag') == 'N'){
    @endphp
         <input type="button" value="QR 생성" onClick="showPopup();"/>
    @php
        }
    @endphp

</div>
</body>
</html>