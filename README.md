
# About us
## 개발 연구 테스트 사이트
1. KMS 관리자 개발 연구 - DashBoard UI                       (2024-06-10~2024-06-30)
2. KMS 관리자 개발 연구 - ELK 추가 등록                        (2024-09-02~2024-09-20)
3. KMS 관리자 개발 연구 - googleauth 추가 otp추가              (2024-10-01~2024-10-04)
4. 통계 웹 기능 추가 연구 - test 통계 프로세스 및 로그 분석 시스템 (2025-03-28~) <br>
   4-1) dev_test git 연동 <br>
   4-2) 테스트 통계 프로세스 개발 (테스트 프로세스 2025-04-07 개발완료 )<br> 
   4-3) 통계 로그 분석 시스템 개발 (2025-04-07 ~)

<details>
   <summary> Initial settings(DB) </summary>
   <div markdown="1">

```sql
# (필수)DB 생성

sql> CREATE DATABASE IF NOT EXISTS `stats`;
sql> CREATE DATABASE IF NOT EXISTS `ELK_LOG`;
sql> CREATE DATABASE IF NOT EXISTS `admininfodb`;

# (필수)TABLE 생성
sql> use stats;
sql> CREATE TABLE `output_fields` (
`its_key` varchar(10) NOT NULL COMMENT 'JIRA ITS KEY',
`user_stats_list_seq` int NOT NULL COMMENT 'user_stats_list SEQ',
`output_field` text NOT NULL COMMENT '요청자 통계 기준값',
PRIMARY KEY (`its_key`,`user_stats_list_seq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='산출해야할 필드';

sql> CREATE TABLE `output_names` (
`its_key` varchar(10) NOT NULL COMMENT 'JIRA ITS KEY',
`user_stats_list_seq` int NOT NULL COMMENT 'user_stats_list SEQ',
`output_name` text NOT NULL COMMENT '요청자 통계 기준값',
PRIMARY KEY (`its_key`,`user_stats_list_seq`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='산출해야할 필드 명' ;

sql> CREATE TABLE `user_stats_list` (
`seq` int NOT NULL AUTO_INCREMENT COMMENT '고유값',
`its_key` varchar(10) NOT NULL DEFAULT '' COMMENT 'JIRA ITS KEY',
`user_id` varchar(30) NOT NULL DEFAULT '' COMMENT '담당자',
`subject` varchar(60) NOT NULL DEFAULT '' COMMENT '통계 제목',
`start_date` date DEFAULT NULL COMMENT '대상 시작일',
`end_date` date NOT NULL COMMENT '대상 종료일',
`stats_project` char(9) NOT NULL DEFAULT '' COMMENT '분석된 통계 항목',
`project_name` varchar(100) NOT NULL DEFAULT '',
`target_field` varchar(100) NOT NULL DEFAULT '',
`join_table` char(1) NOT NULL DEFAULT 'n' COMMENT '임시 테이블의 값 기준으로 데이터 산출 여부 <y/n>',
`group_field` varchar(100) NOT NULL DEFAULT '' COMMENT '그룹핑 필드',
`reg_date` date DEFAULT NULL COMMENT '통계 요청일',
`reg_time` time DEFAULT NULL COMMENT '통계 요청일',
`gubun` char(1) DEFAULT 'B' COMMENT '신/구버전비교 <B/A>',
`cron_flag` char(1) NOT NULL DEFAULT 'n' COMMENT '크론 유무 <y/n>',
`cron_date` datetime DEFAULT NULL COMMENT '크론 시작 시간',
`result_flag` char(1) NOT NULL DEFAULT 'n' COMMENT '결과 유무 <y/n>',
`result_date` datetime DEFAULT NULL COMMENT '추출 완료 시간',
`down_flag` char(1) NOT NULL DEFAULT 'n' COMMENT '내려받기 유무 <y/n>',
`down_date` datetime DEFAULT NULL COMMENT '다운로드 완료 시간',
`view` char(1) NOT NULL DEFAULT 'y' COMMENT '보기 여부(y/n)',
`view_date` datetime DEFAULT NULL COMMENT '삭제시간',
PRIMARY KEY (`seq`),
KEY `its_key` (`its_key`),
KEY `user_id` (`user_id`),
KEY `stats_state` (`stats_project`,`join_table`),
KEY `reg_date` (`reg_date`,`reg_time`),
KEY `search_flag` (`cron_flag`,`result_flag`,`down_flag`),
KEY `view` (`view`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='사용자 자료 요청 리스트';

sql> use admininfodb;

sql> CREATE TABLE `admin_info_new` (
`admin_no` int unsigned NOT NULL AUTO_INCREMENT COMMENT '관리자 번호',
`admin_id` varchar(20) NOT NULL COMMENT '관리자 아이디',
`admin_level` char(1) NOT NULL DEFAULT '1' COMMENT '관리자 레벨 (1: 전체 허용 , 2: 조회만 가능, ..., 0: 접근 불가)',
`admin_state` char(1) NOT NULL DEFAULT '1' COMMENT '관리자 상태 (1: 일반, 2: 휴면, 3: 5회 오류, 4: 퇴사)',
`depart_code` varchar(2) NOT NULL DEFAULT '00' COMMENT '부서 팀 코드 (01: T/D 팀, 02: S/D팀 )',
`position_code` varchar(2) NOT NULL DEFAULT '00' COMMENT '직급 코드 (01: 연구원, 02: 선임 연구원, 03: 책임 연구원, 04: 팀장)',
`google_auth_flag` enum('Y','N') NOT NULL DEFAULT 'N' COMMENT 'OTP 인증 여부 (Y:인증, N:미인증)',
`reg_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록 일시',
`admin_leave_date` datetime DEFAULT NULL COMMENT '퇴사 일시',
PRIMARY KEY (`admin_no`),
UNIQUE KEY `admin_id_UNIQUE` (`admin_id`),
KEY `reg_date` (`reg_date`) /*!80000 INVISIBLE */,
KEY `admin_level` (`admin_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='관리자 일반 정보' ;

sql> CREATE TABLE `admin_google_auth_list` (
`seq` int unsigned NOT NULL AUTO_INCREMENT COMMENT '일련번호',
`admin_no` int unsigned NOT NULL COMMENT '관리자 번호',
`secret_key` varchar(64) NOT NULL COMMENT 'OTP KEY',
`expire_date` date NOT NULL DEFAULT '0000-00-00' COMMENT '사용 만료일 (0000-00-00 영구)',
`use_flag` enum('Y','N') NOT NULL DEFAULT 'Y' COMMENT '';

sql> use itemmania_key;
sql> CREATE TABLE `crypto_helper` (
`seq` int unsigned NOT NULL AUTO_INCREMENT COMMENT '일련번호',
`service_type` char(1) NOT NULL COMMENT '서비스분류',
`crypto_code` varchar(64) NOT NULL COMMENT '암호코드',
`crypto_version` tinyint unsigned NOT NULL COMMENT '암호버전',
`use_start_date` date NOT NULL COMMENT '유효기간 시작일',
`use_end_date` date NOT NULL COMMENT '유효기간 종료일',
`use_flag` enum('y','n') NOT NULL COMMENT '사용여부',
`reg_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '등록일시',
PRIMARY KEY (`seq`),
UNIQUE KEY `service_version` (`service_type`,`crypto_version`),
UNIQUE KEY `crypto_code` (`crypto_code`),
KEY `use_date` (`use_start_date`,`use_end_date`),
KEY `use_flag` (`use_flag`),
KEY `reg_date` (`reg_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='암호키 리스트'     


# (테스트)데이터 삽입.
#
```

   </div>
</details>



## sql


