<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\clients\Chat;
use App\Models\clients\Tours;
use App\Models\clients\User;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    protected $client;
    protected $geminiModel = 'gemini-2.5-flash-preview-09-2025';
    protected $maxHistory = 6;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/',
            'headers'  => ['Content-Type' => 'application/json'],
            'timeout'  => 60.0,
        ]);
    }

    public function index(Request $request)
    {
        if (!$request->session()->has('username')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để sử dụng chatbot');
        }

        $username = $request->session()->get('username');
        $userModel = new User();
        $userId = $userModel->getUserId($username);

        if (!$userId) {
            return redirect()->route('login');
        }

        $chats = Chat::where('userId', $userId)
            ->orderBy('createdDate', 'asc')
            ->get();

        return view('clients.partials.chat', compact('chats'));
    }

    public function send(Request $request)
{
    Log::info('=== CHAT REQUEST START ===');
    
    try {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        if (!$request->session()->has('username')) {
            Log::error('User not logged in');
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để sử dụng Chatbot.'
            ], 401);
        }

        $username = $request->session()->get('username');
        Log::info('Username: ' . $username);

        $userModel = new User();
        $userId = $userModel->getUserId($username);
        
        if (!$userId) {
            Log::error('User ID not found');
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy thông tin người dùng.'
            ], 401);
        }

        Log::info('User ID: ' . $userId);

        $userMessage = $request->input('message');
        Log::info('User message: ' . $userMessage);
        
        $apiKey = env('GEMINI_API_KEY');
        
        if (empty($apiKey)) {
            Log::error('Gemini API key is empty!');
            return response()->json([
                'success' => false,
                'reply' => 'Lỗi cấu hình: Thiếu API key.'
            ], 500);
        }
        
        Log::info('API Key exists: ' . substr($apiKey, 0, 10) . '...');

        try {
            Chat::create([
                'userId' => $userId,
                'role' => 'user',
                'messages' => $userMessage,
                'createdDate' => now(),
            ]);
            Log::info('User message saved to database');
        } catch (\Exception $e) {
            Log::error('Failed to save user message: ' . $e->getMessage());
            throw $e;
        }
        Log::info('Getting tour context...');
        $context = $this->getTourContext($userMessage);
        Log::info('Context length: ' . strlen($context));
        Log::info('Getting chat history...');
        $history = Chat::where('userId', $userId)
            ->orderBy('createdDate', 'desc')
            ->limit($this->maxHistory)
            ->get()
            ->reverse();
        Log::info('History count: ' . $history->count());
        $contents = $this->buildGeminiContents($history, $userMessage, $context);
        Log::info('Contents built, count: ' . count($contents));
        
        $uri = "models/{$this->geminiModel}:generateContent?key={$apiKey}";
        Log::info('Calling Gemini API...');

        try {
            $response = $this->client->post($uri, [
                'json' => ['contents' => $contents]
            ]);
            
            Log::info('Gemini API response status: ' . $response->getStatusCode());
            
            $result = json_decode($response->getBody()->getContents(), true);
            Log::info('Gemini API result: ' . json_encode($result));

            if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('Invalid Gemini response structure');
                $reply = 'Xin lỗi, tôi gặp sự cố kỹ thuật. Vui lòng thử lại.';
            } else {
                $reply = $result['candidates'][0]['content']['parts'][0]['text'];
            }

            try {
                Chat::create([
                    'userId' => $userId,
                    'role' => 'bot',
                    'messages' => $reply,
                    'createdDate' => now(),
                ]);
                Log::info('Bot reply saved to database');
            } catch (\Exception $e) {
                Log::error('Failed to save bot reply: ' . $e->getMessage());
            }

            Log::info('=== CHAT REQUEST SUCCESS ===');
            return response()->json(['success' => true, 'reply' => $reply]);
            
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorBody = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : 'No response';
            Log::error('Gemini API RequestException: ' . $errorBody);
            
            return response()->json([
                'success' => false,
                'reply' => 'Xin lỗi, dịch vụ AI đang quá tải. Vui lòng thử lại.',
            ], 500);
        }
        
    } catch (\Exception $e) {
        Log::error('CHAT ERROR: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'reply' => 'Có lỗi xảy ra: ' . $e->getMessage(),
        ], 500);
    }
}

    private function buildGeminiContents($history, $userQuery, $context): array
    {
        $instruction = "Bạn là Chatbot AI tư vấn tour du lịch Việt Nam. 
        Hãy trả lời tự nhiên, thân thiện, dùng tiếng Việt, và gợi ý tour nếu phù hợp.
        Dữ liệu tour được cung cấp dưới đây:";

        $contents = [[
            'role' => 'user',
            'parts' => [[
                'text' => "{$instruction}\n\n=== DỮ LIỆU TOUR ===\n{$context}\n=== HẾT ==="
            ]]
        ]];

        foreach ($history as $chat) {
            $role = $chat->role === 'bot' ? 'model' : 'user';
            $contents[] = [
                'role' => $role,
                'parts' => [['text' => $chat->messages]],
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userQuery]],
        ];

        return $contents;
    }

    private function getTourContext(string $query): string
{
    $plainQuery = strtolower($this->removeAccents($query));
    
    $stopWords = [
        'the', 'thi', 'sao', 'nhu', 'la', 'co', 'gi', 'cho', 'toi', 'minh', 'ban',
        'va', 'cua', 'trong', 'voi', 'ma', 'den', 'se', 'duoc', 'hay', 'rat',
        'nhung', 'cac', 'mot', 'nay', 'do', 'khi', 'chi', 'ho', 'oi', 'a', 'an'
    ];
    
    $words = preg_split('/\s+/', $plainQuery);
    $keywords = array_filter($words, function($word) use ($stopWords) {
        return strlen($word) >= 2 && !in_array($word, $stopWords);
    });
    
    if (empty($keywords)) {
        return "Vui lòng cung cấp thêm thông tin về tour bạn muốn tìm (ví dụ: địa điểm, thời gian).";
    }
    
    Log::info('Search keywords: ' . implode(', ', $keywords));

    $tours = Tours::all()->filter(function ($tour) use ($keywords) {
        $searchText = strtolower($this->removeAccents(
            $tour->title . ' ' . 
            $tour->description . ' ' . 
            $tour->destination . ' ' . 
            $tour->domain
        ));
        
        $matchCount = 0;
        foreach ($keywords as $keyword) {
            if (str_contains($searchText, $keyword)) {
                $matchCount++;
            }
        }
        
        $threshold = max(1, ceil(count($keywords) * 0.3));
        return $matchCount >= $threshold;
    })
    ->sortByDesc(function ($tour) use ($keywords) {
        $searchText = strtolower($this->removeAccents(
            $tour->title . ' ' . 
            $tour->description . ' ' . 
            $tour->destination . ' ' . 
            $tour->domain
        ));
        
        $score = 0;
        foreach ($keywords as $keyword) {
            if (str_contains($searchText, $keyword)) {
                if (str_contains(strtolower($this->removeAccents($tour->title)), $keyword)) {
                    $score += 3;
                } else {
                    $score += 1;
                }
            }
        }
        return $score;
    })
    ->take(5);

    if ($tours->isEmpty()) {
        return "Không tìm thấy tour nào phù hợp. Bạn có thể hỏi về các địa điểm như: Nha Trang, Đà Nẵng, Hà Nội, Sapa, Phú Quốc...";
    }

    $context = "";
    foreach ($tours as $tour) {
        $context .= "Tour: {$tour->title}\n";
        $context .= "Thời gian: {$tour->time}\n";
        $context .= "Điểm đến: {$tour->destination} ({$tour->domain})\n";
        $context .= "Giá người lớn: " . number_format($tour->priceAdult) . "đ - Trẻ em: " . number_format($tour->priceChild) . "đ\n";
        $context .= "Khởi hành: {$tour->startDate} - Kết thúc: {$tour->endDate}\n";
        $context .= "Mô tả: {$tour->description}\n\n";
    }
    
    Log::info('Found ' . $tours->count() . ' matching tours');

    return $context;
}

    private function removeAccents($str)
    {
        $accents = [
            'à'=>'a','á'=>'a','ạ'=>'a','ả'=>'a','ã'=>'a','â'=>'a','ầ'=>'a','ấ'=>'a','ậ'=>'a','ẩ'=>'a','ẫ'=>'a',
            'ă'=>'a','ằ'=>'a','ắ'=>'a','ặ'=>'a','ẳ'=>'a','ẵ'=>'a','è'=>'e','é'=>'e','ẹ'=>'e','ẻ'=>'e','ẽ'=>'e',
            'ê'=>'e','ề'=>'e','ế'=>'e','ệ'=>'e','ể'=>'e','ễ'=>'e','ì'=>'i','í'=>'i','ị'=>'i','ỉ'=>'i','ĩ'=>'i',
            'ò'=>'o','ó'=>'o','ọ'=>'o','ỏ'=>'o','õ'=>'o','ô'=>'o','ồ'=>'o','ố'=>'o','ộ'=>'o','ổ'=>'o','ỗ'=>'o',
            'ơ'=>'o','ờ'=>'o','ớ'=>'o','ợ'=>'o','ở'=>'o','ỡ'=>'o','ù'=>'u','ú'=>'u','ụ'=>'u','ủ'=>'u','ũ'=>'u',
            'ư'=>'u','ừ'=>'u','ứ'=>'u','ự'=>'u','ử'=>'u','ữ'=>'u','ỳ'=>'y','ý'=>'y','ỵ'=>'y','ỷ'=>'y','ỹ'=>'y','đ'=>'d',
            'À'=>'A','Á'=>'A','Ạ'=>'A','Ả'=>'A','Ã'=>'A','Â'=>'A','Ầ'=>'A','Ấ'=>'A','Ậ'=>'A','Ẩ'=>'A','Ẫ'=>'A',
            'Ă'=>'A','Ằ'=>'A','Ắ'=>'A','Ặ'=>'A','Ẳ'=>'A','Ẵ'=>'A','È'=>'E','É'=>'E','Ẹ'=>'E','Ẻ'=>'E','Ẽ'=>'E',
            'Ê'=>'E','Ề'=>'E','Ế'=>'E','Ệ'=>'E','Ể'=>'E','Ễ'=>'E','Ì'=>'I','Í'=>'I','Ị'=>'I','Ỉ'=>'I','Ĩ'=>'I',
            'Ò'=>'O','Ó'=>'O','Ọ'=>'O','Ỏ'=>'O','Õ'=>'O','Ô'=>'O','Ồ'=>'O','Ố'=>'O','Ộ'=>'O','Ổ'=>'O','Ỗ'=>'O',
            'Ơ'=>'O','Ờ'=>'O','Ớ'=>'O','Ợ'=>'O','Ở'=>'O','Ỡ'=>'O','Ù'=>'U','Ú'=>'U','Ụ'=>'U','Ủ'=>'U','Ũ'=>'U',
            'Ư'=>'U','Ừ'=>'U','Ứ'=>'U','Ự'=>'U','Ử'=>'U','Ữ'=>'U','Ỳ'=>'Y','Ý'=>'Y','Ỵ'=>'Y','Ỷ'=>'Y','Ỹ'=>'Y','Đ'=>'D'
        ];

        return strtr($str, $accents);
    }
}