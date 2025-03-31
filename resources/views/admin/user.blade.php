@extends('layout.header')
@section('content')
    <body>
        <div class="tool-wrap">
            <div class="search-box">

            </div>
            <div class="btn_create_key"  onclick="window.open('/signup','cp','width=600, height=800, scrollbars=yes')">
                <span>회원 생성</span>
            </div>
        </div>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
            <tr>
                <th>회원번호</th>
                <th>아이디</th>
                <th>이름</th>
                <th>소속부서</th>
                <th>직책</th>
                <th>가입일</th>
                <th> 변경 </th>
            </tr>
            </thead>
            <tbody>
            @foreach($rgData as $data)
                <tr>
                    <td>{{ $data->admin_no }}</td>
                    <td>{{ $data->admin_id }}</td>
                    <td>{{ $data->admin_name }}</td>
                    <td>{{ fnChangeDepartCode($data->depart_code) }}</td>
{{--                    <td>{{ $data->user_type }}</td>--}}
                    <td>{{ fnChangePositionCode($data->position_code) }}</td>
                    <td>{{ $data->reg_date }}</td>
                    <td><input type="button" value="상세보기" onClick="openAdminInfo({{$data->admin_no}})"></td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </body>
@endsection
<script language="JavaScript">
    function openAdminInfo(adminNo){
        window.open('/member/detail?adminNo='+adminNo,'cp','width=600, height=800, scrollbars=yes');

    }

</script>

@php

    function fnChangeDepartCode($departCode) {
        $strReturnCode = '';
        if($departCode == '01'){
            $strReturnCode = 'T/D 팀';
        }else if ($departCode == '02'){
            $strReturnCode = 'S/D 팀';
        }else $strReturnCode = '-';

	return $strReturnCode;
    }

        function fnChangePositionCode($positionCode) {
        $strReturnCode = '';
        if($positionCode == '01'){
            $strReturnCode = '연구원';
        }else if ($positionCode == '02'){
            $strReturnCode = '선임 연구원';
        }else if ($positionCode == '03'){
            $strReturnCode = '책임 연구원';
        }else if ($positionCode == '04'){
            $strReturnCode = '팀장';
        }else $strReturnCode = '-';

	return $strReturnCode;
    }

@endphp