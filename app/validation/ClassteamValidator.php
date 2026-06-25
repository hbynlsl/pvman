<?php
declare(strict_types=1);

namespace app\validation;

use support\validation\Validator;

class ClassteamValidator extends Validator
{
    protected array $rules = [
        'id' => 'nullable|integer',
        'name' => 'required|string',
        'semester' => 'required|string',
    ];

    protected array $messages = [];

    protected array $attributes = [];

    protected array $scenes = [
        'create' => [
            'name',
            'semester',
        ],
        'update' => [
            'id',
            'name',
            'semester',
        ],
        'delete' => [
            'id',
        ],
        'detail' => [
            'id',
        ],
    ];
}
