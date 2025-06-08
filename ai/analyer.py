from flask import Flask, request, jsonify
from konlpy.tag import Okt

app = Flask(__name__)
okt = Okt()

emotion_keywords = {
    '분노': ['화', '짜증', '열받', '빡치', '성질', '발끈', '분통', '미치겠어', '답답', '복수', '격앙', '성가심'],
    '슬픔': ['슬프', '우울', '눈물', '상실', '지치', '힘들', '울적', '허탈', '괴로움', '체념'],
    '외로움': ['외롭', '쓸쓸', '고독', '버림받은', '고립', '단절', '외딴', '유배'],
    '불안': ['불안', '초조', '긴장', '걱정', '두렵', '조급', '긴박', '조마조마', '불길한 예감'],
    '걱정': ['걱정', '고민', '불확실', '속상', '복잡', '안쓰러워', '애가 타', '안절부절', '초조', '연민'],
    '사랑': ['좋아', '사랑', '설레', '그립', '끌림'],
    '기쁨': ['행복', '기쁨', '좋아', '신나', '즐거', '상쾌', '흐뭇', '벅참' , '환희']
}

@app.route('/analyze', methods=['POST'])
def analyze():
    text = request.json.get('text', '')
    words = okt.morphs(text)

    for emotion, keywords in emotion_keywords.items():
        for word in words:
            if any(k in word for k in keywords):
                return jsonify({'emotion': emotion})

    return jsonify({'emotion': '기쁨'})  # default

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
