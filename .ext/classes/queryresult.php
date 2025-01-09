<?php
include_once "result.php";

/**
 * Osztály egy SQL művelet eredményének hatékony tárolására.
 */
class QueryResult extends Result {

    // Nyilvános tulajdonságok
    public $code;
    public $affectedRows;
    public $lastInsertId;
    public $query;
    public $queryType = self::REGULAR;

    public const REGULAR = 0;
    public const PREPARED = 1;

    // Privát tulajdonságok
    private const QUERY_TYPES = [
        self::REGULAR => "REGULAR (non-prepared)",
        self::PREPARED => "PREPARED",
    ];

    public function __construct(int $type, mixed $message, string $query = null, mixed $params = null, ?int $code = null, int $affectedRows = 0, ?int $lastInsertId = null) {
        parent::__construct($type, $message);

        $this->code = $code;
        $this->affectedRows = $affectedRows;
        $this->lastInsertId = $lastInsertId;

        if (!is_null($params)) {
            $this->queryType = self::PREPARED;

            if (!is_array($params)) {
                $params = [$params];
            }

            $offset = 0;
            foreach ($params as $param) {
                $placeholderPos = strpos($query, "?", $offset);

                if ($placeholderPos === false) {
                    break;
                }

                $query = substr_replace($query, strval($param), $placeholderPos, 1);
                $offset += $placeholderPos + mb_strlen(strval($param));
            }
        }

        $this->query = $query;
    }

    // Tömbbé konvertálás
    public function toArray(): array {
        $base = parent::toArray();

        $base['rowsAffected'] = $this->affectedRows;
        $base['lastInsertId'] = $this->lastInsertId;
        $base['query'] = $this->query;
        $base['queryType'] = self::QUERY_TYPES[$this->queryType];

        return $base;
    }
}
