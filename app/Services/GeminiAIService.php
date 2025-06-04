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

    public function generateTags(string $title, string $description, ?float $latitude, ?float $longitude): array
    {
        try {
            $prompt = $this->buildPrompt($title, $description, $latitude, $longitude);
            
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

            if ($response->successful()) {
                $result = $response->json();
                // Extract tags from the response and convert to array
                $tags = $this->parseTagsFromResponse($result);
                return $tags;
            }

            Log::error('Gemini AI API error: ' . $response->body());
            return [];
        } catch (\Exception $e) {
            Log::error('Gemini AI Service error: ' . $e->getMessage());
            return [];
        }
    }

    protected function buildPrompt(string $title, string $description, ?float $latitude, ?float $longitude): string
    {
        $locationInfo = '';
        if ($latitude && $longitude) {
            $locationInfo = "Location coordinates: Latitude {$latitude}, Longitude {$longitude}.";
        }

        return "Based on the following activity information, generate relevant tags (keywords) that describe this activity. 
        Return only a JSON array of strings, nothing else. Maximum 10 tags. also add the location name of coordinates as a tag.
        
        Title: {$title}
        Description: {$description}
        {$locationInfo}
        
        Example response format: ['sports', 'outdoor', 'team']";
    }

    protected function parseTagsFromResponse(array $response): array
    {
        try {
            // Extract the text content from the response
            $text = $response['candidates'][0]['content']['parts'][0]['text'] ?? '';
            
            // Clean the response and parse as JSON
            $text = trim($text, "` \t\n\r\0\x0B");
            $tags = json_decode($text, true);
            
            return is_array($tags) ? $tags : [];
        } catch (\Exception $e) {
            Log::error('Error parsing Gemini AI response: ' . $e->getMessage());
            return [];
        }
    }
} 