<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class IsValidIdentification implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string $message, ?string $attribute = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $digits = preg_replace('/[^\d]+/', '', $value);

        switch (\strlen($digits)) {
            case 11:
                if (! $this->isValidCPF($digits)) {
                    $fail('The :attribute is not a valid CPF.');
                }
                break;
            case 14:
                if (! $this->isValidCNPJ($digits)) {
                    $fail('The :attribute is not a valid CNPJ.');
                }
                break;
            case 9:
                if (! $this->isValidSSN($digits)) {
                    $fail('The :attribute is not a valid SSN.');
                }
                break;
            default:
                $fail('The :attribute must be a valid CPF, CNPJ, or SSN.');
        }
    }

    public function isValidCNPJ(string $cnpj): bool
    {
        $cnpj = (string) $cnpj;

        $rawCnpj = $cnpj;

        $firstCnpjDigits = substr($cnpj, 0, 12);

        $firstCalc = self::multiplyCnpj($firstCnpjDigits);

        $firstDigit = $firstCalc % 11 < 2 ? 0 : 11 - $firstCalc % 11;

        $firstCnpjDigits .= $firstDigit;

        $secondCalc = self::multiplyCnpj($firstCnpjDigits, 6);

        $segundo_digito = $secondCalc % 11 < 2 ? 0 : 11 - $secondCalc % 11;

        $cnpj = "{$firstCnpjDigits}{$segundo_digito}";

        return $cnpj === $rawCnpj;
    }

    public function isValidCPF(string $cpf): bool
    {
        $digits = substr($cpf, 0, 9);

        $newCpf = self::digitPositionsCalc($digits);

        $newCpf = self::digitPositionsCalc($newCpf, 11);

        return $newCpf === $cpf;
    }

    public function isValidSSN(string $ssn): bool
    {
        return (bool) preg_match('/^(?!000|666|9\d{2})\d{3}(?!00)\d{2}(?!0000)\d{4}$/', (string) $ssn);
    }

    private static function multiplyCnpj(string $cnpj, int $position = 5): int
    {
        $calc = 0;

        for ($i = 0; $i < \strlen($cnpj); $i++) {
            $calc += $cnpj[$i] * $position;

            $position--;

            if ($position < 2) {
                $position = 9;
            }
        }

        return $calc;
    }

    private static function digitPositionsCalc(string $digits, int $positions = 10, int $digitSum = 0): string
    {
        for ($i = 0; $i < \strlen($digits); $i++) {
            $digitSum += $digits[$i] * $positions;
            $positions--;
        }

        $digitSum %= 11;

        $digitSum = ($digitSum < 2) ? 0 : 11 - $digitSum;

        return "{$digits}{$digitSum}";
    }
}
