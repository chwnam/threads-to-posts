# Threads To Post 플러그인

스레드에 포스팅된 내 글을 워드프레스로 가져옵니다.

## 설치 안내

**일러두기**: 이 플러그인은 정식으로 wordpress.org 에 등록된 플러그인이 아닙니다. PHP를 커맨드라인으로 실행할 수 있고,
composer PHP 패키지 관리자가 설치되어 있어야 합니다.

**PHP 8.0 이상에서 동작합니다.**

플러그인을 git clone 으로 복제합니다.

```bash
git clone https://github.com/chwnam/threads-to-posts.git
```

플러그인을 디렉토리째 워드프레스의 `wp-content/plugins/` 아래로 옮긴 후 플러그인 루트로 이동합니다.
필요한 의존성 패키지를 설치합니다.

```bash
cd /path/to/wordpress/wp-content/plugins/threads-to-posts
composer install
composer dump-autoload -a
```

워드프레스에 로그인 한 후, 관리자 페이지로 가서 플러그인을 활성화합니다.
그러면 관리자 메뉴에서 'TTP'를 발견할 수 있을 것입니다.

## 설정 안내

스레드 API를 정상적으로 사용하려면 메타 개발자 사이트에서 앱을 등록해야 합니다.
이와 관련된 설정은 [설정 문서](./doc/how-to-setup.md)를 참고하기 바랍니다.

## 버전 변경 기록

### 0.3.0

**알파 버전**

- 테스터 페이지의 간단 구현
- 로그 파일의 웹에서 접근하지 못하게 URL 보호 처리 (아파치 전용)
- 기타 설정 추가

### 0.2.0

**알파 버전**

2025-04-14

- 도구(tools.php) 아래로 페이지를 이동
- 설정 탭에서 인증, 토큰, 크론 설정 생성
- 테스터 페이지 생성

### 0.1.0

**알파 버전**

2025-04-13

- 스레드 인증 및 크론 테스트 버전
