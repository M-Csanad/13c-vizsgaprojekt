<?php
include_once __DIR__.'/result.php';

/**
 * Captcha osztály
 * 
 * A Captcha osztály a reCAPTCHA ellenőrzés kezelésére szolgál.
 * A reCAPTCHA tokenek érvényességének ellenőrzését végzi a Google reCAPTCHA Enterprise API segítségével.
 */
class Captcha {
    // Hitelesítő adatok
    private string $recaptchaKey;
    private string $projectId;
    private string $siteKey;

    /**
     * A biztonsági kockázat küszöbértéke, amely alatt a token érvénytelennek minősül.
     */
    private const RISK_THRESHOLD = 0.5;
    
    /**
     * Captcha konstruktor.
     * 
     * Betölti a környezeti változókat és inicializálja a reCAPTCHA kulcsokat, projektazonosítót és a webhely kulcsot.
     */
    public function __construct() {
        load_env(__DIR__ . '/../.env');
        $this->recaptchaKey = $_ENV['RECAPTCHA_KEY'];
        $this->projectId = $_ENV['RECAPTCHA_PROJECT_ID'];
        $this->siteKey = $_ENV['RECAPTCHA_SITE_KEY'];
    }
    
    /**
     * A reCAPTCHA token ellenőrzése.
     * 
     * @param string $token A reCAPTCHA token, amelyet ellenőrizni kell.
     * @param string $action Az elvárt művelet neve (alapértelmezett: üres string).
     * @return Result Egy Result objektum, amely tartalmazza az ellenőrzés eredményét.
     */
    public function verify(string $token, string $action = ''): Result {
        if (empty($token)) {
            return new Result(Result::ERROR, "reCAPTCHA token hiányzik");
        }

        // Kérés URL formázása
        $url = sprintf(
            'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s',
            $this->projectId,
            $this->recaptchaKey
        );

        // Kérés adatai
        $data = [
            'event' => [
                'token' => $token,
                'siteKey' => $this->siteKey,
                'expectedAction' => $action
            ]
        ];

        // Kérés küldése
        // cURL használata a gyorsabb és megbízhatóbb kommunikációért
        $curl = curl_init($url);
        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
            CURLOPT_CONNECTTIMEOUT => 3,    // 3 másodperces kapcsolódási időkorlát
            CURLOPT_TIMEOUT => 5,           // 5 másodperces teljes időkorlát
            CURLOPT_SSL_VERIFYPEER => true  // SSL ellenőrzés megtartása
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Hibakezelés
        if ($response === false) {
            return new Result(Result::ERROR, "reCAPTCHA kapcsolódási hiba: " . $error);
        }

        if ($httpCode !== 200) {
            return new Result(Result::ERROR, "reCAPTCHA API hiba (HTTP $httpCode)");
        }

        $assessment = json_decode($response, true);

        if (!isset($assessment['tokenProperties']['valid']) || 
            !$assessment['tokenProperties']['valid'] || 
            !isset($assessment['riskAnalysis']['score']) || 
            $assessment['riskAnalysis']['score'] < self::RISK_THRESHOLD) {
            return new Result(Result::ERROR, "Biztonsági ellenőrzés sikertelen. Kérjük próbálja újra.");
        }

        return new Result(Result::SUCCESS, "Sikeres reCAPTCHA ellenőrzés");
    }
}
