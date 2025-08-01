# 스레드 포스트 구조

플러그인은 단일 포스트 호출 시, 가능한 모든 필드를 가져오도록 합니다.
그러나 게시물의 형태에 따라 필드의 구조는 조금씩 다를 수 있습니다.

본 문서는 여러 형태의 스레드 포스트 응답 예시를 기록합니다.

## TEXT_POST 타입

가장 일반적인 텍스트만 가진 포스트입니다. 

```json
{
  "id": "1234567",
  "media_product_type": "THREADS",
  "media_type": "TEXT_POST",
  "permalink": "https://www.threads.net/@threadsapitestuser/post/abcdefg",
  "owner": {
    "id": "1234567"
  },
  "username": "meta",
  "text": "Today Is Monday",
  "topic_tag": "Mondays",
  "timestamp": "2023-10-09T23:18:27+0000",
  "shortcode": "abcdefg",
  "is_quote_post": false
}
```

## IMAGE 타입

이미지를 추가하여 작성하면 다음처럼 구성됩니다. 

```json
{
  "id": "18052189877390493",
  "media_product_type": "THREADS",
  "media_type": "IMAGE",
  "media_url": "https://scontent-ssn1-1.cdninstagram.com/v/t51.82787-15/524704307_17915832591117096_2223824483709801467_n.webp?stp=dst-jpg_e35_tt6&_nc_cat=105&ccb=1-7&_nc_sid=18de74&_nc_ohc=trEJA0r4FWAQ7kNvwGt378f&_nc_oc=Adm2I0EcK9v_4z4yyJxC-RyeDC7s_uyu32G14u8J4UYWd1DMCVvOwancvBWav4BYVvs&_nc_zt=23&_nc_ht=scontent-ssn1-1.cdninstagram.com&edm=ANQ71j8EAAAA&_nc_gid=y4_bTsaN76PyDD89WmdHtQ&oh=00_AfSpBVmmpxvKG_WATDAtIk8Cd3GR4Q3D0-ay8hohuGC--w&oe=6890E441",
  "permalink": "https://www.threads.com/@changwoo0215/post/DMm9sGfzdzu",
  "owner": {
    "id": "9491711744278740"
  },
  "username": "changwoo0215",
  "text": "임만 그래도 이렇게 답글달면 어쩌랍니까? 대관절 당신이 어떤 사람인데 초보니 고수니 초면에 급질하고 계십니까?",
  "timestamp": "2025-07-27T11:01:37+0000",
  "shortcode": "DMm9sGfzdzu",
  "is_quote_post": true,
  "quoted_post": {
    "id": "17846304630513618"
  }
}
```

## REPOST_FACADE 타입

사용자가 리포스트한 경우에 해당합니다. 'text' 필드가 없고 'permalink'를 따라가면
원래 사용자의 포스트로 리다이렉션됩니다. 공식 API로는 다른 사용자의 포스트 정보를 조회할 수 없습니다.

```json
{
    "id": "18045444965288307",
    "media_product_type": "THREADS",
    "media_type": "REPOST_FACADE",
    "permalink": "https://www.threads.com/@changwoo0215/post/DMlHsMVT8bt",
    "owner": {
        "id": "9491711744278740"
    },
    "username": "changwoo0215",
    "timestamp": "2025-07-26T17:50:31+0000",
    "shortcode": "DMlHsMVT8bt",
    "is_quote_post": false
}
```


## 인용된 포스트, 리포스트

다른 포스트를 인용할 경우 `is_quote_post`는 `true`가 됩니다.
이 때 자신의 포스트를 인용하는 경우 아래처럼 `quoted_post.id`를 확인할 수 있습니다.
반면 타인의 포스트를 인용하는 경우 인용된 원래 포스트의 정보를 볼 수 없습니다.
즉 타인의 포스트를 인용하는 경우 `is_quote_post` 필드만 확인 가능합니다.

```json
{
  "is_quote_post": true,
  "quoted_post": {
    "id": "17846304630513618"
  }
}
```

리포스트도 마찬가지입니다. `reposted_post` 필드는 자신의 포스트를 리포스트 하는 경우에만
값을 가질 수 있습니다. 타인의 포스트를 리포스트하는 경우 필드가 나오지 않습니다.

```json
{
  "reposted_post": {
    "id": "18051201287628875"
  }
}
```

## 링크 삽입

종종 포스트 하단에 외부 링크에 대한 정보가 적힌 카드가 보일 때가 있습니다.
이것은 `link_attachment_url` 필드에 있는 값입니다.

```json
{
    "link_attachment_url": "https://.....",
}
```
