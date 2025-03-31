
@extends('layout.header')
@section('content')
    @php

        $divideValue = ($rgInfo['total'] - ($rgInfo['total'] % 10)) / 10;
        $i=1;

 @endphp


    <body>


        <form>
            <div class="tool-wrap">
                <div class="search-box">

                    <input type="number" placeholder="검색" name="search" id="search_box" value="{{$rgInfo['search']}}">
        {{--            <input type="button" onclick="onClickEvent()" value="search">--}}
                    <input type="submit" value="검색">
                </div>
            </div>
            <table border="1" cellpadding="10" cellspacing="0" >
                <thead>
                <tr>
                    <th>NO</th>
                    <th>회원번호</th>
                    <th>서비스 아이피</th>
                    <th>구분</th>
                    <th>상태</th>
                    <th>로그정보</th>
                    <th>로그내용</th>
                    <th>등록일시</th>
                </tr>
                </thead>
                <tbody>
                @foreach($rgInfo['posts'] as $data)
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $data['user_no'] }}</td>
                        <td>{{ $data['client_ip'] }}</td>
                        <td>{{ $data['type'] }}</td>
                        <td>{{ $data['status'] }}</td>
                        <td>{{ $data['title'] }}</td>
                        <td>{{ $data['content'] }}</td>
                        <td>{{ $data['reg_date'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <form action="" method="get">
                @for($i = 0 ; $i <= $divideValue; $i++)
                    <input type="submit" value="{{$i}}" name="page">
                @endfor
            </form>

        </form>


{{--    페이징네이션--}}


    
    </body>

    <script>
        function onClickEvent(){
            console.log('here');
            let search_word = document.getElementById('search_box').value;
            location.href='?search='+search_word;

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
@endphp