<?php
include_once __DIR__.'/result.php';
class InputValidator {
    public $inputs;
    public $rules;

    public function __construct($inputs, $rules) {
        $this->inputs = $inputs; 
        $this->rules = $rules;
    }

    public function test(): Result {
        foreach ($this->inputs as $field => $value) {
            if (isset($this->rules[$field])) {
                $rule = $this->rules[$field]["rule"];
                $message = $this->rules[$field]["message"];

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