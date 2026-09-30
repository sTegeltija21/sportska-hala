<?php

require_once __DIR__ . '/BazaPodataka.php';

class Izvestaji extends BazaPodataka
{
    public function dnevni(string $datum): array
    {
        return $this->pozoviProceduru(
            'dnevni_izvestaj',
            [$datum]
        );
    }

    public function rezimeZaDan(string $datum): array
    {
        $redovi = $this->procitajRedove(
            'SELECT broj_termina, rezervisano_minuta, ukupno_ucesnika
             FROM dnevna_zauzetost
             WHERE datum_koriscenja = ?',
            [$datum]
        );

        return $redovi[0] ?? [
            'broj_termina' => 0,
            'rezervisano_minuta' => 0,
            'ukupno_ucesnika' => 0
        ];
    }
}