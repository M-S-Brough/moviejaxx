<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Log\LoggerInterface;

class MovieApiService {
    // Define properties for the API client, API key, and logger
    private Client $client; // Guzzle HTTP client
    private string $apiKey; // API key for authorization
    private LoggerInterface $logger; // Logger for logging API requests and responses

    // Constructor to initialize the API service with the API key and logger
    public function __construct(string $apiKey, LoggerInterface $logger) {
        // Initialize Guzzle HTTP client with base URI and authorization header
        $this->client = new Client([
            'base_uri' => 'https://api.themoviedb.org/3/',
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey // Authorization header with API key
            ]
        ]);
        // Assign API key and logger to class properties
        $this->apiKey = $apiKey;
        $this->logger = $logger;
    }

    // Method to fetch movie details by ID from the API
    public function fetchMovieDetailsById(int $id) {
        try {
            // Send GET request to fetch movie details by ID
            $response = $this->client->request('GET', "movie/{$id}", [
                'query' => [
                    'api_key' => $this->apiKey, // API key parameter
                    'append_to_response' => 'credits' // Additional parameter to fetch credits (cast and crew)
                ]
            ]);
            // Decode JSON response body to array
            $data = json_decode($response->getBody()->getContents(), true);

            // Log raw API response
            $this->logger->info('API response', ['response' => $data]);

            // Filter and extract necessary details from the API response
            $filteredData = [
                'title' => $data['title'] ?? 'N/A',
                'release_date' => $data['release_date'] ?? 'N/A',
                'overview' => $data['overview'] ?? 'No description available.',
                'director' => $this->extractDirector($data['credits'] ?? []),
                'cast' => $this->extractCast($data['credits'] ?? [], 5), // Extracts top 5 cast members
                'runtime' => $data['runtime'] ?? 'N/A'
            ];

            // Log filtered data before returning
            $this->logger->info('Filtered movie details', ['details' => $filteredData]);

            return $filteredData;
        } catch (GuzzleException $e) {
            // Log error if fetching movie details fails
            $this->logger->error('Failed to fetch movie details by ID', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return ['error' => 'Failed to fetch movie details.'];
        }
    }

    // Method to extract director from the credits data
    private function extractDirector(array $credits) {
        foreach ($credits['crew'] as $member) {
            if ($member['job'] === 'Director') {
                return $member['name'];
            }
        }
        return 'Director not found';
    }

    // Method to extract cast members from the credits data
    private function extractCast(array $credits, $limit = 5) {
        $castList = [];
        $count = 0;
        foreach ($credits['cast'] as $member) {
            if ($count >= $limit) break;
            $castList[] = [
                'name' => $member['name'],
                'character' => $member['character']
            ];
            $count++;
        }
        return $castList;
    }

    // Method to search movies by a query string
    public function searchMovies(string $query): array {
        try {
            // Send GET request to search movies based on the query
            $response = $this->client->request('GET', 'search/movie', [
                'query' => [
                    'api_key' => $this->apiKey, // API key parameter
                    'query' => $query // Query parameter for movie search
                ]
            ]);
            // Decode JSON response body to array and return
            return json_decode($response->getBody()->getContents(), true);
        } catch (GuzzleException $e) {
            // Return error message if searching movies fails
            return ['error' => $e->getMessage()];
        }
    }
}
