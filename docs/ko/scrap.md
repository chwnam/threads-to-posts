# 스레드 글 스크랩하기

스크랩 하기 전 반드시 아래 사항을 확인하세요

- App ID, App Secret을 발급 받아서 플러그인 설정창에 입력하였는가?
- 플러그인은 액세스 토큰을 저장하고 있는가?

만약 위 사항이 충족되지 않았다면 올바르게 스레드의 글을 가져올 수 없습니다.

## 프론트엔드 이용하기

워드프레스의 `manage_options` 권한을 가진 계정이라면
`관리자 메뉴 > Threads > Settings`를 통해 설정 페이지에 접근 가능합니다.

여기서 'Scrap' 탭으로 이동합니다.

'Current Scrap Mode'에서 'Light mode'를 선택합니다. 저장 버튼을 누릅니다.
이렇게 하면 스크랩이 지속적으로 모니터링되며 스크랩이 진행됩니다.

## 커맨드라인 이용하기

WP-CLI를 이요앻 커맨드라인으로도 스크랩을 진행할 수 있습니다.
단, 프론트엔드와의 실행 충돌을 방지하기 위해 프론트엔드에서 'Current Scrap Mode'를
'disabled'로 먼저 지정해 두고 커맨드라인을 사용해 주십시오.

### 가벼운 스크랩

```shell
wp ttp scrap
wp ttp run
```

가벼운 스크랩은 아래처럼 동작합니다.

- 스레드 내 글 목록 처음 25개만을 읽어들입니다.
- 이 25개 글 중 15분이 지나지 않은 글은 제외합니다. 또한 이미 저장된 글도 제외합니다.
- 25개의 글에 대해 각각 작성된 댓글을 읽습니다.
    - 댓글 목록은 가장 최신순으로 25개만을 읽습니다.
    - 내 원래 글에 달린 댓글, 내 댓글 아래 작성된 내 댓글만 수집합니다.

가벼운 스크랩은 매 10분마다 실행횝니다. 글을 지속적으로 모니터링하여 최신 글만을 스크랩하기에 알맞은 모드입니다

### 무거운 스크랩

```shell
wp ttp scrap --heavy
wp ttp run
```

무거운 스크랩은 아래처럼 동작합니다.

- 스레드 글을 처음부터 끝까지 모두 검사합니다.
- 모든 글을 읽어 스크랩합니다.
- 각 글에 달린 모든 댓글 목록을 읽어들입니다.
    - 내 원래 글에 달린 댓글, 내 댓글 아래 작성된 내 댓글만 수집합니다.

무거운 스크랩은 커맨드라인으로만 지원합니다. 모든 글을 스크랩하기에 알맞습니다.
`wp ttp run` 명령은 최대 25개까지 실행하므로, 만약 가져와야 할 글의 양이 많다면 명령을 많이 반복해야만 합니다.
이럴 때는 `--max_task`옵션을 사용해 한번의 실행해 처리할 수 있는 태스크의 최대 갯수를 조절하세요.
단, `--max_task` 수를 너무 크게 잡지 마세요. 적정한 수는 100~300 정도입니다.
그리고 각 API 호출 사이 휴지를 2초 이상 유지하세요.
많은 태스크를 한번에 처리하려면 쉘의 반복을 사용하세요. 아래는 예시입니다.

```shell
for i in $(seq 1 10):
do
  wp ttp run --max_task=100 --sleep=3
done
```
