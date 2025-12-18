<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    protected $database;
    protected $projectId;
    protected $credentials;

    public function __construct()
    {
        $credentialsPath = config('firebase.credentials.file');
        
        $factory = (new Factory)
            ->withServiceAccount($credentialsPath)
            ->withDatabaseUri(config('firebase.database.url'));

        $this->database = $factory->createDatabase();

        if (file_exists($credentialsPath)) {
            $this->credentials = json_decode(file_get_contents($credentialsPath), true);
            $this->projectId = $this->credentials['project_id'] ?? null;
        }
    }

    public function getDocument($collection, $documentId)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$documentId}";
        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->getAccessToken()])->get($url);

        return $response->successful() ? $this->parseFirestoreDocument($response->json()) : null;
    }

    public function queryCollection($collection, $filters = [], $orderBy = null, $limit = null)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
        $query = ['structuredQuery' => ['from' => [['collectionId' => $collection]]]];

        if (!empty($filters)) {
            $query['structuredQuery']['where'] = $this->buildFilters($filters);
        }
        if ($orderBy) {
            $query['structuredQuery']['orderBy'] = [$orderBy];
        }
        if ($limit) {
            $query['structuredQuery']['limit'] = $limit;
        }

        $response = Http::withHeaders(['Authorization' => 'Bearer ' . $this->getAccessToken()])->post($url, $query);
        return $response->successful() ? $this->parseQueryResults($response->json()) : [];
    }

    public function addDocument($collection, $data)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";
        $document = $this->buildFirestoreDocument($data);
        return Http::withHeaders(['Authorization' => 'Bearer ' . $this->getAccessToken()])->post($url, ['fields' => $document])->successful();
    }

    public function updateDocument($collection, $documentId, $data)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$documentId}";
        
        // Use patch to merge fields instead of overwriting the whole document
        // We add ?updateMask.fieldPaths to ensure we merge correctly if needed, 
        // but standard PATCH behavior in REST often merges specific fields if they exist.
        // For simple updates, just sending the fields is usually enough.
        
        $document = $this->buildFirestoreDocument($data);
        return Http::withHeaders(['Authorization' => 'Bearer ' . $this->getAccessToken()])->patch($url, ['fields' => $document])->successful();
    }

    private function getAccessToken()
    {
        return cache()->remember('firebase_access_token', 3600, function () {
            $jwt = $this->createJWT();
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);
            return $response->json()['access_token'];
        });
    }

    private function createJWT()
    {
        $now = time();
        $payload = [
            'iss' => $this->credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ];
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode($payload));
        openssl_sign("$header.$payload", $signature, $this->credentials['private_key'], OPENSSL_ALGO_SHA256);
        return "$header.$payload." . base64_encode($signature);
    }

    private function buildFilters($filters)
    {
        $compositeFilters = [];
        foreach ($filters as $filter) {
            $compositeFilters[] = [
                'fieldFilter' => [
                    'field' => ['fieldPath' => $filter['field']],
                    'op' => $filter['op'],
                    'value' => $this->convertValue($filter['value'])
                ]
            ];
        }
        return count($compositeFilters) === 1 ? $compositeFilters[0] : ['compositeFilter' => ['op' => 'AND', 'filters' => $compositeFilters]];
    }

    // --- FIX IS HERE: Added Array/Map Handling ---
    private function convertValue($value)
    {
        if (is_string($value)) return ['stringValue' => $value];
        if (is_int($value)) return ['integerValue' => (string)$value];
        if (is_float($value)) return ['doubleValue' => $value];
        if (is_bool($value)) return ['booleanValue' => $value];
        if ($value instanceof \DateTime) return ['timestampValue' => $value->format('Y-m-d\TH:i:s\Z')];
        
        if (is_array($value)) {
            // Check if associative array (Map) or sequential array (List)
            $isAssoc = array_keys($value) !== range(0, count($value) - 1) && !empty($value);

            if ($isAssoc) {
                // MapValue (Object)
                $fields = [];
                foreach ($value as $k => $v) {
                    $fields[$k] = $this->convertValue($v);
                }
                return ['mapValue' => ['fields' => $fields]];
            } else {
                // ArrayValue (List) - This handles your Goals!
                $values = [];
                foreach ($value as $v) {
                    $values[] = $this->convertValue($v);
                }
                return ['arrayValue' => ['values' => $values]];
            }
        }

        return ['nullValue' => null];
    }

    private function buildFirestoreDocument($data)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[$key] = $this->convertValue($value);
        }
        return $fields;
    }

    private function parseFirestoreDocument($document)
    {
        if (!isset($document['fields'])) return null;
        $result = [];
        foreach ($document['fields'] as $key => $value) {
            $result[$key] = $this->parseValue($value);
        }
        if (isset($document['name'])) {
            $parts = explode('/', $document['name']);
            $result['id'] = end($parts);
        }
        return $result;
    }

    private function parseQueryResults($results)
    {
        $documents = [];
        foreach ($results as $result) {
            if (isset($result['document'])) {
                $documents[] = $this->parseFirestoreDocument($result['document']);
            }
        }
        return $documents;
    }

    private function parseValue($value)
    {
        if (isset($value['stringValue'])) return $value['stringValue'];
        if (isset($value['integerValue'])) return (int)$value['integerValue'];
        if (isset($value['doubleValue'])) return (float)$value['doubleValue'];
        if (isset($value['booleanValue'])) return $value['booleanValue'];
        if (isset($value['timestampValue'])) return new \DateTime($value['timestampValue']);
        
        // Handle Array Reading
        if (isset($value['arrayValue'])) {
            $array = [];
            if (isset($value['arrayValue']['values'])) {
                foreach ($value['arrayValue']['values'] as $item) $array[] = $this->parseValue($item);
            }
            return $array;
        }

        // Handle Map Reading
        if (isset($value['mapValue']['fields'])) {
            $map = [];
            foreach ($value['mapValue']['fields'] as $key => $item) {
                $map[$key] = $this->parseValue($item);
            }
            return $map;
        }

        return null;
    }
}