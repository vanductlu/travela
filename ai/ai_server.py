from flask import Flask, request, jsonify
from flask_cors import CORS
import unicodedata
app = Flask(__name__)
CORS(app)

# Dữ liệu mẫu (hardcode) — thay bằng DB hoặc load từ file khi cần
all_tours = [
    {"tourId": 1, "title": "Tour Đà Nẵng 3N2Đ"},
    {"tourId": 2, "title": "Tour Phú Quốc cao cấp"},
    {"tourId": 3, "title": "Tour Hạ Long vịnh đẹp"},
    {"tourId": 4, "title": "Tour Đà Lạt mùa hoa"},
    {"tourId": 5, "title": "Tour Cần Thơ miệt vườn"},
    {"tourId": 6, "title": "Tour Ninh Bình Tràng An"},
    {"tourId": 7, "title": "Tour Quảng Ninh Hạ Long"},
    {"tourId": 8, "title": "Tour Sài Gòn - Miền Tây 4N3Đ"}
]

def normalize_text(text):
    """
    Chuyển tiếng Việt có dấu -> không dấu + viết thường.
    """
    text = unicodedata.normalize('NFD', text)
    text = ''.join(c for c in text if unicodedata.category(c) != 'Mn')
    return text.lower()

@app.route('/api/search-suggestions', methods=['GET'])
def search_suggestions():
    keyword = request.args.get('keyword', '').strip()
    if not keyword:
        return jsonify({"suggestions": [t["title"] for t in all_tours[:6]], "count": 6})

    normalized_keyword = normalize_text(keyword)

    suggestions = [
        t["title"]
        for t in all_tours
        if normalized_keyword in normalize_text(t["title"])
    ]
    return jsonify({"suggestions": suggestions[:10], "count": len(suggestions)})

@app.route('/api/search-tours', methods=['GET'])
def search_tours():
    """
    Trả về cấu trúc 'related_tours' (mảng object) để phù hợp với controller Laravel searchTours.
    Query: ?keyword=...
    """
    keyword = request.args.get('keyword', '').strip().lower()
    if not keyword:
        return jsonify({"related_tours": []})

    related = [t for t in all_tours if keyword in t["title"].lower()]
    # Nếu muốn trả thêm fields, mở rộng object ở all_tours
    return jsonify({"related_tours": related})

if __name__ == '__main__':
    # Chạy local, port 5555
    app.run(host='127.0.0.1', port=5555, debug=True)
