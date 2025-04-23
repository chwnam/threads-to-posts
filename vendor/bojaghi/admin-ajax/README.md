# Admin Ajax

WordPress wp-admin/admin-ajax.php, wp-admin/admin-post.php handler helper.

## 사용법

### 객체 생성하기

```php
$ajax = new \Bojaghi\AdminAjax\AdminAjax([ /* config */ ], $container);
// 또는
$ajax = new \Bojaghi\AdminAjax\AdminAjax('/path/to/config/file.php', $container);
```

위 예처럼 생성자의 인자로 첫번째에 배열, 또는 문자열을 입력할 수 있습니다. 이 값은 필수입니다.
두번째에는 `Psr/Container` 패키지 `Psr\Container\ContainerInterface` 인터페이스를 구현한 컨테이너 객체를 입력합니다.
컨테이너가 별명으로 인스턴스를 가져오게 하기 위해 필요합니다. 컨테이너를 사용하지 않는다면 입력하지 않아도 됩니다.

- `wp-admin/admin-ajax.php`를 위해서는 `Bojaghi\AdminAjax\AdminAjax`를 사용하세요.
- `wp-admin/admin-post.php`를 위해서는 `Bojaghi\AdminAjax\AdminPost`를 사용하세요.
- 두 클래스 모드 `Bojaghi\AdminAjax\SubmitBase`를 상속합니다.

### 설정 파일

설정 파일은 PHP 파일이며, 1개의 배열을 리턴해야 합니다.
아래 예를 참고하시기 바랍니다.

```php
if (!defined('ABSPATH')) {
    exit;
}

return [
    'checkContentType' => false, // 기본값: true
    // 여기부터 요청하는 액션 설정
    [
        // 가장 간단한 형태는 문자열 - 이 경우 콜백 함수 'my_action_1'이 있어야 제대로 동작.
        'my_action_1',
        
        // 콜백을 명시.
        ['my_action_2', 'myClass@callback2'],
        
        // 로그인하지 않은 사람도 호출할 수 있게 허용.
        ['my_action_3', 'myClass@callback3', SubmitBase::ALL_GRANTED],
        
        // 로그인하지 않은 사람만 호출 가능.`
        // 자동 NONCE 체크를 진행. 
        // 요청에 '_my_nonce'를 이름으로 한 NONCE 값을 전달한다.
        // wp_create_nonce() 의 입력으로 액션인 'my_action_4'를 입력한다.
        ['my_action_4', 'myClass@callback4', SubmitBase::ONLY_NOPRIV, '_my_nonce'],
        
        // 로그인한 사용자만 호출 가능.
        // 자동 NONCE 체크를 하지 않는다.
        // add_action() 우선순위를 20으로 설정한다.
        ['my_action_5', 'myClass@callback5', SubmitBase::ONLY_PRIV, '', 20],
    ],
];
```

### 자동 NONCE 체크

위 예시의 `my_action_4`처럼 '_my_nonce' 이름의 NONCE 값을 폼에 삽입하려면 아래처럼 할 수 있습니다.

```html
<input type="hidden" name="_my_nonce" value="<?php echo wp_create_nonce('my_action_4'); ?>" />
```

또는 `wp_nonce_field`를 사용할 수도 있습니다.

```php
wp_nonce_field('my_action_4', '_my_nonce');
```

요청을 처리하면서 콜백 함수를 호출하기 전에 미리 NONCE 검증을 하여 보다 편리합니다.
액션의 이름이 고정되어 있다면 이 방법을 사용하는 것을 추천합니다.

### 콜백 지정 방법

설정 파일의 예에서 `myClass@callback3` 같은 문자열을 콜백으로 사용했습니다. 여기서는 콜백을 해석하는 방법은 설명합니다.

콜백의 입력으로 다음이 가능합니다.

- 함수
- 배열
- 문자열

직관적으로, 익명함수, 람다함수, 함수 이름의 문자열, 배열 형태의 메소드 같이 호출 가능하다면 그대로 콜백으로 사용됩니다.

만약 콜백으로 길이가 2인 배열이되 각 요소가 문자열인 배열이 입력된다면, 이 경우 정해진 방법대로 해석을 거칩니다.

1. 0번 요소가 클래스 이름 그대로라면 해당 클래스를 인스턴스화 시킵니다.
2. 0번 요소가 컨테이너에서 객체의 별명으로 사용된다면 해당 인스턴스를 컨테이너로부터 가져옵니다.
3. `[인스턴스, 메소드]`가 호출 가능하면 해당 배열을, 아니면 `null`을 리턴합니다.

만약 콜백이 함수 이름이 아닌 문자열이라면, 다음 과정을 거칩니다.

1. '@' 표시를 기준으로 문자열을 최대 길이 2짜리 배열로 변환합니다,
2. 길이 1인 배열이 된 경우,
    1. 0번 요소는 클래스 이름이거나 컨테이너에서 사용되는 별명으로 간주합니다.
       그리하여 0번 요소로부터 인스턴스를 추론할 수 있어야 합니다.
    2. 인스턴스화를 마치는 것으로 과정이 끝나고, `null`을 리턴합니다.
       인스턴스를 생성하면서 필요한 과정이 모두 수행된 것으로 생각하기 때문입니다.
3. 길이 2인 배열이 된 경우,
    1. 0번 요소는 클래스 이름이거나 컨터이너에서 사용되는 별명으로 간주합니다.
       그리하여 0번 요소로부터 인스턴스를 추론할 수 있어야 합니다.
    2. 1번 요소는 인스턴스의 메소드 이름이어야 합니다.
    3. `[인스턴스, 메소드]`가 호출 가능하면 해당 배열을, 아니면 `null`을 리턴합니다.
