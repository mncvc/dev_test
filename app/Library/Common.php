<?php
namespace App\Library;


class Common
{
    protected $dbConnection;

    public function __construct($dbConn)
    {
        $this->dbConnection = $dbConn;

        // DB 연결 확인

    }

    // 로그인 로그 기록
    public function fnLoginLog($userNo, $state)
    {
        $dtYear = date('Y');
        try{
        //DB 연결 상태 체크

            $rgLoginLog = [
                'admin_no'          => $userNo,
                'admin_login_ip'    => $_SERVER["REMOTE_ADDR"],
                'admin_login_flag'  =>  $state
            ];

            $nResult = $this->dbConnection->table('admin_login_log_'.$dtYear)->insertGetId($rgLoginLog);

            if(empty($nResult)){
                return 0;
            }

            return 1;
        }catch(Exception $e){
            return 0;
        }
    }

    // 로그인 로그 체크 후 5회이상인 경우 회원 상태 변경
    public function fnCheckLoginLog($userNo)
    {
        $dtYear = date('Y');
        $nFailCount = 0;
        try{
            $rstAdminLoginLog = $this->dbConnection->table('admin_login_log_'.$dtYear)->select('admin_no','admin_login_flag')->where('admin_no','=',$userNo)->orderBy('reg_date','desc')->limit(5)->get();

            foreach($rstAdminLoginLog as $rgAdminLoginLog)
            {
                if($rgAdminLoginLog->admin_login_flag == 'N'){
                    $nFailCount++;
                }else $nFailCount = 0;

                if($nFailCount == 5)
                {
                    $rgAdminInfo = collect($this->dbConnection->table('admin_info_new')->select('*')->where('admin_no',$userNo)->first());

                    if($rgAdminInfo['admin_state'] == '1'){
                        $nResult = $this->fnChangeAdminInfo($userNo, 2, 3);

                        if($nResult == 0){
                            return 0;
                        }

                    }
                }

                return 1;
            }

        }catch(Exception $e){
            return 0;
        }
    }

    // 회원 상태 체크

    // 회원정보 수정
    public function fnChangeAdminInfo($userNo, $targetColNo, $changeValue)
    {
        $rgValidate = [];
        $strTargetCol = '';
        // 컬럼별 도메인 정합성 생성. (1: admin_level, 2: admin_state, 3: depart_code, 4: position_code, 5: google_auth_flag)

        switch ($targetColNo){
            case 1:
                $rgValidate = ['1','2','3','4','5','0'];
                $strTargetCol = 'admin_level';
                break;
            case 2:
                $rgValidate = ['1','2','3','4','5','0'];
                $strTargetCol = 'admin_state';
                break;
            case 3:
                $rgValidate = ['01','02'];
                $strTargetCol = 'depart_code';
                break;
            case 4:
                $rgValidate = ['01','02','03','04'];
                $strTargetCol = 'position_code';
                break;
            case 5:
                $rgValidate = ['Y','N'];
                $strTargetCol = 'google_auth_flag';
                break;
            default :
                return 0;
        }

            if(!in_array($changeValue, $rgValidate)){
                dd('err');
                return 0;
            }

            $rgAdminInfoChange = [
                $strTargetCol => $changeValue
            ];

            $nAffected = $this->dbConnection->table('admin_info_new')->where('admin_no',$userNo)->update($rgAdminInfoChange);

            if($nAffected == 0){
                return 0;
            }

        //
            return 1;
    }


    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        $this->dbConnection = null;

    }
}