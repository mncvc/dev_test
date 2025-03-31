<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
    public function create()
    {

        #
        $rgParams = [
            'type' => '',
            'table' => '',
            'join' => '',
            'where' => '',
            'etc1' => ''
        ];

        # 통계 분류

        # 날짜 체크

        # 조건 확인

        # 산출 결과 확인

        # 통계 산출 쿼리 생성

        # 로그 작성

//        echo $_SERVER['HTTP_REFERER'];
        return view('admin.StatsReg', [ 'title' => '통계 산출']);


    }

//    # 통계 요청 ( 통계 생성 프로세스 ) 통계 산출 x
    public function setStats(Request $request){

        $CStatsDbConnection = DB::connection("StatsDbConnection");
        // DB 체크
        if (empty($CStatsDbConnection)) {
            throw new Exception('Err');
        }

        $bCheckJoin = 'n';


        $rgParams = collect($request->all());

        try {
            $strWhereTable = '';

            if(empty($rgParams['result_target'])){
                $strResultTarget = '*';
            }else {
                $strResult = implode(',',$rgParams['result_target']);
                $strResultTarget =  $strResult;
            }


//            $strQuery = "SELECT " . $strResultTarget . " FROM ELK_LOG.web_log";

//         조건 테이블 생성
            $strWhereJoin = '';
            $strWhereQuery = '';

            if($rgParams['join_target'] !== 'none') {
                $bCheckJoin = 'y';
                $strWhereTable = $rgParams['its_key'] ."_" .$rgParams['join_target'];
                $qryCreateTable = "CREATE table IF NOT EXISTS " . $strWhereTable;
                $qryCreateTable .= " (
                      `key_name` varchar(30) NOT NULL COMMENT ' " . $rgParams['join_target'] . "',
                      PRIMARY KEY (`key_name`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
                    ";

                $arrTargetJoin = array();
                $arrTargetJoin = explode(',', $rgParams['join_target_v']) ;
                $strTargetJoin = implode(',', $arrTargetJoin);
                foreach( $arrTargetJoin as $keyname){
                    $strKey = "('" . $keyname . "')";
                    $qryInsertData = "INSERT INTO $strWhereTable VALUES " . $strKey ;
                    echo $qryInsertData . "\n";
                }

                $strWhereQuery = " WHERE ";
//                조건 존재
                $strWhereJoin = $rgParams['join_target'];



            }

            $rgStatsInfo = [
                'its_key'           => $rgParams['its_key'],
                'user_id'           => $request->session()->get('admin_no'),
                'subject'           => $rgParams['subject'],
                'start_date'        => $rgParams['start_date'] . " 00:00:00",
                'end_date'          => $rgParams['start_date'] . " 23:59:59",
                'stats_project'     => '000000001',
                'project_name'      => '웹 > 로그',
                'target_field'      => $strWhereJoin,
                'join_table'        => 'n',
                'group_field'       => '',
                'reg_date'          => date('Y-m-d'),
                'reg_time'          => date('H:i:s')
            ];

            $nResult = $CStatsDbConnection
                ->table('user_stats_list')
                ->insertGetId($rgStatsInfo );

            $strInsertOutPutNames = "";
            $strInsertOutPutFields = "";
            $rgOutPutNames = [
                'its_key'                => $rgParams['its_key'],
                'user_stats_list_seq'    => $nResult,
                'output_name'            => $rgParams['subject']
            ];
            $rgOutPutFields = [
                'its_key'                       => $rgParams['its_key'],
                'user_stats_list_seq'           => $request->session()->get('admin_no'),
                'output_filed'                  => $rgParams['subject'],
            ];

            $nResult = $CStatsDbConnection
                ->table('output_names')
                ->insertGetId($rgOutPutNames);

            $nResult = $CStatsDbConnection
                ->table('output_fields')
                ->insertGetId($rgOutPutFields);


        }catch(Exception $e){

        }
    }

# 통계 실행
    public function first_page()
    {


    }


    public function checkExistsTable($strDbName, $strTableName){
        $qrySelect = "SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME LIKE '{seq}_{its_key}%' ";
        
        
        
        
    }






}



/*
 * sql
 *  SELECT TABLE_SCHEMA, TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME LIKE '{seq}_{its_key}%' ;
 *
 *
 *
 * */