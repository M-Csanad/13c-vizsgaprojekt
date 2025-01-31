<?php
class InputValidator {
    public $inputs;
    public $rules;

    public function __construct($inputs, $rules) {
        $this->inputs = $inputs; 
        $this->rules = $rules;
    }

    public function test(): bool {
        foreach ($this->inputs as $field => $value) {
            if (isset($this->rules[$field])) {
                $rule = $this->rules[$field];

                if (is_string($rule)) {
                    if (!preg_match($rule, $value)) {
                        return false;
                    }
                } elseif (is_callable($rule)) {
                    if (!$rule($value)) {
                        return false;
                    }
                } else {
                    throw new InvalidArgumentException("Érvénytelen ellenőrzési típus: " . $field);
                }
            }
        }

        return true;
    }
}