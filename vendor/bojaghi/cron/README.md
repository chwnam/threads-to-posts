# Bojaghi Cron

WP Cron 설정을 도와주는 모듈입니다.

## 사용법

### Cron

주기적으로 특정 동작을 실행하기 위한 WP-CRON 설정을 위한 모듈입니다.
생성자에 설정의 배열을 그대로 넣거나, 설정 배열을 리턴하는 파일의 경로를 문자열로 전달합니다.

```php
// 직접 입력 방식
new Cron([ /* ... */]);

// 경로 지정 방식
new Cron('/path/to/config.php');
```

설정 배열 예입니다 아래는 경로 지정을 한 경우의 예제입니다.
```php
/* /path/to/config.php */

if (!defined('ABSPATH')) exit;

// configuration array
return [
    'is_theme'  => false, // 테마에서 사용할 경우 'true' 이고 'main_file'은 무시합니다.
    'main_file' => '...', // 플러그인의 메인 파일입니다.
    'items'     => [
        [
            'timestamp'       => 0,      // 정확한 실행 시간을 넣거나, 0이면 활성화 시간으로 적당히 채워집니다.
            'schedule'        => '',     // 'daily', 'hourly' 같은 코어에 등록된 스케쥴 식별자.
            'hook'            => '',     // 실행할 훅.
            'args'            => [],     // Optional: 훅 실행시 전달될 인자.
            'wp_error'        => false,  // Optional: 에러 발생시 WP_Error 를 일으킬지 선택.
            'is_single_event' => false,  // Optional: 1회성 이벤트인지, 반복되는 이벤트인지 결정.
        ],
    ],
];
```

### CronSchedule

크론 설정 주기를 확장하기 위한 모듈입니다.
생성자에 설정의 배열을 그대로 넣거나, 설정 배열을 리턴하는 파일의 경로를 문자열로 전달합니다.

```php
// 직접 입력 방식
new CronSchedule([ /* ... */]);

// 경로 지정 방식
new CronSchedule('/path/to/config.php');
```

설정 배열 예입니다 아래는 경로 지정을 한 경우의 예제입니다.
```php
/* /path/to/config.php */

if (!defined('ABSPATH')) exit;

// configuration array
return [
    'items'     => [
        [
            'display'  => '', // 관리 화면 같은 프론트에 출력될 경우에 보여질 문자열입니다. 
            'interval' => 0,  // 반복 주기를 0 보다 큰 정수로 입력합니다.
            'schedule' => '', // 코어에 등록될 스케쥴 문자열입니다 영소문자, 언더바, 숫자만 쓰는 것을 추천합니다.``
        ],
    ],
];
```
