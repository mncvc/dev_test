<?php

namespace App\Http\Controllers;

use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

define('LOG_PATH', '/home/mania/kms_admin/storage/app/');
class StatsController extends Controller
{
    public function index()
    {
//        echo $_SERVER['HTTP_REFERER'];
        $CStatsMasterDb = DB::connection("StatsDbConnection");
        $rgCryptoHelper = $CStatsMasterDb
            -> select("SELECT * FROM user_stats_list");
        return view('admin.StatsList', ['rgData' => $rgCryptoHelper, 'title' => '통계']);
    }
    // 통계 생성
    public function statsProcess(Request $request)
    {
        $CStatsDbConnection = DB::connection("StatsDbConnection");

        $rgParams = collect($request->all());
        $nSeq = $rgParams['0'];
//        해당 통계 조회
        # 상태 체크
        echo $nSeq;

        try{
            $dtNow = date('Y-m-d H:i:s');

//        상태 변경
//            $CStatsDbConnection->beginTransaction();
            $rstSelectStats = $CStatsDbConnection->table('user_stats_list')->select('*')->where('seq','=',$nSeq)->first();

            $rgStats = collect($rstSelectStats);

            $fp = fopen(LOG_PATH.$rgStats['seq'] ."_". $rgStats['its_key'] .".log",'a');
            fwrite($fp,"######################### " . $dtNow . " #########################\n");
            fwrite($fp,"1. STATS_SEQ: " . $rgStats['seq'] . "\n");
            fwrite($fp,"2. ITS_KEY: " . $rgStats['its_key'] . "\n");
            fwrite($fp,"3. Author: " . $rgStats['user_id'] . "\n");
            fwrite($fp,"4. subject: " . $rgStats['subject'] . "\n");

            # 쿼리 생성
            $strOutputName = $CStatsDbConnection->table('output_fields')->select('output_field')->where('user_stats_list_seq',$rgStats['seq'])->get()->first();


            $qrySelectStats = "SELECT " . $strOutputName-> output_field . " FROM ELK_LOG.web_log WHERE reg_date BETWEEN '". $rgStats['start_date'] . " 00:00:00' AND '" . $rgStats['end_date'] . " 23:59:59'";
            if($rgStats['cron_flag'] !== 'n'){
                $CStatsDbConnection->rollback();
                echo "flag: " . $rgStats['cron_flag'] ;
            }

            $nAffectedRows = $CStatsDbConnection
                ->table('user_stats_list')
                ->where('seq',$nSeq)
                ->update(['cron_flag' => 'd','cron_date' => $dtNow]);
//
            if($nAffectedRows != 1){
                $CStatsDbConnection->rollback();
                echo "error: update" ;
            }


            $CStatsDbConnection->commit();


            $strWhereTable = '';
            $bCheckTarget = 'n';
            if($rgStats['target_field'] != ''){
                $bCheckTarget = 'y';
                $strItsKey = str_replace("-", "_", $rgStats['its_key'] );
                $strWhereTable = $strItsKey ."_" .$rgStats['target_field'];

                $rstTargetData = $CStatsDbConnection->table($strWhereTable)->select('*')->get();
//                $rgTargetData = collect($rstTargetData);


                $arrTargetData = array();
                foreach($rstTargetData as $result){
                    array_push($arrTargetData, $result->key_name);
                }
                $targetInData = implode("','", $arrTargetData);

                $qrySelectStats .= " AND " . $rgStats['target_field'] . " IN ( '" . $targetInData . "' )";


            }

            fwrite($fp,"5. Query: " . $qrySelectStats . "\n");

            fclose($fp);
            //로그 생성1 (결과산출 로그)
            $fpResult = fopen(LOG_PATH. "log/" . $rgStats['seq'] ."_". $rgStats['its_key'] .".log",'a');
            $strOutputName = $CStatsDbConnection->table('output_names')->select('output_name')->where('user_stats_list_seq',$rgStats['seq'])->get()->first();

            fwrite($fpResult,$strOutputName->output_name . "\n");
            fclose($fpResult);


//        통계 실행 (프로세스)
            $rstStatsResult = $CStatsDbConnection->select($qrySelectStats);

            $fpResult = fopen(LOG_PATH. "log/" . $rgStats['seq'] ."_". $rgStats['its_key'] .".log",'a');

            foreach($rstStatsResult as $strName => $strValue){
                $strResult = "";
                foreach($strValue as $strValue_k => $strValue_v){
                    $strResult .= "," . $strValue_v;
                }
                $strResult = substr($strResult, 1);
                fwrite($fpResult, $strResult . "\n");
            }
//         target table 세팅
            fclose($fpResult);

//        통계 완료
            $nAffectedRows = $CStatsDbConnection
                ->table('user_stats_list')
                ->where('seq',$nSeq)
                ->update(['cron_flag' => 's']);
            if($nAffectedRows < 1 ){
                throw new UpdateException('Err');
            }
//        결과 산출

//            echo '통계 실행중';
//        return redirect('/stats');
            unset($CStatsDbConnection);
        }catch(Exception $e){
            $nAffectedRows = $CStatsDbConnection
                ->table('user_stats_list')
                ->where('seq',$nSeq)
                ->update(['cron_flag' => 'c']);
        }

    }

//    # 통계 요청 ( 통계 생성 프로세스 ) 통계 산출 x
    public function setStats(Request $request){

        $CStatsDbConnection = DB::connection("StatsDbConnection");
        // DB 체크

        $bCheckJoin = 'n';

        $rgParams = collect($request->all());

        try {
            if (empty($CStatsDbConnection)) {
//                throw new Exception();
                dd('here');
            }

            $strWhereTable = '';
            if(empty($rgParams['result_target'])){
                $strResultTarget = '*';
            }else {
                $strResult = implode(',',$rgParams['result_target']);
                $strResultTarget =  $strResult;
            }

//         조건 테이블 생성
            $strWhereJoin = '';
            $strWhereQuery = '';

            if($rgParams['join_target'] !== 'none') {
//                $bCheckJoin = 'y';

                $strItsKey = str_replace("-", "_", $rgParams['its_key'] );
                $strWhereTable = $strItsKey ."_" .$rgParams['join_target'];
                $qryCreateTable = "CREATE TABLE IF NOT EXISTS stats." . $strWhereTable;
                $qryCreateTable .= " (
                      `key_name` varchar(30) NOT NULL COMMENT '" . $rgParams['join_target'] . "',
                      PRIMARY KEY (`key_name`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
                    ";
                $CStatsDbConnection->statement($qryCreateTable);

                $arrTargetJoin = array();
                $arrTargetJoin = explode(',', $rgParams['join_target_v']) ;
                $strTargetJoin = implode(',', $arrTargetJoin);
                foreach( $arrTargetJoin as $keyname){
//                    $strKey = "('" . $keyname . "')";
//                    $qryInsertData = "INSERT INTO $strWhereTable VALUES " . $strKey ;
//                    echo $qryInsertData . "\n";

                    $CStatsDbConnection
                        ->table($strWhereTable)
                        ->insert([
                            'key_name' => $keyname
                        ]);
                }
//                SELECT TABLE_SCHEMA, TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA = 'stats' AND TABLE_NAME LIKE '%stats%';
                $rstResult = $CStatsDbConnection
                    -> table('information_schema.TABLES')
                    -> select('TABLE_SCHEMA', 'TABLE_NAME')
                    -> where([
                        ['TABLE_SCHEMA','stats'],
                        ['TABLE_NAME', $strWhereTable]
                    ])->get();

                var_dump($rstResult);

                if(!isset($rstResult)){
                    echo 'error';
                }

//                $strWhereQuery = " WHERE ";
//                조건 존재
                $strWhereJoin = $rgParams['join_target'];

            }

            $rgStatsInfo = [
                'its_key'           => $rgParams['its_key'],
                'user_id'           => $request->session()->get('admin_id'),
                'subject'           => $rgParams['subject'],
                'start_date'        => $rgParams['start_date'],
                'end_date'          => $rgParams['end_date'],
                'stats_project'     => '000000001',
                'project_name'      => '웹 > 로그',
                'target_field'      => $strWhereJoin,
                'join_table'        => $bCheckJoin,
                'group_field'       => '',
                'reg_date'          => date('Y-m-d'),
                'reg_time'          => date('H:i:s')
            ];

            $CStatsDbConnection->beginTransaction();

//            $nResult = 1;
            $nResult = $CStatsDbConnection
                ->table('user_stats_list')
                ->insertGetId($rgStatsInfo );

            $strInsertOutPutNames = "";
            $strInsertOutPutFields = "";
            $rgOutPutNames = [
                'its_key'                => $rgParams['its_key'],
                'user_stats_list_seq'    => $nResult,
                'output_name'            => $strResultTarget
            ];
            
            $rgOutPutFields = [
                'its_key'                       => $rgParams['its_key'],
                'user_stats_list_seq'           => $nResult,
                'output_field'                  => $strResultTarget,
            ];

            $nOutPutNameUid = $CStatsDbConnection
                ->table('output_names')
                ->insertGetId($rgOutPutNames);

            if($nOutPutNameUid > 1){
                echo '[Err1]';
                $CStatsDbConnection->rollback();
            }

            $nOutPutFieldUid = $CStatsDbConnection
                ->table('output_fields')
                ->insertGetId($rgOutPutFields);

            if($nOutPutFieldUid > 1){
                echo '[Err2]';
                $CStatsDbConnection->rollback();
            }

            $CStatsDbConnection->commit();

        }catch(Exception $e){
            $CStatsDbConnection->rollback();
        }finally{
            return redirect('/stats');
        }
    }

# 통계 실행
    public function create()
    {
        return view('admin.StatsReg', [ 'title' => '통계 생성']);

    }


    public function checkExistsTable($strDbName, $strTableName){
        $qrySelect = "SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME LIKE '{seq}_{its_key}%' ";
        
        
        
        
    }






}



/*
 * sql
 *  SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME LIKE '{seq}_{its_key}%' ;
 * SELECT TABLE_SCHEMA, TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA = 'stats' AND TABLE_NAME LIKE '%stats%';
 *
 *
 * */