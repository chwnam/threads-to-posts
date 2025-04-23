# Template

워드프레스 플러그인과 테마에서 사용할 수 있는 템플릿

## 사용법

`Bojaghi\Template\Template` 클래스를 객체화 합니다.

```php
use Bojaghi\Template\Template

$t = new Template(
    [
        'extensions' => ['php'],
        'infix'      => 'tmpl',
        'scopes'     => [
            plugin_dir_path(__FILE__) . '/templates',
        ],
    ],
);
```

입력되는 인자는 하나의 연관배열이며 다음 값을 갖습니다.

- extensions: 문자열 배열로 템플릿으로 사용할 파일의 확장자를 지정합니다.
  확장자 앞 점은 자동으로 입력되므로 생략해도 됩니다.
  기본값은 `['html', 'php']`입니다. 먼저 작성된 확장자가 우선합니다.
  즉, 'foo.html', 'foo.php'가 동시에 있을 때 `['html', 'php']`라면
  html 파일이 우선순위가 더 높습니다.
- infix: 파일 이름과 확장자 사이에 추가적인 문자열을 넣을 수 있습니다.
  기본값은 공백입니다.
  만약 infix 가 'tmpl'로 정해졌다면 템플릿 파일은 항상 `.tmpl.php` 처럼 끝나야 합니다.
- scopes: 문자열 배열로 필수입니다. 유효한 절대 경로를 입력해야 합니다.
  먼저 적힌 경로에서 발견된 템플릿 파일이 우선적으로 선택됩니다.

### 템플릿 불러오기

template() 메소드로 템플릿을 불러옵니다.

```php
$output = $t->template('my-template', ['foo' => 'bar']);
```

`my-template` 템플릿을 찾아 HTML 코드를 그려냅니다. 이 때 'foo': 'bar' 가 문맥(context)로 주어집니다.
`my-template`의 실제 파일은 scopes, extensions, infix를 고려한, 예를 들어, `{scope}/my-template{infix}{extension}`
처럼 경로를 추적하여 실제 파일이 있는지 검사합니다. 있으면 그 파일을 인클루드 합니다.
이 파일 안에 출력할 요소가 작성되어 있습니다.

`$this->get('...')` 메소드를 사용하여 문맥변수로 입력한 값을 활용할 수 있습니다.

```php
// my-template.php ?>
<div>Hello, <?php echo esc_html($this->get('foo')); ?>!</div>
<?php
// 결과: <div Hello, bar!</div>
```

### 탬플릿에서 요소 지정하기

start(), end() 메소드로 임의의 요소를 저장할 수 있습니다.
저장된 요소는 fetch() 메소드로 불러올 수 있습니다. 불러올 수 있는 횟수에는 제한이 없으며,
입력할 수 있는 값의 형태에도 제한이 없습니다.

```php
$this->start('title'); ?>
<span class="title">Mr.</span><?php
$this->end();
// ....
?>
<div><?php echo $this->fetch('title'); ?> Baker</div>
<div><?php echo $this->fetch('title'); ?> Thompson</div>
<div><?php echo $this->fetch('title'); ?> Ronald</div>
```

assign() 메소드로 한 줄에 걸쳐 지정할 수도 있습니다.

```php
$this->assign('title', '<span class="title">Ms.</span>');
```

### 템플릿 확장하기

한 템플릿이 유사한 구조로 여러번 반복하여 사용된다면, 일일이 템플릿을 만들어 반복하지 마세요.
기존이 되는 상위 템플릿을 생성한 후 하위 템플릿에서 적절히 확장하면 반복을 줄일 수 있습니다.

```php
// parent.php
?>
<div>Hello, I am <?php echo esc_html($this->fetch('whoami', 'parent')); ?> template!</div>
<!-- End of parent.php -->

<?php
// child-a.php
$this->extends('parent')->assign('whoami', 'child-a');
?>
<div>Nice to meet you!</div>
<!-- End of child-a.php -->

<?php
// child-b.php
$this->extends('parent')->assign('whoami', 'child-b');
?>
<div>It's a beautiful day!</div>
<!-- End of child-b.php -->
```

```html
<!-- parent.php -->
<div>Hello, I am parent template!</div>


<!-- child-a.php -->
<div>Hello, I am child-a template!</div>
<div>Nice to meet you!</div>


<!-- child-b.php -->
<div>Hello, I am child-a template!</div>
<div>It's a beautiful day!</div>
```

### 프래그먼트 불러오기

주로 고정된 코드 조각을 자주 반복할 셩우 fragment() 메소드를 사용해 반복을 줄입니다.

```html
<!-- frag-a.html -->
<p>This is a fragment.</p>
```

```php
echo $t->fragment('frag-a');
```
