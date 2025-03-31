@extends('layout.header')
@section('content')
<body>
{{--        <div class="btn_create_key">--}}
{{--            <a href="javascript:;">키 생성</a>--}}
{{--            <a href="javascript:;" onclick="window.open('/admin/create','cp','width=400, height=600, scrollbars=yes');"> 키생성 </a>--}}
{{--        </div>--}}
            <div class="tool-wrap">
                <div class="search-box"></div>

            </div>
        <table border="1" cellpadding="10" cellspacing="0" >
            <thead>
                <tr>
                    <th>NO</th>
                    <th>서비스분류</th>
                    <th>암호코드</th>
                    <th>암호버전</th>
                    <th>유효기간 시작일</th>
                    <th>유효기간 종료일</th>
                    <th>사용여부</th>
                    <th>등록일시</th>
                </tr>
            </thead>
            <tbody>
            @foreach($rgData as $data)
                <tr>
                    <td>{{ $data->seq }}</td>
                    <td>{{ $data->service_type }}</td>
                    <td>{{ fnMaskCryptoCode($data->crypto_code) }}</td>
                    <td>{{ $data->crypto_version }}</td>
                    <td>{{ $data->use_start_date }}</td>
                    <td>{{ $data->use_end_date }}</td>
                    <td>{{ $data->use_flag }}</td>
                    <td>{{ $data->reg_date }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>


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