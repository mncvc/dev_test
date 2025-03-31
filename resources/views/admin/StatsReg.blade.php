@extends('layout.header')
@section('content')
    <body>
    <body>
    <span>통계 생성</span>

    {{-- 산출 조건 --}}
    <div style="height:100vh">
        <form action="statsReg/t" method="POST">
            @csrf
            <label>its-key</label>
            <input type="text" id="its_key" name="its_key" value="ASTAT-" maxlength="10">
            <br/>

            <label>subject</label>
            <input type="text" id="subject" name="subject" maxlength="30">
            <br/>

            <label>시작일</label>
            <input type="date" id="start_date" name="start_date">
            <br/>

            <label>종료일</label>
            <input type="date" id="end_date" name="end_date">
            <br/>

            {{-- target --}}
            <label>산출 결과</label>
            <div class="checkbox-container" id="result_target" name="result_target">
                <label>
                    <input type="checkbox" id="selectAll"> 전체 선택
                </label>
                <label>
                    <input type="checkbox" class="item" name="result_target[]" value="id"> 아이디
                </label>
                <label>
                    <input type="checkbox" class="item" name="result_target[]" value="status"> 상태1(코드)
                </label>
                <label>
                    <input type="checkbox" class="item" name="result_target[]" value="state"> 상태2
                </label>
                <label>
                    <input type="checkbox" class="item" name="result_target[]" value="method"> 메서드
                </label>
                <label>
                    <input type="checkbox" class="item" name="result_target[]" value="path"> 경로
                </label>
                <label>
                    <input type="checkbox" class="item" name="result_target[]" value="url"> 사용자 url
                </label>
                <label>
                    <input type="checkbox" class="item" name="result_target[]" value="client_ip"> 사용자IP
                </label>
                <label>
                    <input type="checkbox" class="item" name="result_target[]" value="user"> 사용자계정
                </label>
                <label>
                    <input type="checkbox" class="item" name="result_target[]" value="modify_date"> 수정일
                </label>
                <label>
                    <input type="checkbox" class="item" name="result_target[]" value="reg_date"> 등록일
                </label>
            </div>
            <div class="selected-items">선택된 항목: 없음</div>

            <label>산출 조건</label>
            <select name="join_target" id="join_target" >
                <option value="none">없음</option>
                <option value="status">status</option>
                <option value="state">state</option>
                <option value="method">method</option>
            </select>
            <input type="TEXT" id="join_target_v" name="join_target_v" placeholder="조건 ','으로 구분 ">


            <button type="submit">전송하기</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const selectAll = document.getElementById("selectAll");
            const checkboxes = document.querySelectorAll(".item");
            const selectedText = document.querySelector(".selected-items");
            const selectedItemsInput = document.getElementById("selectedItems");

            // 전체 선택 체크박스 클릭 시 모든 항목 선택/해제
            selectAll.addEventListener("change", function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
                updateSelectedText();
            });

            // 개별 체크박스 변경 시 전체 선택 체크박스 상태 변경
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener("change", function() {
                    const total = checkboxes.length;
                    const checkedCount = document.querySelectorAll(".item:checked").length;
                    selectAll.checked = (checkedCount === total);
                    selectAll.indeterminate = (checkedCount > 0 && checkedCount < total);
                    updateSelectedText();
                });
            });

            // 선택된 항목 텍스트 업데이트 함수
            function updateSelectedText() {
                const selected = Array.from(document.querySelectorAll(".item:checked"))
                    .map(checkbox => checkbox.value);
                selectedText.textContent = selected.length > 0
                    ? `선택된 항목: ${selected.join(", ")}`
                    : "선택된 항목: 없음";

                // 선택된 항목 값을 hidden input에 업데이트
                selectedItemsInput.value = selected.join(",");
            }
        });
    </script>
    </body>
@endsection



@php
    function fnMaskCryptoCode($cryptoCode) {
        $nLength = strlen($cryptoCode);
        $strFirstPart = substr($cryptoCode, 0, 2);
        $strLastPart = substr($cryptoCode, -3);
        $strModifyPart = str_repeat('*', $nLength - 4);
        $strReturnCode = $strFirstPart . $strModifyPart . $strLastPart;
	return $strReturnCode;
    }
@endphp