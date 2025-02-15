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
    public $params;

    public const REGULAR = 0;
    public const PREPARED = 1;

    // Rejtett tulajdonságok
    private const QUERY_TYPES = [
        self::REGULAR => "REGULAR (non-prepared)",
        self::PREPARED => "PREPARED",
    ];

    /**
     * Egy új QueryResult példányt hoz létre.
     *
     * @param int $type A válasz típusa. Lehet: SUCCESS, ERROR, EMPTY, NO_AFFECT, vagy DENIED.
     * @param mixed $message Az eredmény üzenete. Ez bármilyen adat lehet.
     * @param string $query A lekérdezés SQL kódja, ez lehet előkészített, vagy normál.
     * @param mixed $params Az előkészített lekérdezések paramétere(i).
     * @param ?int $code A hiba kódja (SQL hiba).
     * @param ?int $affectedRows A művelet által érintett sorok száma.
     * @param ?int $lastInsertId A legutóbb beillesztett sor AUTO-INCREMENT értéke.
     *
     * @return QueryResult Az újonnan létrehozott példány.
     * @throws InvalidArgumentException Ha a megadott típus nem támogatott.
     */
    public function __construct(int $type, mixed $message, string $query = null, mixed $params = null, ?int $code = null, ?int $affectedRows = null, ?int $lastInsertId = null) {
        parent::__construct($type, $message);

        $this->code = $code;
        $this->affectedRows = $affectedRows;
        $this->lastInsertId = $lastInsertId;

        // Az előkészített lekérdezésbe beillesztjük a paramétereket
        // (Debug funkció)
        if (!is_null($params)) {
            $this->queryType = self::PREPARED;
            $this->params = $params;

            if (!is_array($params)) {
                $params = [$params];
            }

            // A kérdőjelek behelyettesítése a paraméterekkel
            $offset = 0;
            foreach ($params as $param) {
                $placeholderPos = strpos($query, "?", $offset);

                // Ha nincs több kérdőjel, akkor kilépünk a ciklusból.
                if ($placeholderPos === false) {
                    break;
                }

                // Kicseréljük a kérdőjelet és növeljük az eltolást.
                $query = substr_replace($query, strval($param), $placeholderPos, 1);
                $offset = $placeholderPos + mb_strlen(strval($param));
            }
        }

        $this->query = $query;
    }

    // Tömbbé konvertálás felülírása
    public function toArray($full = false): array {
        $base = parent::toArray();
        if (!$full) return $base;

        $base['rowsAffected'] = $this->affectedRows;
        $base['lastInsertId'] = $this->lastInsertId;
        $base['query'] = $this->query;
        $base['queryType'] = self::QUERY_TYPES[$this->queryType];
        $base['params'] = $this->params;

        return $base;
    }
}
