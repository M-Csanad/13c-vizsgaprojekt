<?php
/**
 * Osztály egy művelet eredményének hatékony tárolására.
 */
class Result {

    // Publikus tulajdonságok
    public $message;
    public $type;

    public const SUCCESS = 1;
    public const ERROR = 2;
    public const EMPTY = 3;
    public const NO_AFFECT = 4;
    public const DENIED = 5;

    // Rejtett tulajdonságok
    private const TYPES = [
        self::SUCCESS => "SUCCESS", 
        self::ERROR => "ERROR", 
        self::EMPTY => "EMPTY", 
        self::NO_AFFECT => "NO_AFFECT", 
        self::DENIED => "DENIED"
    ];

    /**
     * Egy új Result példányt hoz létre.
     *
     * @param int $type A válasz típusa. Lehet: SUCCESS, ERROR, EMPTY, NO_AFFECT, vagy DENIED.
     * @param mixed $message Az eredmény üzenete. Ez bármilyen adat lehet.
     *
     * @throws InvalidArgumentException Ha a megadott típus nem támogatott.
     */
    function __construct(int $type, mixed $message)
    {
        if (!self::isValidType($type)) {
            var_dump($type, $message);
            throw new InvalidArgumentException("Nem támogatott válasz típus!");
        }

        $this->type = $type;
        $this->message = $message;
    }

    // Segédfüggvények
    // Konstruktor validálás
    private static function isValidType(int $type): bool {
        return !is_null($type) && !empty($type) && in_array($type, array_keys(self::TYPES), true);
    }

    // Használati függvények

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

    // Tömbbé konvertálás
    public function toArray(): array {
        return [
            "type" => self::TYPES[$this->type],
            "message" => $this->message,
        ];
    }

    // JSON-re konvertálás
    public function toJSON(): string {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }

}