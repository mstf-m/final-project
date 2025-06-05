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
        if (empty($this->apiKey)) {
            Log::error('Gemini API key is not set in .env file');
        }
    }

    public function generateTags(string $title, string $description): array
    {
        try {
            if (empty($title) || empty($description)) {
                Log::error('Empty title or description provided', [
                    'title' => $title,
                    'description' => $description
                ]);
                return [];
            }

            $prompt = $this->buildPrompt($title, $description);
            
            Log::info('Sending request to Gemini AI', [
                'prompt' => $prompt,
                'api_url' => $this->apiUrl,
                'title_length' => mb_strlen($title),
                'description_length' => mb_strlen($description),
                'api_key_length' => strlen($this->apiKey)
            ]);

            $requestData = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 100,
                ]
            ];

            Log::info('Request data', ['data' => $requestData]);

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '?key=' . $this->apiKey, $requestData);

            Log::info('Raw API Response', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Parsed API Response', ['result' => $result]);
                
                // Extract tags from the response and convert to array
                $tags = $this->parseTagsFromResponse($result);
                Log::info('Final parsed tags', ['tags' => $tags]);
                return $tags;
            }

            Log::error('Gemini AI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers()
            ]);
            return [];
        } catch (\Exception $e) {
            Log::error('Gemini AI Service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return [];
        }
    }

    protected function buildPrompt(string $title, string $description): string
    {
        return "You are a helpful assistant that generates relevant tags for activities. Based on the following activity information, generate relevant tags (keywords) that describe this activity. 
        The text is in Farsi/Persian, and please return the tags in Farsi/Persian as well.
        Return ONLY a JSON array of strings, nothing else. Maximum 5 tags.
        Make sure to return valid JSON format.
        
        Title: {$title}
        Description: {$description}
        
        Example response format: ['ورزشی', 'در فضای باز', 'تیمی']
        
        Remember to return ONLY the JSON array, no other text or explanation.";
    }

    protected function parseTagsFromResponse(array $response): array
    {
        try {
            Log::info('Starting to parse response', ['response' => $response]);
            
            if (empty($response['candidates'][0]['content']['parts'][0]['text'])) {
                Log::error('No text content in response', ['response' => $response]);
                return [];
            }

            // Extract the text content from the response
            $text = $response['candidates'][0]['content']['parts'][0]['text'];
            
            Log::info('Extracted text from response', ['text' => $text]);
            
            // Clean the response and parse as JSON
            $text = trim($text, "` \t\n\r\0\x0B");
            
            // Try to extract JSON array if it's wrapped in other text
            if (preg_match('/\[.*\]/', $text, $matches)) {
                $text = $matches[0];
            }
            
            Log::info('Cleaned text before JSON decode', ['text' => $text]);
            
            $tags = json_decode($text, true);
            
            Log::info('JSON decode result', ['tags' => $tags, 'json_last_error' => json_last_error_msg()]);
            
            if (!is_array($tags)) {
                Log::error('Invalid tags format', [
                    'text' => $text,
                    'json_error' => json_last_error_msg()
                ]);
                return [];
            }

            // Ensure all tags are strings and trim them
            $tags = array_map(function($tag) {
                return trim((string)$tag);
            }, $tags);

            // Remove any empty tags
            $tags = array_filter($tags);

            $finalTags = array_values($tags); // Re-index array
            
            Log::info('Final processed tags', ['tags' => $finalTags]);
            
            return $finalTags;
        } catch (\Exception $e) {
            Log::error('Error parsing Gemini AI response', [
                'error' => $e->getMessage(),
                'response' => $response,
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            return [];
        }
    }
} 