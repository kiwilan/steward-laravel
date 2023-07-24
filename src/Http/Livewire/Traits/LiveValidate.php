<?php

namespace Kiwilan\Steward\Http\Livewire\Traits;

use Closure;
use Illuminate\Validation\Validator;

/**
 * `Livewire\Component` trait to validate form.
 */
trait LiveValidate
{
    use LiveNotify;

    public function validate(Closure $callback = null): array
    {
        return $this->withValidator(
            fn (Validator $validator) => $validator->after(
                fn (Validator $validator) => $this->validating($validator, $callback)
            )
        )->validate();
    }

    private function validating(Validator $validator, ?Closure $callback): void
    {
        if ($validator->errors()->any()) {
            $messages = [];

            foreach ($validator->errors()->getMessages() as $key => $value) {
                $messages[] = $value[0];
            }
            $messages = implode(' ', $messages);

            if ($callback) {
                $callback($messages);
            } else {
                $this->notify($messages)
                    ->error()
                    ->send()
                ;
            }
        }
    }
}
