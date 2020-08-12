<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PalestineIDNumber implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (strlen($value) !== 9) {
            return false;
        }
        $sum = 0;
        for ($i = 0; $i < 8; $i++) {
            $digit = $value[$i];
            $factor = $i % 2 == 0 ? 1 : 2;
            $result = (string) ($digit * $factor);
            if ($result > 9) {
                $sum += $result[0] + $result[1];
            } else {
                $sum += $result;
            }
        }
        $last_digit = substr($sum, -1);

        if (10 - $last_digit != $value[8]) {
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid ID Number.';
    }
}
