<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Library\Common;
use App\Library\CustomLog;

use PragmaRX\Google2FAQRCode\Google2FA;
use Elasticsearch\Client;

class UserController extends Controller
{
    protected $rstLogData=[
        'user_no' => 0,
        'client_ip' => '',
        'type' => '', // a:로그인, b:로그아웃, c:,d:,e:,f:,g:
        'status' => '',
        'title' => '',
        'content' => ''
    ];
    public function __construct()
    {}


// 로그인 페이지
    public function index(){
        if(Auth::check()){
            return redirect('admin');
        }

        return view('admin.login',['result'=>1,'message'=>'']);
    }

// 로그인
    public function login(Request $request){
        $cLog = new CustomLog();
        $this->rstLogData['client_ip'] = $request->ip();
        $this->rstLogData['type'] = 'a';
        $this->rstLogData['title'] = '로그인(1차)';

        try{
            if(Auth::check()){
                return redirect('admin');
            }

            $rgParams = collect($request->all());
            // 파라미터 정합성 체크
            if ($rgParams->get('userId') == null) {
                $this->rstLogData['status'] = 'fail';
                $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 실패 (아이디 미입력)';

                throw new Exception("회원 아이디를 입력 해주세요!", ERR_BACK);
            }
            if ($rgParams->get('password') == null) {

                $this->rstLogData['status'] = 'fail';
                $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 실패 (패스워드 미입력)';

                throw new Exception("1차 비밀번호를 입력 해주세요!");
            }
            // 파라미터 정합성 체크

            //DB 연결
            $CAdminInfoMasterDb = DB::connection("AdmininfoDbConnection");
            // DB 체크
            if (empty($CAdminInfoMasterDb)) {
                throw new Exception('Err');
            }

            $cCommon = new Common($CAdminInfoMasterDb);

            $rstAdminInfo = $CAdminInfoMasterDb->table('admin_info_new')
                ->select('*')
                ->join('admin_private_info','admin_info_new.admin_no','=','admin_private_info.admin_no')
                ->where('admin_info_new.admin_id',$rgParams->get('userId'));

            $rgAdminInfo = collect($rstAdminInfo->first());

            if($rgAdminInfo->isEmpty()){

                $this->rstLogData['status'] = 'fail';
                $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 실패 (회원정보 미일치)';

                return view('admin.login',['result'=>2, 'message'=>'입력한 회원 정보가 정확하지 않습니다.1']);
            }

            $this->rstLogData['user_no'] = $rgAdminInfo['admin_no'];

            $salt = base64_decode($rgAdminInfo['admin_salt']);
            $strEncPassword = base64_encode(sodium_crypto_auth($rgParams->get('password'), $salt));

            if($rgAdminInfo['admin_state'] != 1){
                $strErrMessage = '';
                if($rgAdminInfo['admin_state'] == 2){
                    $strErrMessage = '휴면 회원 입니다. 관리자에게 문의 해주세요!';
                }else if ($rgAdminInfo['admin_state'] == 3){
                    $strErrMessage = '로그인 5회 오류 회원 입니다. 관리자에게 문의 해주세요!' ;
                }else if ($rgAdminInfo['admin_state'] == 4){
                    $strErrMessage = '퇴사 처리된 계정입니다. 관리자에게 문의 해주세요 !' ;
                }else $strErrMessage = '회원 상태 값이 존재 하지 않습니다. 관리자에게 문의 해주세요!';

                // 로그인 실패
                $cCommon->fnLoginLog($rgAdminInfo['admin_no'], 'N');

                $this->rstLogData['status'] = 'fail';
                $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 실패 (회원상태 : ' . $rgAdminInfo['admin_state'] . ')';

                return view('admin.login',['result'=>2, 'message'=>$strErrMessage]);
            }

            if($rgAdminInfo['admin_password'] !== $strEncPassword){
                // 로그인 실패
                if($cCommon->fnLoginLog($rgAdminInfo['admin_no'], 'N') === 0){
                    return view('admin.login',['result'=>2, 'message'=>'로그 작성 실패']);
                }

                if($cCommon->fnCheckLoginLog($rgAdminInfo['admin_no']) === 0){
                    return view('admin.login',['result'=>2, 'message'=>'관리자 상태 체크 실패']);
                }
                $this->rstLogData['status'] = 'fail';
                $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 실패 (패스워드 미일치)';

                return view('admin.login',['result'=>2, 'message'=>'입력한 회원 정보가 정확하지 않습니다.2']);
            }

            $chrGoogleAuthFlag=$rgAdminInfo['google_auth_flag'];

            if($request->session()->has('admin_no')){
                $request->session()->flush();
            }

            $rgSessionAdminInfo = [
                'admin_no' => $rgAdminInfo['admin_no'],
                'admin_id' => $rgAdminInfo['admin_id'],
                'admin_name'=> $rgAdminInfo['admin_name'],
                'password'=>$rgParams->get('password'),
                'login_step' => 1,
                'google_auth_flag' => $chrGoogleAuthFlag
            ] ;

            $this->rstLogData['status'] = 'success';
            $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 성공 (1차)';

            $request->session()->put($rgSessionAdminInfo);
            return view('admin.otpAuth');

        }catch(Exception $e){
            $request->session->flush();

            $this->rstLogData['status'] = 'fail';
            $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 실패';

            return view('admin.login',['result'=>2, 'message'=>$e]);
        }finally{

            $cLog->fnSetLog($this->rstLogData);
        }

    }

    public function showOtp(Request $request){
        return view('admin.otpAuth');
    }
// otp QR코드 생성
    public function createQr(Request $request)
    {
        $rgSession = collect($request->session()->all());
        $google2fa = new Google2FA();

        $secret = '';
        // 비밀키를 사용자에게 저장
        $CAdminInfoMasterDb = DB::connection("AdmininfoDbConnection");
        $cCommon = new Common($CAdminInfoMasterDb);

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

            $cCommon->fnChangeAdminInfo($rgSession['admin_no'], 5,'Y');

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
// otp 인증
    public function otpProcess(Request $request)
    {
        $cLog = new CustomLog();

        try{
            $rgSession = collect($request->session()->all());
            $rgParams = collect($request->all());

            // 로그
            $this->rstLogData['user_no'] = $rgSession['admin_no'];
            $this->rstLogData['client_ip'] = $request->ip();
            $this->rstLogData['type'] = 'a';
            $this->rstLogData['title'] = '로그인(2차)';


            //         비밀키를 사용자에게 저장
            $CAdminInfoMasterDb = DB::connection("AdmininfoDbConnection");
            $cCommon = new Common($CAdminInfoMasterDb);

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
                    $this->rstLogData['status'] = 'fail';
                    $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 실패 (회원정보 미존재)';

                    return view('admin.login',['result'=>2, 'message'=>'존재하지 않는 아이디 입니다.']);
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
                    $cCommon->fnLoginLog( $rgSession['admin_no'], 'Y');

                    $this->rstLogData['status'] = 'success';
                    $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 성공(2차)';

                    return redirect()->intended('admin');

                }else {
                    $cCommon->fnLoginLog( $rgSession['admin_no'], 'N');
                    $cCommon->fnCheckLoginLog($rgSession['admin_no']);

                    $this->rstLogData['status'] = 'fail';
                    $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 실패 ';

                    return redirect()->intended('otpAuth');
                }

            }else{
                $cCommon->fnLoginLog( $rgSession['admin_no'], 'N');
                $cCommon->fnCheckLoginLog($rgSession['admin_no']);

                $this->rstLogData['status'] = 'fail';
                $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그인 실패 (OTP 불일치)';

                return redirect()->intended('otpAuth');
            }

        }catch(Exception $e){
            $cCommon->fnLoginLog( $rgSession['admin_no'], 'N');
            $cCommon->fnCheckLoginLog($rgSession['admin_no']);

            return view('admin.login',['result'=>2, 'message'=>'오류 발생']);
        } finally {
            $cLog->fnSetLog($this->rstLogData);
        }

    }

    public function otpReset(Request $request)
    {
        $dtNow = date('Y-m-d H:i:s');
        $nUserNo = $request->input('admin_no');
        $rgSession = collect($request->session()->all());
        $CAdminInfoMasterDb = DB::connection("AdmininfoDbConnection");
        try {
        // 해당 회원 정보 조회

            // admin_info n
            // admin_google_auth_list -> n

            // 임시 패스워드 생성 및 저장 (예시)
            $CAdminInfoMasterDb->beginTransaction();

            $nAffected1  = $CAdminInfoMasterDb->table('admin_info_new')->where('admin_no',$nUserNo)->update(['google_auth_flag'=>'N']);
//            dd($nAffected1);
            if($nAffected1 != 1){
                $CAdminInfoMasterDb->rollback();
                return response()->json(['message' => '회원 정보를 찾을 수 없습니다1.'], 401);
            }

            $nAffected2 = $CAdminInfoMasterDb->table('admin_google_auth_list')->where('admin_no',$nUserNo)->where('use_flag','Y')->update(['use_flag'=>'N', 'delete_date'=>$dtNow, 'delete_admin_no'=>$rgSession['admin_no'], 'delete_ip'=>$_SERVER["REMOTE_ADDR"]]);

            if($nAffected2 != 1){
                $CAdminInfoMasterDb->rollback();
                return response()->json(['message' => '회원 정보를 찾을 수 없습니다2.'], $nAffected2);
            }

            $CAdminInfoMasterDb->commit();

            // 응답 반환
            return response()->json(['message' => 'OTP 인증 초기화되었습니다.'], 200);

        } catch(Exception $e) {
            $CAdminInfoMasterDb->rollback();
            return response()->json(['message' => '회원 정보를 찾을 수 없습니다3'], 403);
        }
    }
    public function logout(Request $request){
        $cLog = new CustomLog();

        $this->rstLogData['user_no'] = $request->session()->get('admin_no');
        $this->rstLogData['client_ip'] = $request->ip();
        $this->rstLogData['type'] = 'a';
        $this->rstLogData['status'] = 'success';
        $this->rstLogData['title'] = '로그아웃';
        $this->rstLogData['content'] = '[회원번호: ' . $this->rstLogData['user_no'] . '] - 로그아웃 성공';

        //session에 admin_no 존재 여부 체크
        //session이 존재하면 session 전체 삭제
        if($request->session()->has('admin_no')){
            $request->session()->flush();
        }
        // auth 인증 로그아웃.
        Auth::logout();
        $cLog->fnSetLog($this->rstLogData);

        return redirect()->intended('admin')->with('jsAlert', '로그아웃 되었습니다.');
    }

// 회원 가입 페이지
    public function signUpForm(){
        return view('admin.signup');
    }

/*
 *  회원가입
 * userId,password,userName,userDepart,position,employee_no
 * */
    public function signUpProcess(Request $request){
        $cLog = new CustomLog();
        //

        $this->rstLogData['user_no'] = $request->session()->get('admin_no');
        $this->rstLogData['client_ip'] = $request->ip();
        $this->rstLogData['type'] = 'b'; // 계정 생성
        $this->rstLogData['title'] = '계정생성';

        try {

            $rgParams = collect($request->all());

            // 정합성 검사
            $validatedAdmin = Validator::make($rgParams->toArray(),[
                'userId'        => ['required','max:20'],
                'password'      => ['required'],
                'userName'      => ['required', 'alpha'],
                'userDepart'    => ['required', 'max:2'],
                'position'      => ['required'],
                'employeeNo'   => ['required', 'max:6'],
            ]);

            // 회원 가입 관련 변수 세팅 (** key: 값 수정 필수 테스트 목적으로 '1234' 적용)
            $rgData['userId']           = $rgParams->get('userId');
            $rgData['password']         = $rgParams->get('password');
            $rgData['userName']         = $rgParams->get('userName');
            $rgData['userDepart']       = $rgParams->get('userDepart');
            $rgData['position']         = $rgParams->get('position');
            $rgData['phone']            = $rgParams->get('phone');
            $rgData['employeeNo']       = $rgParams->get('employeeNo');
            // 회원 가입 관련 변수 세팅 끝

            $this->rstLogData['content'] =  $cLog->fnSetLogMessage($this->rstLogData['content'], "i", $rgData);

            if($validatedAdmin->fails()){
                $this->rstLogData['status'] = 'fail';
                $this->rstLogData['content'] .=  $cLog->fnSetLogMessage("", "r", array('error' => "회원생성 실패 입력값 이상"));


                return view('admin.popUpResult', ['message'=>'회원가입 실패']);
            }

            // DB 연결
            $CAdminInfoMasterDb = DB::connection("AdmininfoDbConnection");
            // DB 체크
            if (empty($CAdminInfoMasterDb)) {
                $CAdminInfoMasterDb->rollback();
                return view('admin.popUpResult', ['message'=>'회원가입 실패']);
            }

            // 데이터 존재 여부 체크 (중복가입)
            $rstAdminInfo = $CAdminInfoMasterDb->table('admin_info_new')
                ->select('*')
                ->join('admin_private_info', 'admin_info_new.admin_no', '=', 'admin_private_info.admin_no')
                ->where('admin_info_new.admin_id', $rgData['userId']);

            $isCheckDuplicate = collect($rstAdminInfo->first());

            if(!$isCheckDuplicate -> isEmpty()){
                $this->rstLogData['status'] = 'fail';
                $this->rstLogData['content'] .=  $cLog->fnSetLogMessage("", "r", array('error' => "중복계정"));

                $CAdminInfoMasterDb->rollback();
                return view('admin.popUpResult', ['message'=>'회원가입 실패']);
            }

            // 패스워드 생성
            $salt = sodium_crypto_auth_keygen();
            $strEncryptPwd = base64_encode(sodium_crypto_auth($rgData['password'], $salt));

            // 연락처 암호화
            $strEncryptPhone = encrypt($rgData['phone']);

            $CAdminInfoMasterDb->beginTransaction();

            $rgAdminInfo = [
                'admin_id'      => $rgData['userId'],
                'admin_level'   => '1',
                'depart_code'   => $rgData['userDepart'],
                'position_code'  =>  $rgData['position']
            ];

            $nResult = $CAdminInfoMasterDb
                ->table('admin_info_new')
                ->insertGetId($rgAdminInfo);

            $rgAdminPrivateInfo = [
                'admin_no'          => $nResult,
                'admin_name'        => $rgData['userName'],
                'admin_password'    => $strEncryptPwd,
                'admin_salt'        => base64_encode($salt),
                'admin_allow_ip'    => '0.0.0.0',
                'admin_mobile'       => $rgData['phone'],
                'employee_no'       => $rgData['employeeNo']
            ];

            $nResult2 = $CAdminInfoMasterDb
                ->table('admin_private_info')
                ->insertGetId($rgAdminPrivateInfo);

            $this->rstLogData['status'] = 'success';
            $this->rstLogData['content'] .=  $cLog->fnSetLogMessage("", "r", $rgAdminPrivateInfo);

            $CAdminInfoMasterDb->commit();

//            return redirect('user');

            return view('admin.popUpResult', ['message'=>'회원가입']);
        }catch(Exception $e){
            echo $e;
        } finally {
            $cLog->fnSetLog($this->rstLogData);
        }

    }

    // 회원 정보 조회
    public function showUser(){
        try {
            // DB 연결
            $CAdminInfoMasterDb = DB::connection("AdmininfoDbConnection");
            // DB 체크
            if (empty($CAdminInfoMasterDb)) {
                throw new Exception('Err');
            }
            $rgResult = $CAdminInfoMasterDb->table('admin_info_new as a')->join('admin_private_info as b','a.admin_no','=','b.admin_no')->select('a.*','b.admin_name')->limit(10)->get();
            
            return view('admin.user', ['rgData' => $rgResult, 'title' => '회원 정보']);
        }catch(Exception $e){
            echo $e;
        }
    }

    public function detail(Request $request){
        $userNo = $request->get('adminNo');
        if(empty($userNo)){
            throw new Exception('회원 번호가 정확하지 않습니다.');
        }

        try{
            $CAdminInfoMasterDb = DB::connection("AdmininfoDbConnection");
            if (empty($CAdminInfoMasterDb)) {
                throw new Exception('Err');
            }

            $rgData = $CAdminInfoMasterDb->table('admin_info_new as a')->join('admin_private_info as b','a.admin_no','=','b.admin_no')->select('*')->where('a.admin_no','=',$userNo)->first();

            if(empty($rgData)){
                dd('err');
            }
//            dd($rgData);
            return view('admin.adminDetail', ['rgData'=>$rgData]);
        }catch(Exception $e){

        }

    }

    public function myPage(Request $request){

        $rgSession = $request->session()->all();
//        dd($rgSession['admin_no']);
        if(empty($rgSession['admin_no'])){
            throw new Exception('회원 번호가 정확하지 않습니다.');
        }

        try{
            $CAdminInfoMasterDb = DB::connection("AdmininfoDbConnection");
            if (empty($CAdminInfoMasterDb)) {
                throw new Exception('Err');
            }

            $rgData = $CAdminInfoMasterDb->table('admin_info_new as a')->join('admin_private_info as b','a.admin_no','=','b.admin_no')->select('*')->where('a.admin_no','=',$rgSession['admin_no'])->first();

            if(empty($rgData)){
                dd('err');
            }
//            dd($rgData);
            return view('admin.myPage', ['rgData'=>$rgData, 'title'=>'마이 페이지']);
        }catch(Exception $e){

        }

    }



// 임시 (암호화)라이브러리
// 암호화 전화번호 암호화 알고리즘 필요 16자리



}
