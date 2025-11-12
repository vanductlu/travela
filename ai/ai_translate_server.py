from flask import Flask, request, jsonify
from flask_cors import CORS
from transformers import MarianTokenizer, MarianMTModel
import torch

app = Flask(__name__)
CORS(app)

# Danh sách model hỗ trợ
MODELS = {
    "en-vi": "Helsinki-NLP/opus-mt-en-vi",
    "vi-en": "Helsinki-NLP/opus-mt-vi-en",
    "en-fr": "Helsinki-NLP/opus-mt-en-fr",
    "fr-en": "Helsinki-NLP/opus-mt-fr-en",
    "en-ja": "Helsinki-NLP/opus-mt-en-jap",
    "ja-en": "Helsinki-NLP/opus-mt-jap-en",
    "en-zh": "Helsinki-NLP/opus-mt-en-zh",
    "zh-en": "Helsinki-NLP/opus-mt-zh-en"
}

model_cache = {}

def get_model_pair(lang_pair):
    """Lấy hoặc load model"""
    if lang_pair not in MODELS:
        return None, None
    
    if lang_pair not in model_cache:
        print(f"Loading model: {lang_pair}...")
        model_name = MODELS[lang_pair]
        tokenizer = MarianTokenizer.from_pretrained(model_name)
        model = MarianMTModel.from_pretrained(model_name)
        model_cache[lang_pair] = (tokenizer, model)
        print(f"✓ Model loaded: {lang_pair}")
    
    return model_cache[lang_pair]

def split_into_sentences(text):
    """Chia text thành câu để dịch tốt hơn"""
    import re
    # Tách câu dựa trên dấu câu
    sentences = re.split(r'([.!?]+\s+|\n+)', text)
    # Ghép lại câu với dấu câu
    result = []
    for i in range(0, len(sentences), 2):
        if i + 1 < len(sentences):
            result.append(sentences[i] + sentences[i+1])
        else:
            result.append(sentences[i])
    return [s.strip() for s in result if s.strip()]

@app.route("/api/translate", methods=["POST"])
def translate():
    try:
        data = request.get_json()
        text = data.get("text", "").strip()
        src = data.get("src", "vi")
        tgt = data.get("tgt", "en")
        
        if not text:
            return jsonify({"translation": ""})
        
        print(f"\n{'='*50}")
        print(f"Request: {src} → {tgt}")
        print(f"Text: {text[:100]}...")
        
        lang_pair = f"{src}-{tgt}"
        tokenizer, model = get_model_pair(lang_pair)
        
        if not tokenizer or not model:
            return jsonify({
                "error": f"Không hỗ trợ {src}→{tgt}",
                "translation": f"⚠️ Không hỗ trợ dịch {src} sang {tgt}"
            }), 400
        
        # Chia nhỏ nếu text quá dài
        if len(text) > 500:
            sentences = split_into_sentences(text)
            print(f"Split into {len(sentences)} sentences")
            
            translations = []
            for sentence in sentences:
                if sentence:
                    tokens = tokenizer([sentence], return_tensors="pt", padding=True, truncation=True, max_length=512)
                    with torch.no_grad():
                        translated = model.generate(**tokens, max_length=512, num_beams=4, early_stopping=True)
                    result = tokenizer.decode(translated[0], skip_special_tokens=True)
                    translations.append(result)
            
            final_translation = " ".join(translations)
        else:
            # Dịch trực tiếp với cấu hình tốt hơn
            tokens = tokenizer([text], return_tensors="pt", padding=True, truncation=True, max_length=512)
            
            with torch.no_grad():
                translated = model.generate(
                    **tokens,
                    max_length=512,
                    num_beams=4,  # Tăng beam search
                    early_stopping=True,
                    length_penalty=1.0
                )
            
            final_translation = tokenizer.decode(translated[0], skip_special_tokens=True)
        
        print(f"Result: {final_translation[:100]}...")
        print(f"{'='*50}\n")
        
        return jsonify({"translation": final_translation})
        
    except Exception as e:
        print(f"ERROR: {str(e)}")
        return jsonify({
            "error": str(e),
            "translation": "⚠️ Lỗi khi dịch"
        }), 500

@app.route("/api/health", methods=["GET"])
def health():
    """Endpoint kiểm tra server"""
    return jsonify({
        "status": "ok",
        "models_loaded": len(model_cache),
        "supported_pairs": list(MODELS.keys())
    })

if __name__ == "__main__":
    print("\n" + "="*60)
    print("🚀 AI Translation Server")
    print("="*60)
    print("📡 Running on: http://127.0.0.1:5556")
    print("🌐 Supported languages: VI, EN, FR, JA, ZH")
    print("="*60 + "\n")
    
    app.run(host="127.0.0.1", port=5556, debug=True)