from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector
from mysql.connector import Error
import unicodedata

app = Flask(__name__)
CORS(app, resources={
    r"/api/*": {
        "origins": "*",
        "methods": ["GET", "POST", "OPTIONS"],
        "allow_headers": ["Content-Type"]
    }
})

# ✅ CẤU HÌNH DATABASE - THAY ĐỔI THEO DATABASE CỦA BẠN
DB_CONFIG = {
    'host': '127.0.0.1',
    'user': 'root',          # Thay bằng username MySQL của bạn
    'password': '',          # Thay bằng password MySQL của bạn
    'database': 'travela',   # Thay bằng tên database của bạn
    'port': 3306
}

def get_db_connection():
    """Tạo kết nối đến MySQL database"""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        if connection.is_connected():
            return connection
    except Error as e:
        print(f"Lỗi kết nối database: {e}")
        return None

def normalize_text(text):
    """Chuyển tiếng Việt có dấu -> không dấu + viết thường"""
    text = unicodedata.normalize('NFD', text)
    text = ''.join(c for c in text if unicodedata.category(c) != 'Mn')
    return text.lower()

@app.route('/api/search-suggestions', methods=['GET'])
def search_suggestions():
    """API gợi ý tìm kiếm từ database MySQL"""
    keyword = request.args.get('keyword', '').strip()
    
    if not keyword or len(keyword) < 2:
        return jsonify({"suggestions": [], "count": 0})
    
    connection = get_db_connection()
    if not connection:
        return jsonify({"suggestions": [], "count": 0, "error": "Database connection failed"})
    
    try:
        cursor = connection.cursor(dictionary=True)
        
        # Query tìm kiếm trong database
        query = """
            SELECT DISTINCT title, destination 
            FROM tbl_tours 
            WHERE availability = 1 
            AND (title LIKE %s OR destination LIKE %s OR description LIKE %s)
            LIMIT 8
        """
        
        search_param = f"%{keyword}%"
        cursor.execute(query, (search_param, search_param, search_param))
        tours = cursor.fetchall()
        
        print(f"Tìm thấy {len(tours)} tours cho keyword: {keyword}")
        
        # Tạo danh sách gợi ý
        suggestions = []
        seen = set()
        
        for tour in tours:
            # Thêm title nếu match
            if keyword.lower() in tour['title'].lower() and tour['title'] not in seen:
                suggestions.append(tour['title'])
                seen.add(tour['title'])
            
            # Thêm destination nếu match
            dest_suggestion = f"Tour {tour['destination']}"
            if keyword.lower() in tour['destination'].lower() and dest_suggestion not in seen:
                suggestions.append(dest_suggestion)
                seen.add(dest_suggestion)
        
        # Giới hạn số lượng gợi ý
        suggestions = suggestions[:6]
        
        cursor.close()
        connection.close()
        
        return jsonify({
            "suggestions": suggestions,
            "count": len(suggestions)
        })
        
    except Error as e:
        print(f"Lỗi query database: {e}")
        if connection and connection.is_connected():
            connection.close()
        return jsonify({"suggestions": [], "count": 0, "error": str(e)})

@app.route('/api/search-tours', methods=['GET'])
def search_tours():
    """API tìm kiếm tours trả về danh sách tourId"""
    keyword = request.args.get('keyword', '').strip().lower()
    
    if not keyword:
        return jsonify({"related_tours": []})
    
    connection = get_db_connection()
    if not connection:
        return jsonify({"related_tours": []})
    
    try:
        cursor = connection.cursor(dictionary=True)
        
        query = """
            SELECT tourId, title, destination, description 
            FROM tbl_tours 
            WHERE availability = 1 
            AND (title LIKE %s OR destination LIKE %s OR description LIKE %s)
            LIMIT 12
        """
        
        search_param = f"%{keyword}%"
        cursor.execute(query, (search_param, search_param, search_param))
        tours = cursor.fetchall()
        
        cursor.close()
        connection.close()
        
        return jsonify({"related_tours": tours})
        
    except Error as e:
        print(f"Lỗi query: {e}")
        if connection and connection.is_connected():
            connection.close()
        return jsonify({"related_tours": []})

@app.route('/test-db', methods=['GET'])
def test_db():
    """Endpoint test kết nối database"""
    connection = get_db_connection()
    if connection:
        try:
            cursor = connection.cursor()
            cursor.execute("SELECT COUNT(*) as total FROM tbl_tours WHERE availability = 1")
            result = cursor.fetchone()
            cursor.close()
            connection.close()
            return jsonify({
                "status": "success",
                "message": "Kết nối database thành công",
                "total_tours": result[0]
            })
        except Error as e:
            return jsonify({
                "status": "error",
                "message": f"Lỗi query: {e}"
            })
    else:
        return jsonify({
            "status": "error",
            "message": "Không thể kết nối database. Kiểm tra cấu hình DB_CONFIG"
        })

if __name__ == '__main__':
    # Test kết nối khi khởi động
    print("Đang test kết nối database...")
    conn = get_db_connection()
    if conn:
        print("✅ Kết nối database thành công!")
        conn.close()
    else:
        print("❌ Không thể kết nối database. Kiểm tra cấu hình!")
    
    # Chạy Flask server
    app.run(host='127.0.0.1', port=5555, debug=True)