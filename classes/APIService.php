<?php
class APIService
{
    private string $baseUrl = 'https://api.example-currency.com/latest';

    public function fetchExternalData(string $endpoint): array|bool
    {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $output) {
            return json_decode($output, true);
        }
        return false;
    }

    public function getUSDExchangeRate(): float|bool
    {
        $data = $this->fetchExternalData('?base=CAD');

        if ($data && isset($data['rates']['USD'])) {
            return (float)$data['rates']['USD'];
        }
        return false;
    }
}
