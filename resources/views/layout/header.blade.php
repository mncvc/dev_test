
<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            margin: 10px auto;
            padding: 0;
            background-color: #f5f5f5;
            height: 100vh;
            color: #333;
        }
        a{
            text-decoration: none;
            color:inherit;
        }
        ul,li{
            list-style: none;
        }

        table {
            width:100%;
            margin-top : 50px;
        }
        th{
            height: 20px;
            background-color: antiquewhite;
        }

        td{
            color: #343434;
            text-align: center;
        }

        .container{
            margin:0 auto;
            padding:0 40px;
            max-width:1200px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .header-wrap{
            width:100%;
            height:80px;
            display:flex;
            justify-content: space-between;
        }
        .top-log{
            display: flex;
            align-items: center;
            padding:0 5px;
            height:100%;
            font-size: 1.75rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            text-align: center;
            color: #4a90e2;
        }
        .userinfo-wrap{
            display: flex;
            align-items: center;
            justify-content: space-between;
            width:300px;
            height:100%;
        }
        .logout_btn{
            border:1px solid black;
            background-color:#888;
            color:white;
            padding: 2px 4px;
        }
        .menu-wrap {
            margin: 0;
            display:flex;
            justify-items: flex-start;
            align-items: center;
        }
        .itm-list{
            padding:3px 10px;
            margin:0 5px;
        }
        .itm-list:hover{
            color : blue;
        }
        .tool-wrap{
            display: flex;
            justify-content: space-between;
            width: 100%;
            height:50px;
        }
        .search-box{
            width: 700px;
            border: 1px solid black;
            margin-left: 40px;
        }
        /* 생성 버튼 */
        .btn_create_key {
            width: 100px;
            padding: 0.75rem;
            text-align: center;
            background-color: #4a90e2;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn_create_key:hover {
            background-color: #357abd;
        }
        .btn_create_key:focus {
            outline: none;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.5);
        }

    </style>
</head>
<body>
<div class="container">
    <div class="header-wrap">
        <div class="top-log">KMS 관리자</div>
        <div class="userinfo-wrap">
            IT 기술연구소 / {{session('admin_name')}} 님
            <div class="logout_btn"><a href="/logout">로그아웃</a></div>
        </div>
    </div>
        <ul class="menu-wrap">
            <li class="itm-list"><a href="/admin">키 관리</a></li>
            <li class="itm-list"><a href="/user">회원 관리</a></li>
            <li class="itm-list"><a href="/log">로그관리</a></li>
        </ul>
    <hr/>
@yield('content')
</div>



</body>
</html>