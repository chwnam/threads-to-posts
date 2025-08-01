# 필드 매핑

스레드 API를 사용하여 얻은 JSON 응답의 필드와 워드프레스 포스트가 어떻게 매핑되는지 기록합니다.

## 워드프레스 필드와 매핑

예를 들어, 아래는 API 호출로 하나의 포스트를 가져온 JSON 결과입니다.

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

받아들이는 필드는 아래와 같습니다.

- id
- owner.id
- showrtcde
- text
- timestamp
- username

워드프레스 포스트는 아래처럼 표와 같이 매핑됩니다.

| WP Field              | Mapping   | Remarks                             |
|-----------------------|-----------|-------------------------------------|
| ID                    | -         | PK                                  |
| post_author           | -         | 0                                   |
| post_date             | timestamp | 워드프레스에서 지정한 지역 시간대로 변경              |
| post_date_gmt         | timestamp | UTC                                 |
| post_content          | text      |                                     |
| post_title            | shortcode | 포스트 중복 기록을 방지하기 위해 shortcode를 사용합니다 |
| post_excerpt          | -         |                                     |
| post_status           | -         | 항상 'publish' 입니다.                   |
| comment_status        | -         | 항상 'closed' 입니다.                    |
| ping_status           | -         | 항상 'closed' 입니다.                    |
| post_password         | -         |                                     |
| post_name             | -         | 스레드의 id를 사용해 `ttp-{id}`로 고정됩니다.     |
| to_ping               | -         |                                     |
| pinged                | -         |                                     |
| post_modified         | -         |                                     |
| post_modified_gmt     | -         |                                     |
| post_content_filtered | -         |                                     |
| post_parent           | -         |                                     |
| guid                  | -         |                                     |
| menu_order            | -         |                                     |
| post_type             | -         | 항상 'ttp_threads'로 고정됩니다.            |
| post_mime_type        | -         |                                     |
| comment_count         | -         |                                     |

아래는 메타 키 입니다.

| 키                        | 설명                                                             |
|--------------------------|----------------------------------------------------------------|
| _ttp_timestamp           | 이 게시물이 수집될 당시의 유닉스 타임스탬프입니다.                                   |
| _ttp_is_quote_post       | 인용된 게시물인지 표시합니다. (1.0.0-beta.4 부터)                             |
| _ttp_link_attachment_url | 외부 링크 주소 필드입니다 (1.0.0-bate.5 부터)                               |
| _ttp_media_type          | media_type 필드입니다. (1.0.0-beta.4 부터)                            |
| _ttp_media_url           | media_url 필드입니다. (1.0.0-beta.4 부터)                             |
| _ttp_reposted_post_id    | 리포스트된 포스트 ID 입니다. 자기 게시물을 리포스트한 경우에만 보입니다. (1.0.0-beta.4 부터)   |
| _ttp_topic_tag           | topic_tag 필드입니다. (1.0.0-beta.5 부터)                             |
| _ttp_owner               | owner.id 필드입니다.                                                |
| _ttp_quoted_post_id      | quoted_post.id 필드입니다. 자기 게시물을 인용할 경우에만 보입니다. (1.0.0-beta.4 부터) |
| _ttp_username            | username 필드입니다.                                                |

- 1.0.0-beta.4 부터 타 유저의 포스트를 리포스트하는 경우 간단한 크롤링을 ㅅ행합니다.
  텍스트 데이터만 일부 추출해 `post_content` 필드에 기록합니다. 모든 내용을 정확히 추출하지 못할 수도 있습니다.
