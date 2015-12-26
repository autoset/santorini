# 산토리니 프레임워크

## 개요

- Santorini Framework(산토리니 프레임워크)는 PHP Framework로 JAVA의 Spring, MyBatis, Tiles 등으로부터 사상을 차용하여 개발된
프레임워크입니다.
- JAVA개발자와 PHP 개발자 간의 언어 장벽을 낮추고 언어간의 포팅이 용이하도록 설계되어 보다 생산적으로 개발하고 다양한 환경으로 쉽게 포팅할 수
있도록 설계되어 있습니다.

* [[산토리니 프레임워크 개발 배경|About ! Santorini-Framework]]

## 주요 특징

- 빠른 개발을 위한 SDK(Software Development Kit) 제공
- CUBRID, MySQL, SQLite, MS-SQL 등 다양한 DBMS 드라이브 지원
- 다양한 템플릿 엔진 결합 가능(Tiles 2 for Santorini 기본 지원)
- 프록시 기반의 AOP(Aspect Oriented programming) 지원(DB 트랜잭션 관리 등)
- RESTFul API 지원, AJAX Handler 제공
- 기본 사용자 액세스 컨트롤 지원
- 어노테이션(Annotation)을 통한 설정 및 로직 구현의 간결화 가능
- JAVA 및 Spring Interface 명 동일하게 제공(JAVA 포팅 용이)
- JAVA 개발 환경 차용을 통한 익숙한 디렉토리 구조(for JAVA개발자)
- 캐싱 기능을 통한 빠른 로직 수행 처리 지원

## 설치 방법

1. ddl_example.sql 파일을 MySQL/MariaDB에 실행해, 세션 관리용 테이블과 사용자 관리용 테이블을 생성한다.
2. servlet.php가 있는 폴더를 `DOCUMENT_ROOT`로 하여, 웹 서버를 구동한다.
