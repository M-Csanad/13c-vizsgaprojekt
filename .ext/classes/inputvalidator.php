<?php
include_once __DIR__.'/result.php';

/**
 * InputValidator osztály
 * 
 * Az InputValidator osztály felelős a bemeneti adatok ellenőrzéséért a megadott szabályok alapján.
 * A bemeneteket reguláris kifejezések vagy megadott függvények segítségével ellenőrzi.
 */
class InputValidator {
    public $inputs;
    public $rules;

    /**
     * InputValidator konstruktor.
     * 
     * @param array $inputs Egy asszociatív tömb a validálandó bemeneti adatokkal.
     * @param array $rules Egy asszociatív tömb a validálási szabályokkal. Minden szabály lehet reguláris kifejezés vagy függvény.
     */
    public function __construct(array $inputs, array $rules) {
        $this->inputs = $inputs; 
        $this->rules = $rules;
    }

    /**
     * A bemenetek validálása a megadott szabályok alapján.
     * 
     * @return Result Egy Result objektum, amely tartalmazza a validálás eredményét.
     * @throws InvalidArgumentException Ha érvénytelen ellenőrzési típus található.
     */
    public function test(): Result {
        $hasAnyMatcher = array_key_exists("*", $this->rules);
        foreach ($this->inputs as $field => $value) {
            if (isset($this->rules[$field]) || $hasAnyMatcher) {
                $rule = isset($this->rules[$field]) ? $this->rules[$field]["rule"] : $this->rules["*"]["rule"];
                $message = isset($this->rules[$field]) ? $this->rules[$field]["message"] : $this->rules["*"]["message"];

                if (is_string($rule)) {
                    if (!preg_match($rule, $value)) {
                        return new Result(Result::ERROR, $message);
                    }
                } elseif (is_callable($rule)) {
                    if (!$rule($value)) {
                        return new Result(Result::ERROR, $message);
                    }
                } else {
                    throw new InvalidArgumentException("Érvénytelen ellenőrzési típus: " . $field);
                }
            }
        }

        return new Result(Result::SUCCESS, "Minden mező érvényes.");
    }
}
