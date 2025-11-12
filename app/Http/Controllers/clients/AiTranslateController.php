<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
class AiTranslateController extends Controller
{
     public function translate(Request $request)
    {
        try {
            // Lấy dữ liệu từ request
            $text = $request->input('text');
            $src = $request->input('src', 'en');
            $tgt = $request->input('tgt', 'vi');
            
            // Gọi Flask API
            $response = Http::timeout(30)->post('http://127.0.0.1:5556/api/translate', [
                'text' => $text,
                'src' => $src,
                'tgt' => $tgt
            ]);
            
            // Trả về kết quả
            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'error' => 'Translation service error',
                    'translation' => 'Không thể dịch'
                ], 500);
            }
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'translation' => 'Lỗi kết nối server'
            ], 500);
        }
    }
}
