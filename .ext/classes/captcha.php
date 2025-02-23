<?php
include_once __DIR__.'/result.php';

class Captcha {
    private string $recaptchaKey;
    private string $projectId;
    private string $siteKey;
    
    public function __construct() {
        load_env(__DIR__ . '/../.env');
        $this->recaptchaKey = $_ENV['RECAPTCHA_KEY'];
        $this->projectId = $_ENV['RECAPTCHA_PROJECT_ID'];
        $this->siteKey = $_ENV['RECAPTCHA_SITE_KEY'];
    }
    
    public function verify(string $token, string $action = ''): Result {
        if (empty($token)) {
            return new Result(Result::ERROR, "reCAPTCHA token hiányzik");
        }

        $url = sprintf(
            'https://recaptchaenterprise.googleapis.com/v1/projects/%s/assessments?key=%s',
            $this->projectId,
            $this->recaptchaKey
        );

        $data = [
            'event' => [
                'token' => $token,
                'siteKey' => $this->siteKey,
                'expectedAction' => $action
            ]
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\n",
                'method' => 'POST',
                'content' => json_encode($data)
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === FALSE) {
            return new Result(Result::ERROR, "reCAPTCHA ellenőrzési hiba");
        }

        $assessment = json_decode($response, true);

        if (!isset($assessment['tokenProperties']['valid']) || 
            !$assessment['tokenProperties']['valid'] || 
            !isset($assessment['riskAnalysis']['score']) || 
            $assessment['riskAnalysis']['score'] < 0.5) {
            return new Result(Result::ERROR, "Biztonsági ellenőrzés sikertelen. Kérjük próbálja újra.");
        }

        return new Result(Result::SUCCESS, "Sikeres reCAPTCHA ellenőrzés");
    }
}
