<?php

require_once __DIR__ . '/BazaPodataka.php';
require_once __DIR__ . '/Sport.php';

class Sportovi extends BazaPodataka
{
    public function prikaziSve(): array
    {
        $upit = $this->veza->query(
            'SELECT id, naziv FROM sportovi ORDER BY naziv'
        );

        return $upit->fetchAll(PDO::FETCH_ASSOC);
    }

    public function pronadji(int $id): ?Sport
    {
        $upit = $this->veza->prepare(
            'SELECT id, naziv FROM sportovi WHERE id = ?'
        );

        $upit->execute([$id]);
        $podaci = $upit->fetch(PDO::FETCH_ASSOC);

        if (!$podaci) {
            return null;
        }

        return new Sport(
            (int) $podaci['id'],
            $podaci['naziv']
        );
    }
}