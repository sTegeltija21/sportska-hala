<?php

class Ucesnik
{
    private string $imePrezime;

    public function __construct(string $imePrezime)
    {
        $imePrezime = trim($imePrezime);

        if ($imePrezime === '' || mb_strlen($imePrezime) > 100) {
            throw new InvalidArgumentException(
                'Ime i prezime učesnika mora imati od 1 do 100 znakova.'
            );
        }

        $this->imePrezime = $imePrezime;
    }

    public function imePrezime(): string
    {
        return $this->imePrezime;
    }
}