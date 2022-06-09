<?php

namespace App\Traits;

trait Camelize {
    public function snakeCaseToCamelCase(string $field) {
        return str_replace('_', '', ucwords($field, '_'));
    }
}