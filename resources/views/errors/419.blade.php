@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')

<script>
    alert("세션이 만료 되었습니다. ")

    window.location.href = 'http://192.168.56.108:8080/login';
</script>

@section('message', __('Page Expired'))
