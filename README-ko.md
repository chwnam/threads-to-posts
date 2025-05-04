# 스레드 투 포스트

[스레드](https://threads.net)에 포스팅된 내 글을 내 워드프레스의 포스트로 가져오는 워드프레스 플러그인입니다.

## 설치 안내

**일러두기**: 이 플러그인은 정식으로 \[wordpress.org\]에 등록된 플러그인이 아닙니다.

- PHP 8.0 이상
- 필수 확장: `ext-pcntl`, `ext-zlib`

플러그인을 git clone 으로 복제합니다.

```bash
git clone https://github.com/chwnam/threads-to-posts.git
```

플러그인을 디렉토리째 워드프레스의 `wp-content/plugins/` 아래로 옮긴 후 플러그인 루트로 이동합니다.
워드프레스에 로그인 한 후, 관리자 페이지로 가서 플러그인을 활성화합니다.
그러면 관리자 > 도구 메뉴에서 'Threads to Posts'를 발견할 수 있습니다.

## 설정 안내

스레드 API를 정상적으로 사용하려면,

1. [메타 개발자](https://developers.facebook.com/) 사이트에서 앱을 등록해야 합니다.
2. 앱에서 생성된 App ID, App Secret을 플러그인 설정에 등록해야 합니다.
3. 플러그인에서 액세스 토큰을 발급받아야 합니다.

이와 관련된 설정은 [설정 문서](./docs/ko/how-to-setup.md)를 참고하십시오.

## 글 수집하기

글 수집하기 관련 사항은 [해당 문서](./docs/ko/scrap.md)를 참고하삽시오.

## 커맨드라인

이 플러그인은 일부 상세 명령을 WP-CLI를 통해 지원합니다.
이와 관련된 사항은 [커맨드 라인 문서](./docs/ko/cli-manual.md)를 참고하십시오.

## 버전 변경 기록

[CHANGELOG.md](./CHANGELOG.md)
