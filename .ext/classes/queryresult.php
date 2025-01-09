<?php
class QueryResult {
    public $message;
    public $type;
    public $code;
    public $contentType;

    private $types = ["SUCCESS", "ERROR", "EMPTY", "NO_AFFECT", "DENIED"];

    function __construct($type, $message, $code = null, $contentType = null)
    {
        if (!in_array($type, $this->types)) {
            throw new InvalidArgumentException("Nem támogatott válasz típus!");
        }

        $this->type = $type;
        $this->message = $message;
        $this->code = $code;
        $this->contentType = $contentType;
    }

    public function isError() {
        return $this->type === "ERROR";
    }

    public function isEmpty() {
        return $this->type === "EMPTY";
    }

    public function isSuccess() {
        return $this->type === "SUCCESS";
    }

    public function isTypeOf($type = null) {
        if (!in_array($type, $this->types)) {
            throw new InvalidArgumentException("Nem támogatott válasz típus az isTypeOf függvényben!");
        }

        return $this->type === $type;
    }
}