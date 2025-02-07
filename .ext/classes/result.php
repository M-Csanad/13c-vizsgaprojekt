<?php
/**
 * Osztály egy művelet eredményének hatékony tárolására.
 */
class Result {

    // Publikus tulajdonságok
    public $message;
    public $type;
    public $typeAsString;

    public const SUCCESS = 1;
    public const ERROR = 2;
    public const EMPTY = 3;
    public const NO_AFFECT = 4;
    public const DENIED = 5;
    public const PROMPT = 6;

    // Rejtett tulajdonságok
    private const TYPES = [
        self::SUCCESS => "SUCCESS", 
        self::ERROR => "ERROR", 
        self::EMPTY => "EMPTY", 
        self::NO_AFFECT => "NO_AFFECT", 
        self::DENIED => "DENIED",
        self::PROMPT => "PROMPT"
    ];

    /**
     * Egy új Result példányt hoz létre.
     *
     * @param int $type A válasz típusa. Lehet: SUCCESS, ERROR, EMPTY, NO_AFFECT, vagy DENIED.
     * @param mixed $message Az eredmény üzenete. Ez bármilyen adat lehet.
     *
     * @return Result Az új példány.
     * @throws InvalidArgumentException Ha a megadott típus nem támogatott.
     */
    function __construct(int $type, mixed $message)
    {
        if (!self::isValidType($type)) {
            var_dump($type, $message);
            throw new InvalidArgumentException("Nem támogatott válasz típus!");
        }

        $this->type = $type;
        $this->typeAsString = self::TYPES[$this->type];
        $this->message = $message;
    }

    // Segédfüggvények
    // Konstruktor validálás
    private static function isValidType(int $type): bool {
        return !is_null($type) && !empty($type) && in_array($type, array_keys(self::TYPES), true);
    }


    /**
     * Ellenőrzi, hogy az eredmény sikertelen-e.
     * 
     * @return bool Igaz, ha sikertelen, hamis ha nem.
     */
    public function isError(): bool {
        return $this->type === self::ERROR;
    }
    
    /**
     * Ellenőrzi, hogy az eredmény üres-e.
     * 
     * @return bool Igaz, ha üres, hamis ha nem.
     */
    public function isEmpty(): bool {
        return $this->type === self::EMPTY;
    }
    
    /**
     * Ellenőrzi, hogy az eredmény sikeres-e.
     * 
     * @return bool Igaz, ha sikeres, hamis ha nem.
     */
    public function isSuccess(): bool {
        return $this->type === self::SUCCESS;
    }
    
    /**
     * Ellenőrzi, hogy az eredmény a paraméterben megadott típussal rendelkezik-e.
     * 
     * @return bool Igaz, ha a típusuk egyezik, hamis ha nem.
     */
    public function isOfType(int $type = null): bool {
        if (!self::isValidType($type)) {
            throw new InvalidArgumentException("Nem támogatott válasz típus az isTypeOf függvényben!");
        }
        
        return $this->type === $type;
    }

    /**
     * Egy Result példányt asszociációs tömbbé alakít.
     * 
     * @return array Az átalakított tömb.
     */
    public function toArray($full = false): array {
        return [
            "type" => self::TYPES[$this->type],
            "message" => $this->message,
        ];
    }

    /**
     * Egy Result példányt JSON stringgé alakít.
     * 
     * @return string Az átalakított tömb.
     */
    public function toJSON($full = false): string {
        return json_encode($this->toArray($full), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Egy Result példány üzenetét JSON stringgé alakítja.
     * 
     * @return string Az átalakított tömb.
     */
    public function messageJSON(): string {
        return json_encode($this->message, JSON_UNESCAPED_UNICODE);
    }

}