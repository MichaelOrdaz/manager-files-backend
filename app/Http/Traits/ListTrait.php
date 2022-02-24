<?php

namespace App\Http\Traits;

trait ListTrait {
  protected function pagination($modelo) {
    return [
        'links' => [
            'first' => $modelo->url(1),
            'last' => $modelo->url($modelo->lastPage()),
            'prev' => $modelo->previousPageUrl(),
            'next' => $modelo->nextPageUrl(),
        ],
        'meta' =>
        [
            'current_page' => $modelo->currentPage(),
            'from' => $modelo->firstItem(),
            'last_page' => $modelo->lastPage(),
            'path' => $modelo->resolveCurrentPath(),
            'per_page' => $modelo->perPage(),
            'to' => $modelo->lastItem(),
            'total' => $modelo->total(),
        ],
    ];
}

}