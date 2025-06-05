#  감정 기반 음악 추천 챗봇 (Rule-Based)

##  프로젝트 소개

사용자의 사건(Event)과 감정(Emotion)을 입력받아, 감정에 맞는 유튜브 음악을 추천해주는 PHP 기반 챗봇 시스템입니다.

- 로그인 / 회원가입 기능
- 사건과 감정을 DB에 저장
- 감정 통계 시각화 (Chart.js)
- 간단한 룰 기반 감정 인식
- Docker + MariaDB + PHP + Apache로 실행

---

##  실행 방법

### 1. 프로젝트 클론

```bash
git clone https://github.com/OhJisong/Linix-project_Rule-based-chatbot.git
cd chatbot-project
```

### 2. 도커 실행

```bash
docker-compose up --build
```

### 3. 웹사이트 접속

```
http://localhost:8080
```

---

##  감정 분류 (Rule-Based)

| 감정     | 키워드 예시                              | 유튜브 검색 문구             |
|----------|------------------------------------------|------------------------------|
| 분노     | 화나, 짜증, 빡쳐, 열받아                 | 화났을 때 듣는 노래 플레이리스트 |
| 슬픔     | 우울, 슬퍼, 눈물, 절망                   | 우울할 때 듣는 노래 플레이리스트 |
| 외로움   | 외로워, 혼자, 쓸쓸해                     | 외로울 때 듣는 노래 플레이리스트 |
| 불안     | 불안, 초조, 긴장, 두려움                 | 불안할 때 듣는 노래 플레이리스트 |
| 걱정     | 걱정돼, 고민, 신경 쓰여                   | 걱정이 많을 때 듣는 노래 플레이리스트 |
| 사랑     | 사랑해, 좋아해, 설레, 그리워             | 사랑에 빠졌을 때 듣는 노래 플레이리스트 |
| 기쁨     | 행복해, 기뻐, 신나, 즐거워               | 신날 때 듣는 노래 플레이리스트 |

---

##  데이터베이스 구조

### users
| 필드명     | 타입         | 설명          |
|------------|--------------|---------------|
| id         | VARCHAR      | 사용자 ID (PK) |
| nickname   | VARCHAR      | 사용자 닉네임 |
| password   | VARCHAR      | 해시된 비밀번호 |

### chat_history
| 필드명       | 타입         | 설명              |
|--------------|--------------|-------------------|
| id           | INT(AI)      | 고유번호 (PK)     |
| user_id      | VARCHAR      | 사용자 ID (FK)    |
| event        | TEXT         | 사용자가 말한 사건 |
| emotion      | VARCHAR      | 감정 (7가지 중 하나) |
| youtube_link | TEXT         | 추천된 유튜브 링크 |
| created_at   | DATETIME     | 기록 시각         |

---

##  외부 접속용 QR 생성

1. ngrok 설치 후 아래 명령 실행

```bash
ngrok http 8080
```

2. 생성된 주소(`https://xxxx.ngrok.io`) 복사
3. [QR 코드 생성기](https://www.qr-code-generator.com/)로 QR 이미지 만들기
4. `/images/qr_code.png`에 저장 후 README에 추가

```markdown
![QR 코드](images/qr_code.png)
```

---

##  Docker 구조

- `docker-compose.yml`
    - PHP + Apache
    - MariaDB
    - phpMyAdmin (포트 8081)

---

##  스크린샷 예시

- 로그인/회원가입 화면
- 대화 인터페이스
- 감정 통계 차트
- QR 코드 접속

> `/images` 폴더에 포함

---

## 기타

- AI 모델 제거됨 (룰 기반만 적용)
- PHP 세션 기반 로그인 유지
- 모든 기능은 `chatbot.php`, `index.php`, `emotion_stats.php` 등으로 모듈화

---

## 문의

- 작성자: 오지송 (Oh Jisong)
- Email: ohjisong@example.com
- GitHub: https://github.com/OhJisong

