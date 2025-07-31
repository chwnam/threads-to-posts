# 변경 기록

## Beta Phase

### 1.0.0.beta-4

2024-08-01

- Support for more detailed fields:
  - is_quote_post
  - media_type
  - media_url
  - reposted_post.id
  - quited_post.id
- Support simple text fetching from other users' posts when you repost it.
- Add crawl test in the admin tester page.
- Fix wrong redirection after authorization. 

### 1.0.0.beta-3

2024-05-04

- Add log when API server returns a string type status code
- Add addtional exception code when API server returns a string-typed status code
- Change Threads domain from `www.threads.net` to `www.threads.com`
- Fix wrong total number display when used with `--forever` switch
- Update logger format, now it can display context data, and extra data
- Update task queue, now it can compressto task queue
- Update task queue run behavior, now it saves while running, after completing every 50 tasks 
- Remove version from composer.json
- Update docs

### 1.0.0.beta-2

2024-05-04

- Fix CSS loading error
- Add feature: fetching threads single post and display the raw API result in the admin single page.
- Improve scrap process

### 1.0.0.beta-1

2024-04-28

- Add English version documentation
- Namespce prefixing

## Alpha Phase

### 0.12

2024-04-24

- API 덤프 기능 삭제
- 가벼운 모드 스크랩일 때 수집된 글의 모든 댓글을 수집하게 수정
- 관리자 페이지에서 댓글 목록을 숨긴 것을 다시 노출시키도록 수정
- 로그 파일을 매일 분할하여 최대 7개까지 보관하게 수정
- 문서 디렉토리 구조 수정
- 문서 디렉토리 이름을 docs로 변경.
- 설정 Cron status 에 스크랩 스케쥴을 추가
- 커맨드라인 명령어 매뉴얼 추가
- 커맨드라인 명령어 일부 누락된 사항을 구현
- 크론 이벤트가 제대로 비활성화되지 않는 문제 수정

### 0.11

2024-04-22

- 큐의 태스크 구분자를 콤마에서 리턴으로 변경
- README.md 에 설정법과 글 수집법 추가
- 옵션 저장시 토큰 정보가 삭제되는 문제 수정
- 프론트엔드 페이지 개선

### 0.10

2024-04-21

- 태스크 러너 기능 개선
- CLI 문서 추가

### 0.9

2024-04-19

- 큐 러너 안정화
- 변경 기록에 표기된 잘못된 연도 수정
- 스레드 수집 시점의 타임스탬프 데이터 추가
- 커스텀 포스트에 대한 역할, 권한 관련 수정
- 스레드 커스텀 포스트 싱글 페이지 구현
- 태스크 매니저 탭 추가
- 기타 자잘한 기능 수정

### 0.8

2025-04-19

- 스레드 커스텀 포스트에 대한 권한 추가
- 스레드 커스텀 포스트 리스트 테이블에 대한 조정 추가
- 큐를 transient 기반 구현으로 수정
- 태스크 러너가 동작할 때, 표준 출력으로도 로그가 나오도록 추가
- 포스트 내보내기 기능 추가
- 커맨드라인 기능 추가

### 0.7

2025-04-17

- wp ttp remove 동작 수정
- 스레드 SVG 아이콘 추가
- 스크랩 코드 안정화

### 0.6

2025-04-17

- 스케쥴러와 작업 큐 구현
- 커맨드라인 지원 추가
- 코드스타일 수정

### 0.5

2025-04-15

- CHANGELOG.md 문서 생성
- API 호출 코드 수정
- 소프트, 하드 업데이트의 개념 삭제
- 커스텀 포스트 레이블 수정

### 0.4

2025-04-15

- 커스텀 포스트 추가
- 내 게시물들을 커스텀 포스트 형태로 저장하는 프로세스 구현

### 0.3

2025-04-14

- 테스터 페이지의 간단 구현
- 로그 파일의 웹에서 접근하지 못하게 URL 보호 처리 (아파치 전용)
- 기타 설정 추가

### 0.2

2025-04-14

- 도구(tools.php) 아래로 페이지를 이동
- 설정 탭에서 인증, 토큰, 크론 설정 생성
- 테스터 페이지 생성

### 0.1

2025-04-13

- 스레드 인증 및 크론 테스트 버전
