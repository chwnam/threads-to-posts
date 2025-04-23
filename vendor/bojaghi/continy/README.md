# Continy

아주 작고 간단한 워드프레스 플러그인과 테마 개발을 위한 컨테이너. 나름 의존성 주입을 지원한답니다!

## 목차

* [요구사항](#요구사항)
* [설치](#설치)
* [시작하기](#시작하기)
* [설정과 예시](#설정과-예시)

## 요구사항

* PHP 8.0 이상

## 설치

워드프레스 플러그인에서 composer 패키지 매니저를 사용하여 설치합니다.

```bash
composer require bojaghi/continy
```

## 시작하기

autoload.php 파일은 정확하게 덤프되었나요?

```shell
composer dump-autoload
composer dump-autoload -a # 최적화 버전
```

그다음 아래의 예시처럼 `myPlugin()` 래퍼 함수를 각자의 플러그인이나 테마에서 구현합니다.
이렇게 Continy 객체를 생성하면, 처음 호출 때 필요한 과정이 실행됩니다.

```php
/**
 * Plugin Name: My plugin
 * Description: ...
 * ... 
 */

require_once __DIR__ . '/vendor/autoload.php';

// 플러그인에서 도움이 되는 래퍼 함수 생성
if ( !function_exists( 'myPlugin' ) ) {
    /**
     * Wrapper function
     * 
     * @return \Bojaghi\Continy\Continy
     * @throws \Bojaghi\Continy\ContinyException
     * @throws \Bojaghi\Continy\ContinyNotFoundException
     */
    function myPlugin(): Bojaghi\Continy\Continy {
        static $continy = null;
        
        if (is_null($continy)) {
            $continy = Bojaghi\Continy\ContinyFactory::create(__DIR__ . '/conf/setup.php');
        }
        
        return $continy;
    }
}

if (!function_exists('myPluginGet')) {
    /**
     * @template T
     * @param class-string<T> $id
     *
     * @return T|object|null
     */
    function myPluginGet(string $id)
    {
        try {
            $instance = myPlugin()->get($id);
        } catch (\Bojaghi\Continy\ContinyException|\Bojaghi\Continy\ContinyNotFoundException $e) {
            $instance = null;
        }

        return $instance;
    }
}

// 플러그인 시동하기
myPlugin();
```

## 설정과 예시

워드프레스의 플러그인과 테마 개발의 핵심은 적절한 액션, 혹은 필터를 추가하는 것입니다.
액션과 필터를 추가할 때는 반드시 콜백 함수를 명시하게 되어 있는데, 이 콜백 함수에서 우리가 원하는 동작을 구현합니다.

워드프레스 코어에 우리가 원하는 기능을 구현하기 위해, 그리고 보다 쉽게 해당 기능을 관리하기 위해 '모듈'이라는 콤포넌트를 사용합니다.
적절한 기능들을 의미적으로 묶어 하나의 독립적인 모듈로 표현하는 것입니다.

Continy는 이러한 설정을 하나의 PHP 배열로 관리합니다.
직접 설정을 배열로 입력하든지, 아니면 설정을 리턴하는 파일을 지정합니다.
아래는 그 예입니다.

```php
// create() 부분만 예시로 듭니다.
// 옵션 #1: 설정 파일이 있는 경로 지정하기
$continy = Bojaghi\Continy\ContinyFactory::create(__DIR__ . '/conf/setup.php');

// 옵션 #2: 직접 설정을 배열로 넣기
$continy = Bojaghi\Continy\ContinyFactory::create(
    [
        'main_file' => __FILE__,
        'version'   => '1.0.0',
        // ...
    ],
);
```

두 옵션은 결과적으로 같은 동작을 합니다.
그리고 아래는 설정의 예시입니다.

```php
/**
 * 설정 파일의 예시
 */
if (!defined('ABSPATH')) {
    exit;
}

return [
    'main_file' => dirname(__DIR__) . '/index.php', // 플러그인 메인 파일
    'version'   => '1.0.0',                         // 플러그인의 버전
    
    /**
     * 훅 선언
     * 
     * 키: 훅 이름
     * 값: 콜백 함수에서 허용하는 인자 수, 0 이상의 정수 
     */
    'hooks' => [
        'admin_init' => 0,
        'init'       => 0,
    ],
    
    /**
     * 바인딩 선언
     *
     * 키: 별명 (alias)
     * 값: 실제 클래스 (FQCN)
     */
    'bindings' => [
        'myModule'  => MyModule::class,
        'foo'       => Foo::class,
        IBar::class => BarImpl::class, 
    ],
      
    /**
      * 클래스 의존성 주입 선언
      *
      * 키: 별명, 또는 FQCN
      * 값: 배열, 또는 함수 - 함수는 배열을 리턴해야 함 
      */
    'arguments' => [
        'myModule' => ['p1' => 'X', 'p2' => 'Y'],
        'foo'      => function (Continy $continy) { return ['p1' => 'X', 'p2' => 'Y']; },
    ],  
      
    /**
     * 모듈 선언
     */  
    'modules' => [
        // 1.0.2 부터 지원하는 언더스코어 모듈: Continy 가 인스턴스화 될 때 바로 실행되는 모듈.
        // 우선순위 키는 사용하지 않습니다.
        '_' => [
            IBar::class,
        ],
        // 훅 이름
        'init' => [
            // 모듈 우선순위 
            Continy::PR_DEFAULT => [
                // 모듈 목록
                'myModule',
                'foo',
            ],
        ],
        // 혹 이름, 모듈 우선순위, 모듈의 목록 ...  
    ],
];
```

### 의존성 주입 예시

Continy 객체를 얻기 우해서는 `get()` 메소드를 사용합니다.

메소드 또는 함수 호출에 의존성 주입을 위해 `call()` 메소드를 사용합니다.
