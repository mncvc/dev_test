@extends('layout.header')
@section('content')
    <body>
    <div class="tool-wrap">
        <div class="search-box"></div>
    </div>
    <table border="1" cellpadding="10" cellspacing="0" >
        <thead>
        <tr>
            <th>NO</th>
            <th>통계명</th>
            <th>통계 분류</th>
            <th>상태</th>
            <th>실행 여부</th>
        </tr>
        </thead>
        <tbody>
        @foreach($rgData as $data)
            <tr>
                <td>{{ $data->seq }}</td>
                <td>{{ $data->subject }}</td>
                <td>{{ $data->project_name }}</td>
                <td>{{ fnStateKr($data->cron_flag) }}</td>
                @if($data->cron_flag == 'n')
                    <td> <input type="button" onclick="fnStatsProcess({{$data->seq}})" value="실행"></td>
                @elseif($data->cron_flag == 'd')
                    <td> 실행중 </td>
                @else
                    <td> 완료 </td>
                @endif



            </tr>
        @endforeach
        </tbody>
    </table>
    </body>
    <script>
        function fnStatsProcess(seq){
            if (confirm( seq + ' 통계를 실행 하십니까?')) {
                // AJAX 요청
                let xhr = new XMLHttpRequest();
                xhr.open('POST', '/stats/process', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}'); // CSRF 토큰 설정

                let data = seq

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        alert('통계 실행 중');

                    } else {
                        console.log(xhr.status);
                        alert('예기치 못한 오류 발생');
                    }
                };

                xhr.onerror = function() {
                    alert('서버와의 통신 중 오류가 발생했습니다.');
                };

                xhr.send(data);
            }
        }
    </script>



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

    function fnStateKr($chrStateCode) {
       $strReturn = '';

       switch ($chrStateCode){
           case 'n':
               $strReturn = '대기';
               break;
           case 'd':
               $strReturn = '진행';
               break;
           case 's':
               $strReturn = '완료';
               break;
           default:
               $strReturn = '오류';
               break;
       }

	    return $strReturn;
    }


@endphp