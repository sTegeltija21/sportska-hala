<?php

require_once __DIR__ . '/BazaPodataka.php';
require_once __DIR__ . '/Zahtev.php';

class Zahtevi extends BazaPodataka
{
    public function sacuvaj(array $podaci, Zahtev $zahtev): void
    {
        $this->veza->beginTransaction();

        try {
            $upit = $this->veza->prepare(
                'INSERT INTO zahtevi
                 (broj_zahteva, datum_podnosenja, podnosilac, kontakt,
                  datum_koriscenja, vreme_od, vreme_do, sport_id)
                 VALUES (?, CURRENT_DATE(), ?, ?, ?, ?, ?, ?)'
            );

            $upit->execute([
                $podaci['broj_zahteva'],
                $podaci['podnosilac'],
                $podaci['kontakt'],
                $podaci['datum_koriscenja'],
                $podaci['vreme_od'],
                $podaci['vreme_do'],
                $zahtev->sport()->id()
            ]);

            $zahtevId = (int) $this->veza->lastInsertId();

            $upitUcesnik = $this->veza->prepare(
                'INSERT INTO ucesnici (zahtev_id, ime_prezime) VALUES (?, ?)'
            );

            foreach ($zahtev->ucesnici() as $ucesnik) {
                $upitUcesnik->execute([
                    $zahtevId,
                    $ucesnik->imePrezime()
                ]);
            }

            $this->veza->commit();
        } catch (Throwable $greska) {
            $this->veza->rollBack();
            throw $greska;
        }
    }

    public function prikaziSve(string $pretraga = ''): array
    {
        $upit = $this->veza->prepare(
            'SELECT z.id, z.broj_zahteva, z.podnosilac,
                    z.datum_koriscenja, z.vreme_od, z.vreme_do,
                    z.status, s.naziv AS sport
             FROM zahtevi z
             JOIN sportovi s ON s.id = z.sport_id
             WHERE z.broj_zahteva LIKE :pretraga
                OR z.podnosilac LIKE :pretraga
             ORDER BY z.datum_koriscenja DESC, z.vreme_od DESC'
        );

        $upit->execute([
            'pretraga' => '%' . $pretraga . '%'
        ]);

        return $upit->fetchAll(PDO::FETCH_ASSOC);
    }

    public function prikaziJedan(int $id): ?array
    {
        $upit = $this->veza->prepare(
            'SELECT z.*, s.naziv AS sport
             FROM zahtevi z
             JOIN sportovi s ON s.id = z.sport_id
             WHERE z.id = ?'
        );

        $upit->execute([$id]);
        $zahtev = $upit->fetch(PDO::FETCH_ASSOC);

        return $zahtev ?: null;
    }

    public function prikaziUcesnike(int $zahtevId): array
    {
        $upit = $this->veza->prepare(
            'SELECT id, ime_prezime
             FROM ucesnici
             WHERE zahtev_id = ?
             ORDER BY id'
        );

        $upit->execute([$zahtevId]);

        return $upit->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obrisi(int $id): bool
    {
        $upit = $this->veza->prepare(
            'DELETE FROM zahtevi WHERE id = ?'
        );

        $upit->execute([$id]);

        return $upit->rowCount() === 1;
    }

    public function izmeni(int $id, array $podaci, Zahtev $zahtev): void
    {
        $this->veza->beginTransaction();

        try {
            $provera = $this->veza->prepare(
                'SELECT id FROM zahtevi WHERE id = ? FOR UPDATE'
            );
            $provera->execute([$id]);

            if (!$provera->fetch()) {
                throw new RuntimeException('Zahtev nije pronađen.');
            }

            if ($podaci['status'] === 'odobren') {
                $preklapanje = $this->veza->prepare(
                    'SELECT id
                     FROM zahtevi
                     WHERE datum_koriscenja = ?
                       AND status = ?
                       AND vreme_od < ?
                       AND vreme_do > ?
                       AND id <> ?
                     LIMIT 1
                     FOR UPDATE'
                );

                $preklapanje->execute([
                    $podaci['datum_koriscenja'],
                    'odobren',
                    $podaci['vreme_do'],
                    $podaci['vreme_od'],
                    $id
                ]);

                if ($preklapanje->fetch()) {
                    throw new RuntimeException(
                        'Termin se preklapa sa već odobrenim zahtevom.'
                    );
                }
            }

            $upit = $this->veza->prepare(
                'UPDATE zahtevi
                 SET broj_zahteva = ?, podnosilac = ?, kontakt = ?,
                     datum_koriscenja = ?, vreme_od = ?, vreme_do = ?,
                     sport_id = ?, status = ?
                 WHERE id = ?'
            );

            $upit->execute([
                $podaci['broj_zahteva'],
                $podaci['podnosilac'],
                $podaci['kontakt'],
                $podaci['datum_koriscenja'],
                $podaci['vreme_od'],
                $podaci['vreme_do'],
                $zahtev->sport()->id(),
                $podaci['status'],
                $id
            ]);

            $brisanje = $this->veza->prepare(
                'DELETE FROM ucesnici WHERE zahtev_id = ?'
            );
            $brisanje->execute([$id]);

            $upisUcesnika = $this->veza->prepare(
                'INSERT INTO ucesnici (zahtev_id, ime_prezime) VALUES (?, ?)'
            );

            foreach ($zahtev->ucesnici() as $ucesnik) {
                $upisUcesnika->execute([
                    $id,
                    $ucesnik->imePrezime()
                ]);
            }

            $this->veza->commit();
        } catch (Throwable $greska) {
            $this->veza->rollBack();
            throw $greska;
        }
    }
}