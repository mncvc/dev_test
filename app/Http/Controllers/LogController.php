<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
class LogController extends Controller
{
    //
    function index(Request $request){
//        input info
        $rgInfo = array(
            'search' => 0,
            'page' => 0,
            'total' => 0,
            'posts' => array()
        );

        $rgInfo['search'] = !empty($request->get('search')) ? $request->get('search') :0;
        $rgInfo['page'] = !empty($request->get('page')) ? ($rgInfo['page'] + $request->get('page')) * 10 : 0;

        $params = [
            'index' => 'my_index',
            'body'  => [
                'sort' => [['reg_date' => 'desc']],
                'query' => [
                    'match_all' => new \stdClass() // 모든 문서를 조회
                ],
                'from' => $rgInfo['page'],
                'size' => 10 // 조회할 문서 수 (기본값: 10, 필요하면 설정)
            ],
        ];

        if($rgInfo['search'] != 0) {
            $params = [
                'index' => 'my_index',
                'body' => [
                    'sort' => [['reg_date' => 'desc']],
                    'query' => [
                        'bool' => [
                            'must' => [
                                ['match' => ['user_no' => $rgInfo['search']]]
                            ]
                        ],
                    ],
                    'from' => $rgInfo['page'],
                    'size' => 10
                ],
            ];
        }

        $client = App::make('Elasticsearch');
        $response = $client->search($params);


        $rgInfo['total'] = $response['hits']['total']['value'];

        $data = array_map(function ($hit) {
            return $hit['_source'];
        }, $response['hits']['hits']);

        $rgInfo['posts'] = $data;


        return view('log.index', ['rgInfo' => $rgInfo, 'title' => '로그인 로그']);

    }

}
