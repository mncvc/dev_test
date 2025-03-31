@php
/*
 * 최고 등급 관리자가 계정을 생성 해준다.
 * 계정 권한 ( 1: master, 2: 조회만 가능, 3: 계정 생성 가능, 4: 키생성 및 관리 가능, 5: 탈퇴 회원 )
 *
*/

@endphp

<!DOCTYPE html>
<html lang="kor">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp</title>
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

        .sign-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
            box-sizing: border-box;
        }
        .sign-title {
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

</head>

<body>
<div class="sign-container">
    <h1 class="sign-title">KMS 관리자 회원 등록</h1>
    <form action="signup" method="POST">
        @csrf
        <div>
            <label for="userId">아이디 :</label>
            <input type="text" name="userId" id="userId" class="input-field" required>
        </div>
        <div>
            <label for="password">1차 비번 :</label>
            <input type="password" name="password" id="password" class="input-field" required>
        </div>

        <div>
            <label for="userName">성명 :</label>
            <input type="text" name="userName" id="userName" class="input-field" required>
        </div>

        <div>
            <label for="userDepart">부서 :</label>
            <select name="userDepart" id="userDepart" class="input-field">
                <option value="00">소속 부서(팀)를  선택해주세요.</option>
                <option value="01">T/D 팀</option>
                <option value="02">S/D 팀</option>
            </select>
        </div>
        <div>
            <label for="position">직급 :</label>
            <select name="position" id="level" class="input-field">
                <option value="01">연구원</option>
                <option value="02">선임 연구원</option>
                <option value="03">책임 연구원</option>
                <option value="04">팀 장</option>
            </select>
        </div>
        <div>
            <label for="employeeNo">사번 :</label>
            <input type="text" name="employeeNo" id="employeeNo" class="input-field" required>
        </div>
        <div>
            <label for="phone">연락처 :</label>
            <input type="text" name="phone" id="phone" class="input-field" required>
        </div>

        <button type="submit" class="submit-button">회원 생성 하기</button>
    </form>
</div>
</body>
</html>


