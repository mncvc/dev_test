<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PragmaRX\Google2FAQRCode\Google2FA;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class GoogleAuthController extends Controller
{
    public function enable2fa(Request $request)
    {
        $rgSession = collect($request->session()->all());
        $google2fa = new Google2FA();

        $secret = '';
        // 비밀키를 사용자에게 저장
        $CAdminInfoMasterDb = DB::connection("AdmininfoDbConnection");
        // 회원 OTP 사용 여부 체크
        $rstAdminSecret = $CAdminInfoMasterDb->table('admin_google_auth_list')->select('*')->where('admin_no','=', $rgSession['admin_no'])->where('use_flag','=','Y');
        $rgAdminGoogleAuth = collect($rstAdminSecret->first());

        if($rgAdminGoogleAuth->isEmpty()){
            // 비밀키 생성
            $secret = $google2fa->generateSecretKey();

            $rgAdminGoogleAuth = [
                'admin_no'      => $rgSession['admin_no'],
                'secret_key'   => $secret,
                'use_flag'  =>  'Y',
                'reg_ip'   => $_SERVER["REMOTE_ADDR"]
            ];

            $nResult = $CAdminInfoMasterDb
                ->table('admin_google_auth_list')
                ->insertGetId($rgAdminGoogleAuth );
            // 체크
        }else $secret = $rgAdminGoogleAuth['secret_key'];

        // QR 코드 생성 (Google Authenticator로 스캔할 수 있는 URL)
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'KMS_관리자',
            $rgSession['admin_id'],
            $secret
        );

        unset($google2fa);
        // 사용자가 QR 코드를 스캔할 수 있도록 이미지 생성
        return view('admin.auth', ['qrCodeUrl' => $qrCodeUrl, 'secret' => $secret]);
    }

    public function verify2fa(Request $request)
    {
        try{
            $rgSession = collect($request->session()->all());
            $rgParams = collect($request->all());

    //         비밀키를 사용자에게 저장
            $CAdminInfoMasterDb = DB::connection("AdmininfoDbConnection");

            // 회원 OTP 사용 여부 체크
            $rstAdminSecret = $CAdminInfoMasterDb->table('admin_google_auth_list')->select('*')->where('admin_no','=', $rgSession['admin_no'])->where('use_flag','=','Y');

            $rgAdminGoogleAuth = collect($rstAdminSecret->first());
            $strSecretKey = $rgAdminGoogleAuth['secret_key'];

            $google2fa = new Google2FA();

            // 사용자가 입력한 OTP와 서버에 저장된 비밀키로 검증
            $valid = $google2fa->verifyKey($strSecretKey, $rgParams['otp']);

            if ($valid) {
                // OTP가 유효할 경우
                $rstAdminInfo = $CAdminInfoMasterDb->table('admin_info_new')
                    ->select('*')
                    ->join('admin_private_info','admin_info_new.admin_no','=','admin_private_info.admin_no')
                    ->where('admin_info_new.admin_id',$rgSession['admin_id'] );

                $rgAdminInfo = collect($rstAdminInfo->first());

                if($rgAdminInfo->isEmpty()){
                    return view('admin.login',['result'=>2, 'message'=>'존재하지 않는 아이디 입돠']);
                }

                $salt = base64_decode($rgAdminInfo['admin_salt']);
                $strEncPassword = base64_encode(sodium_crypto_auth($rgSession['password'], $salt));

                // auth::attempt 적용
                $rgCheckLogin = [
                    'admin_no' => $rgAdminInfo['admin_no'],
                    'password'  => $strEncPassword
                ];
//                dd(Auth::attempt($rgCheckLogin));

                if(Auth::attempt($rgCheckLogin)){
                    $request->session()->forget('password');

                    return redirect()->intended('admin');
                }

            }

         }catch(Exception $e){

            return view('admin.login',['result'=>2, 'message'=>'오류 발생']);
        } finally
        {
            unset($google2fa);
        }

    }

//showOtp
    public function showOtp(Request $request){

        return view('admin.otpAuth');
    }
}
