<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Illuminate\Validation\Validator;

trait Validable
{
    public function validator(?\Closure $callback = null): array
    {
        return $this->withValidator(function (Validator $validator) use ($callback) {
            $validator->after(function (Validator $validator) use ($callback) {
                if ($validator->errors()->any()) {
                    $messages = [];

                    foreach ($validator->errors()->getMessages() as $key => $value) {
                        $messages[] = $value[0];
                    }
                    $messages = implode(' ', $messages);

                    if ($callback) {
                        $callback($messages);
                    } else {
                        $this->notify(
                            success: false,
                            message: $messages,
                        );
                    }
                }
            });
        })->validate();
    }
}
