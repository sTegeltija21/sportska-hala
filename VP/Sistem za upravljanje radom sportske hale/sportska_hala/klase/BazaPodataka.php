<?php

class BazaPodataka
{
    protected PDO $veza;

    public function __construct()
    {
        $this->veza = new PDO(
            'mysql:host=localhost;dbname=sportska_hala;charset=utf8mb4',
            'root',
            ''
        );

        $this->veza->setAttribute(
            PDO::ATTR_ERRMODE,
            PDO::ERRMODE_EXCEPTION
        );
    }

    protected function izvrsiUpit(
        string $sql,
        array $parametri = []
    ): PDOStatement {
        $upit = $this->veza->prepare($sql);
        $upit->execute($parametri);

        return $upit;
    }

    protected function procitajRedove(
        string $sql,
        array $parametri = []
    ): array {
        $upit = $this->izvrsiUpit($sql, $parametri);
        $redovi = $upit->fetchAll(PDO::FETCH_ASSOC);
        $upit->closeCursor();

        return $redovi;
    }

    protected function pozoviProceduru(
        string $naziv,
        array $parametri = []
    ): array {
        if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $naziv)) {
    throw new InvalidArgumentException(
        'Naziv stored procedure nije ispravan.'
    );
}

        $mesta = implode(
            ', ',
            array_fill(0, count($parametri), '?')
        );

        $upit = $this->izvrsiUpit(
            "CALL {$naziv}({$mesta})",
            $parametri
        );

        $redovi = $upit->fetchAll(PDO::FETCH_ASSOC);
        $upit->closeCursor();

        return $redovi;
    }
}