class googleAnalyticsDataApi {
    private $_serviceAccountKeyFile = '';
    private $_authData = [];
    private $_start_date = '';
    private $_end_date = '';
    private $_property_id = '';
    
     public function __construct($data = []) {
        $this->_serviceAccountKeyFile = ((!empty($data['path']))?$data['path']:'');
        $this->_start_date = ((!empty($data['start_date']))?$data['start_date']:date('Y-m-d',strtotime('-14 days', strtotime(date('Y-m-d')))));
        $this->_end_date = ((!empty($data['end_date']))?$data['end_date']:date('Y-m-d'));
        $this->_property_id = ((!empty($data['property_id']))?$data['property_id']:'');
    }

    function fetch($debug = false) {
        $result = [];
        if(!empty($this->oAuth2($debug))) {
            // total
            $postData = 
            [
                'dateRanges' => 
                [
                    [
                        'startDate' =>  $this->_start_date,
                        'endDate'   =>  $this->_end_date
                    ]
                ],
                'metrics' => 
                [
                    [
                        'name'      =>  'screenPageViews'
                    ],
                    [
                        'name'      =>  'activeUsers',
                    ],
                    [
                        'name'      =>  'eventCount',
                    ]
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://analyticsdata.googleapis.com/v1beta/properties/' . $this->_property_id . ':runReport');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->_authData['access_token'],
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            $response = curl_exec($ch);
            curl_close($ch);
            $result_total = json_decode($response, true);
            if(!empty($result_total) && !empty($result_total['metricHeaders'])) {
                $metricHeaders = $result_total['metricHeaders'];
                $metricValues = $result_total['rows'][0]['metricValues'];
                $result['total'] = [];
                foreach ($metricHeaders as $key => $header) {
                    $result['total'][$header['name']] = (!empty($metricValues[$key]['value']))?$metricValues[$key]['value']:0;
                }
            }
            
            // page
            $postData = 
            [
                'dateRanges' => 
                [
                    [
                        'startDate' =>  $this->_start_date,
                        'endDate'   =>  $this->_end_date
                    ]
                ],
                'metrics' => 
                [
                    [
                        'name'      =>  'screenPageViews'
                    ],
                    [
                        'name'      =>  'activeUsers',
                    ],
                    [
                        'name'      =>  'eventCount',
                    ]
                ],
                'dimensions' => 
                [
                    [
                        'name'      =>  'pageTitle',
                    ],
                    [
                        'name'      =>  'pagePath'
                    ]
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://analyticsdata.googleapis.com/v1beta/properties/' . $this->_property_id . ':runReport');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->_authData['access_token'],
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

            $response = curl_exec($ch);
            curl_close($ch);
            $result_page = json_decode($response, true);
            if(!empty($result_page) && !empty($result_page['metricHeaders'])) {
                $metricHeaders = $result_page['metricHeaders'];
                $result['page'] = [];
                if(!empty($result_page['rows'])) {
                    foreach ($result_page['rows'] as $row) {
                        $dimensionValues = $row['dimensionValues'];
                        $metricValues = $row['metricValues'];
                        $page_index = md5(json_encode($dimensionValues));
                        $result['page'][$page_index]['title'] = $dimensionValues[0]['value'];
                        $result['page'][$page_index]['path'] = $dimensionValues[1]['value'];
                        foreach ($metricHeaders as $key => $header) {
                            $result['page'][$page_index][$header['name']] = (!empty($metricValues[$key]['value']))?$metricValues[$key]['value']:0;
                        }
                    }
                }
            }
            
            
            // daily
            $postData = 
            [
                'dateRanges' => 
                [
                    [
                        'startDate' =>  $this->_start_date,
                        'endDate'   =>  $this->_end_date
                    ]
                ],
                'metrics' => 
                [
                    [
                        'name'      =>  'screenPageViews'
                    ],
                    [
                        'name'      =>  'activeUsers',
                    ],
                    [
                        'name'      =>  'eventCount',
                    ]
                ],
                'dimensions' => 
                [
                    [
                        'name'      =>  'date'
                    ],
                    [
                        'name'      =>  'pageTitle',
                    ],
                    [
                        'name'      =>  'pagePath'
                    ]
                ]
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://analyticsdata.googleapis.com/v1beta/properties/' . $this->_property_id . ':runReport');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $this->_authData['access_token'],
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

            $response = curl_exec($ch);
            curl_close($ch);
            $result_daily = json_decode($response, true);
            if(!empty($result_daily) && !empty($result_daily['metricHeaders'])) {
                $metricHeaders = $result_daily['metricHeaders'];
                $result['daily'] = [];
                $result['daily_total'] = [];
                if(!empty($result_daily['rows'])) {
                    foreach ($result_daily['rows'] as $row) {
                        $dimensionValues = $row['dimensionValues'];
                        $metricValues = $row['metricValues'];
                        $daily_index = md5(json_encode($dimensionValues));
                        $result['daily'][$dimensionValues[0]['value']][$daily_index]['title'] = $dimensionValues[1]['value'];
                        $result['daily'][$dimensionValues[0]['value']][$daily_index]['path'] = $dimensionValues[2]['value'];
                        foreach ($metricHeaders as $key => $header) {
                            $result['daily'][$dimensionValues[0]['value']][$daily_index][$header['name']] = (!empty($metricValues[$key]['value']))?$metricValues[$key]['value']:0;
                            if($header['name'] == 'eventCount') {
                                if(empty($result['daily_total'][$dimensionValues[0]['value']])) {
                                    $result['daily_total'][$dimensionValues[0]['value']] = 0;
                                }
                                $result['daily_total'][$dimensionValues[0]['value']] += ((!empty($metricValues[$key]['value']))?$metricValues[$key]['value']:0);
                            }
                        }
                    }
                }
            }
        }
        
        return $result;
    }
    
    private function oAuth2($debug = false) {
        // Load service account credentials from JSON file
        $credentials = json_decode(file_get_contents($this->_serviceAccountKeyFile), true);

        // OAuth 2.0 token endpoint
        $tokenEndpoint = 'https://oauth2.googleapis.com/token';

        // Set POST fields for token request
        $fields = [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $this->generateJWT($credentials, $tokenEndpoint),
        ];

        // Initialize cURL session
        $ch = curl_init();

        // Set cURL options for token request
        curl_setopt($ch, CURLOPT_URL, $tokenEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Execute cURL session
        $response_data = curl_exec($ch);
        
        // Show respons if need
        if(!empty($debug)) {
            echo '<pre>';
            print_r($response_data);
            echo '</pre>';
        }

        // Check for errors
        if (curl_errno($ch)) {
            $this->_error = 'Error occurred: ' . curl_error($ch);
        } else {
            // Close cURL session
            curl_close($ch);
            // Handle token response
            $this->_authData = json_decode($response_data, true);
            if (!empty($this->_authData['access_token'])) {
                //echo 'Access token obtained successfully: ' . $this->_authData['access_token'];
                return true;
            } else {
                $this->_error = 'Failed to obtain access token. Error: ' . $response_data;
            }
        }
        
        return false;
    }

    private function generateJWT($credentials, $tokenEndpoint) {
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $now = time();
        $payload = base64_encode(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/analytics.readonly',
            'aud' => $tokenEndpoint,
            'iat' => $now,
            'exp' => $now + 3600, // Token expires in 1 hour
        ]));
        $signature = $this->signData("$header.$payload", $credentials['private_key']);
        return "$header.$payload.$signature";
    }

    // Function to sign data using RSA private key
    private function signData($data, $privateKey) {
        $key = openssl_pkey_get_private($privateKey);
        openssl_sign($data, $signature, $key, OPENSSL_ALGO_SHA256);
        openssl_free_key($key);
        return base64_encode($signature);
    }
}
