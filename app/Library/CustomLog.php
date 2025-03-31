<?php
namespace App\Library;


use Illuminate\Support\Facades\Http;
class CustomLog
{
    protected $elasticsearch;

    public function __construct(){}

    public function fnSetLog($rstLogData)
    {
//        $dtNow = date('Y-m-d H:i:s');
//        //
//        $url = 'http://192.168.56.108:9200/my_index/_doc';
//
//        // 전송 데이터
//        $rstData=[
//          'user_no' => $rstLogData['user_no'],
//          'client_ip' => $rstLogData['client_ip'],
//          'type' => $rstLogData['type'],
//          'status' => $rstLogData['status'],
//          'title' => $rstLogData['title'],
//          'content' => $rstLogData['content'],
//          'reg_date' => Date($dtNow)
//        ];
//
//        try{
//            // cURL 요청 (POST)
//            $response = Http::withHeaders([
//                'Content-Type' => 'application/json',
//            ])->post($url, $rstData);
//
//            // 결과 확인
//            if ($response->successful()) {
//                return response()->json([
//                    'message' => 'Document indexed successfully!',
//                    'response' => $response->json(),
//                ]);
//            }
//
//            return response()->json([
//                'message' => 'Failed to index document',
//                'response' => $response->json(),
//            ], $response->status());
//
//        }catch(Exception $e){
//            return 0;
//        }
return 0;

    }

    public function fnSetLogMessage($message, $type, $rgData){
        $strMessage = "$message \n";

        if($type == 'i'){
            $strMessage .= "[사용자 입력] \n";
        }else if($type == 'r'){
            $strMessage .= "[결과] \n";
        }
        $strMessage .= "{ \n";

        foreach($rgData as $key => $value){
            $strMessage .= "{$key} : {$value}, \n";
        }
        $strMessage .= "} \n";

        return $strMessage;
    }


}