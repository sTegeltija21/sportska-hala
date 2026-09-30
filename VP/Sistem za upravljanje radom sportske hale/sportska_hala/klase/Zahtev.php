<?php

require_once __DIR__ . '/Sport.php';
require_once __DIR__ . '/Ucesnik.php';

class Zahtev
{
    private Sport $sport;

    /** @var Ucesnik[] */
    private array $ucesnici = [];

    public function __construct(Sport $sport)
    {
        $this->sport = $sport;
    }

    public function dodajUcesnika(Ucesnik $ucesnik): void
    {
        $this->ucesnici[] = $ucesnik;
    }

    public function sport(): Sport
    {
        return $this->sport;
    }

    /** @return Ucesnik[] */
    public function ucesnici(): array
    {
        return $this->ucesnici;
    }
}