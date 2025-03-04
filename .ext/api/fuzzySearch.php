<?php


/**
 * Teljes Damerau–Levenshtein távolság számítása
 * (Lowrance & Wagner módszer alapján, a transzpozíciókat is kezeli)
 */
function damerauLevenshtein($s, $t)
{
    $lenS = strlen($s);
    $lenT = strlen($t);
    $maxdist = $lenS + $lenT;

    // Mátrix létrehozása
    $d = array();
    for ($i = 0; $i < $lenS + 2; $i++) {
        $d[$i] = array_fill(0, $lenT + 2, 0);
    }
    $d[0][0] = $maxdist;
    for ($i = 0; $i <= $lenS; $i++) {
        $d[$i + 1][1] = $i;
        $d[$i + 1][0] = $maxdist;
    }
    for ($j = 0; $j <= $lenT; $j++) {
        $d[1][$j + 1] = $j;
        $d[0][$j + 1] = $maxdist;
    }

    // Utolsó előfordulások
    $da = array();
    $allChars = count_chars($s . $t, 3);
    for ($i = 0; $i < strlen($allChars); $i++) {
        $da[$allChars[$i]] = 0;
    }

    for ($i = 1; $i <= $lenS; $i++) {
        $db = 0;
        for ($j = 1; $j <= $lenT; $j++) {
            $i1 = isset($da[$t[$j - 1]]) ? $da[$t[$j - 1]] : 0;
            $j1 = $db;
            if ($s[$i - 1] == $t[$j - 1]) {
                $cost = 0;
                $db = $j;
            } else {
                $cost = 1;
            }
            $d[$i + 1][$j + 1] = min(
                $d[$i][$j] + $cost,                       // helyettesítés
                $d[$i + 1][$j] + 1,                       // beszúrás
                $d[$i][$j + 1] + 1,                       // törlés
                $d[$i1][$j1] + ($i - $i1 - 1) + 1 + ($j - $j1 - 1) // transzpozíció
            );
        }
        $da[$s[$i - 1]] = $i;
    }

    return $d[$lenS + 1][$lenT + 1];
}

/**
 * Részleges illeszkedésen alapuló minimális távolság (single word ellen).
 *
 * 1) Megnézzük a Damerau–Levenshtein távolságot a teljes candidateWord és a query között.
 * 2) Végigmegyünk a candidateWord minden releváns substringjén, és kiválasztjuk a minimális DL-távolságot.
 */
function partialSubstrDistance($query, $candidateWord)
{
    $Lq = strlen($query);
    $Lc = strlen($candidateWord);

    // Először sima DL a két teljes szó között
    $minDist = damerauLevenshtein($query, $candidateWord);

    if ($Lq > $Lc) {
        // Ha a query hosszabb, nincs értelme substringeket vizsgálni
        return $minDist;
    }

    // substring hossza: ±1 a query hosszától
    $minLen = max(1, $Lq - 1);
    $maxLen = $Lq + 1;

    for ($start = 0; $start < $Lc; $start++) {
        for ($length = $minLen; $length <= $maxLen; $length++) {
            if ($start + $length > $Lc) {
                break;
            }
            $substr = substr($candidateWord, $start, $length);
            $dist = damerauLevenshtein($query, $substr);
            if ($dist < $minDist) {
                $minDist = $dist;
            }
        }
    }
    return $minDist;
}

/**
 * Fuzzy távolság számítása egy teljes candidate-re.
 * Ha a query több szavas, megnézzük, hogy a candidate tartalmazza-e substringként (case-insensitive).
 * Ha a query egy szavas, de a candidate több szóból áll, megnézzük a partialSubstrDistance minden egyes szóra.
 * Egyébként partialSubstrDistance a teljes candidate-re.
 */
function fuzzyDistance($query, $candidate)
{
    // Többszavas query
    if (strpos($query, ' ') !== false) {
        // Ha tartalmazza is (case-insensitive), 0
        if (stripos($candidate, $query) !== false) {
            return 0;
        }
        // Különben sima DL
        return damerauLevenshtein($query, $candidate);
    }

    // Egy szavas query
    if (strpos($candidate, ' ') !== false) {
        // Több szóból áll a candidate
        $words = preg_split('/\s+/', $candidate);
        $minDist = PHP_INT_MAX;
        foreach ($words as $w) {
            $dist = partialSubstrDistance($query, $w);
            if ($dist < $minDist) {
                $minDist = $dist;
            }
        }
        return $minDist;
    } else {
        // Egy szavas candidate
        return partialSubstrDistance($query, $candidate);
    }
}

/**
 * Egyszerű "lineáris" fuzzy kereső osztály.
 * Minden betöltött szóval kiszámoljuk a fuzzyDistance-t, aztán threshold alapján szűrünk.
 */
class LinearFuzzySearch
{
    private $words = array();

    public function __construct($words = array())
    {
        $this->words = $words;
    }

    public function addWord($word)
    {
        $this->words[] = $word;
    }

    /**
     * A keresés: megnézzük minden szóval a fuzzyDistance-t,
     * threshold = pl. a query hossza vagy valami finomhangolt érték,
     * és visszaadjuk azokat, amik beleférnek.
     *
     * Ha gondolod, utána még sorbarendezheted distance szerint.
     */
    public function search($query)
    {
        $results = array();
        if (!strlen($query)) {
            return $results;
        }

        // Tetszés szerint állíthatod.
        // Például: threshold = a query hossza,
        $threshold = max(2, floor(strlen($query)*0.4));
        // $threshold = sqrt(strlen($query));

        foreach ($this->words as $w) {
            $d = fuzzyDistance($query, $w);
            if ($d <= $threshold) {
                $results[] = array('word' => $w, 'distance' => $d);
            }
        }

        // Opcionálisan rendezzük távolság szerint
        usort($results, function ($a, $b) {
            return $a['distance'] - $b['distance'];
        });

        return $results;
    }
}
