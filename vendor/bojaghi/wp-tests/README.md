# WP Tests

WordPress 테스트 수트를 재활용하기 위한 패키지입니다.

## 사용법

`vendor/bin/wp-tests` 스크립트는 테스트를 위해 사용되는 스크립트입니다.
이 스크립트로 아래 와 같은 동작을 진행할 수 있습니다.

- 데이터베이스 사용자 생성하기
- 테스트 데이터베이스와 테이블 생성하기
- 유닛 테스트 설정 적용하기

### 설치하기

Development 패키지로 설치합니다.

```
composer require --dev bojaghi/wp-tests
```

아래 명령어를 이용해 테스트 사용자와, 해당 사용자에 테스트 DB의 권한을 지정합니다.

```
vendor/bin/wp-tests -c
```

### 섫정 복제하기

아래 명령어를 이용해 wp-tests 패키지로부터 테스트에 필요한 기초 설정을 가져옵니다.

```
vendor/bin/wp-tests -i
```

### 테스트 데이터베이스와 테스트 수트 설치

테스트에 필요한 설정들이 잘 심어졌다면, 다음 명령으로 테스트 데이터베이스를 생성합니다.
워드프레스 테스트 수트도 같이 설치됩니다. 이 때 subversion 이 필요합니다.

```
composer wp-tests:setup
```

### 테스트하기

아래 명령어를 사용해 PhpUnit을 실행할 수 있습니다.

```
composer wp-tests
```

정상 동작을 위해 `phpunit.xml` 파일을 열어

```xml

<exclude>./tests/TestSample.php</exclude>
```

부분을 주석 처리합니다.

```xml
<!-- <exclude>./tests/TestSample.php</exclude> -->
```

이렇게 처리하여 아주 간단한 `TestSample.php`에 대해 테스트를 진행할 수 있습니다.
