<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiAIService
{
    protected $apiKey;
    protected $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    public function generateTags(string $title, string $description): array
    {
        try {
            $prompt = $this->buildPrompt($title, $description);
            
            Log::info('Sending request to Gemini AI', [
                'prompt' => $prompt,
                'api_url' => $this->apiUrl
            ]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ]
            ]);

            Log::info('Gemini AI Response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                // Extract tags from the response and convert to array
                $tags = $this->parseTagsFromResponse($result);
                Log::info('Parsed tags', ['tags' => $tags]);
                return $tags;
            }

            Log::error('Gemini AI API error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return [];
        } catch (\Exception $e) {
            Log::error('Gemini AI Service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }

    protected function buildPrompt(string $title, string $description): string
    {
        return "You are a helpful assistant that generates relevant tags for activities. Based on the following activity information, generate relevant tags (keywords) that describe this activity. 
        Return ONLY a JSON array of strings, nothing else. Maximum 5 tags.
        
        Title: {$title}
        Description: {$description}
        
        Example response format: ['sports', 'outdoor', 'team']
        
        Remember to return ONLY the JSON array, no other text.";
    }

    protected function parseTagsFromResponse(array $response): array
    {
        try {
            Log::info('Parsing response', ['response' => $response]);
            
            // Extract the text content from the response
            $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            Log::info('Extracted text', ['text' => $text]);
            
            // Clean the response and parse as JSON
            $text = trim($text, "` \t\n\r\0\x0B");
            $tags = json_decode($text, true);
            
            Log::info('Decoded tags', ['tags' => $tags]);
            
            return is_array($tags) ? $tags : [];
        } catch (\Exception $e) {
            Log::error('Error parsing Gemini AI response', [
                'error' => $e->getMessage(),
                'response' => $response
            ]);
            return [];
        }
    }
} 