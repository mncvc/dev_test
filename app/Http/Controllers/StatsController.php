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

//    # 통계 요청
    public function setStats(Request $request){
        $rgParams = collect($request->all());
        try {
            $strWhereTable = '';

//         통계 조건 테이블 존재 확인
            $qryCreateTable = "CREATE table IF NOT EXISTS " . $strWhereTable;
            $qryCreateTable .= " (
                      `key_name` varchar(30) NOT NULL COMMENT '회원아이디',
                      PRIMARY KEY (`key_name`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;
                    ";
            var_dump($rgParams);
//            echo $qryCreateTable;
            $strResultTarget = '';
            if(empty($rgParams['result_target'])){
                $strResultTarget = '*';
            }else {
                $strResult = implode(',',$rgParams['result_target']);
                $strResultTarget =  $strResult;
            }


            $strQuery = "SELECT " . $strResultTarget . " FROM ELK_LOG.web_log";

//         조건 테이블 생성
            $strWhereJoin = '';
            $strWhereQuery = '';

            if($rgParams['join_target'] !== 'none') {
                $strWhereQuery = " WHERE ";
//                조건 존재
                $strWhereJoin = $rgParams['join_target'];

                $arrTargetJoin = array();
                $arrTargetJoin = explode(',', $rgParams['join_target_v']) ;

                $strTargetJoin = implode("','", $arrTargetJoin);

                $strWhereQuery .= $strWhereJoin . " IN ('" . $strTargetJoin ."');";

            }
            $strQuery .= $strWhereQuery;

            echo $strQuery;




//

//

//


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