<?php

namespace App\Traits;

use App\Traits\Camelize;
use Cake\Http\Exception\InternalErrorException;

trait Hydrate
{
    use Camelize;

    public function hydrate(string $field)
    {
        if (!property_exists($this, $field))
            throw new InternalErrorException("Field $field does not exist");

        $method = 'retrieve' . $this->snakeCaseToCamelCase($field);
        if (!method_exists($this, $method))
            throw new InternalErrorException("Method $method does not exist");

        $this->{$field} = $this->{$method}();
    }
}
