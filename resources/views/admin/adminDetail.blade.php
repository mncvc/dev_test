@php


@endphp
<body>
<div class="container" style="max-width: 800px; margin: 0 auto; padding: 20px; background-color: #f9f9f9; border-radius: 10px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);">

    <h2 style="text-align: center; margin-bottom: 20px;">회원 상세 정보</h2>
    <table border="1" cellpadding="10" cellspacing="0" width="100%" style="border-collapse: collapse;">
        <tr>
            <td colspan="6" style="background-color: #ddd; text-align: center; font-weight: bold;">회원 기본 정보</td>
        </tr>
        <tr>
            <th style="background-color: #f1f1f1;">회원 번호</th>
            <td>{{$rgData->admin_no}}</td>
            <th style="background-color: #f1f1f1;">아이디</th>
            <td>{{$rgData->admin_id}}</td>
            <th style="background-color: #f1f1f1;">이름</th>
            <td>{{$rgData->admin_name}}</td>
        </tr>
        <tr>
            <th style="background-color: #f1f1f1;">부서</th>
            <td colspan="2">IT 기술연구소 T/D 팀</td>
            <th style="background-color: #f1f1f1;">직책</th>
            <td colspan="2">연구원</td>
        </tr>
        <tr>
            <th style="background-color: #f1f1f1;">회원 상태</th>
            <td>
                <div style="display: flex; justify-content: space-between " >
                    <span>{{$rgData->admin_state}}</span>
                    <input type="button" value="수정" style="margin-left: 10px;">
                </div>
                 </td>
            <th style="background-color: #f1f1f1;">등급</th>
            <td>{{$rgData->admin_level}}</td>
            <th style="background-color: #f1f1f1;">OTP 인증 상태</th>
            <td>{{$rgData->google_auth_flag}}</td>
        </tr>
        <tr>
            <th style="background-color: #f1f1f1;">가입일</th>
            <td colspan="5">{{$rgData->reg_date}}</td>
        </tr>
        <tr>
            <td colspan="3" style="text-align: center;">
                <input type="button" value="패스워드 초기화" onClick="" style="padding: 10px; width: 80%;">
            </td>
            <td colspan="3" style="text-align: center;">
                <input type="button" value="OTP 초기화" id="resetOtpBtn" style="padding: 10px; width: 80%;">
            </td>
        </tr>
    </table>
</div>
</body>

<script>
    document.getElementById('resetOtpBtn').addEventListener('click', function() {
        if (confirm('정말로 Otp를 초기화하시겠습니까?')) {
        // AJAX 요청
            let xhr = new XMLHttpRequest();
            xhr.open('POST', '/member/otpReset', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}'); // CSRF 토큰 설정

            let data = JSON.stringify({
                admin_no: {{$rgData->admin_no}}
            }); // 실제로는 회원의 ID나 번호

            xhr.onload = function() {
                if (xhr.status === 200) {

                    alert('otp 인증 초기화되었습니다.');
                } else {
                    console.log(xhr.status);
                    alert('otp 인증 초기화에 실패했습니다. 다시 시도해주세요.');
                }
            };

            xhr.onerror = function() {
                alert('서버와의 통신 중 오류가 발생했습니다.');
            };

            xhr.send(data);
        }
    });
</script>