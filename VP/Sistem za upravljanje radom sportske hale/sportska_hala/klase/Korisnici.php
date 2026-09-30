<?php

require_once __DIR__ . '/BazaPodataka.php';

class Korisnici extends BazaPodataka
{
    public function postojiKorisnik(): bool
    {
        $upit = $this->veza->query(
            'SELECT COUNT(*) FROM korisnici'
        );

        return (int) $upit->fetchColumn() > 0;
    }

    public function napraviPrvog(
        string $korisnickoIme,
        string $lozinka
    ): void {
        if ($this->postojiKorisnik()) {
            throw new RuntimeException('Prvi nalog je već napravljen.');
        }

        $upit = $this->veza->prepare(
            'INSERT INTO korisnici (korisnicko_ime, lozinka_hash)
             VALUES (?, ?)'
        );

        $upit->execute([
            $korisnickoIme,
            password_hash($lozinka, PASSWORD_DEFAULT)
        ]);
    }

    public function pronadjiPoImenu(string $korisnickoIme): ?array
    {
        $upit = $this->veza->prepare(
            'SELECT id, korisnicko_ime, lozinka_hash
             FROM korisnici
             WHERE korisnicko_ime = ?'
        );

        $upit->execute([$korisnickoIme]);
        $korisnik = $upit->fetch(PDO::FETCH_ASSOC);

        return $korisnik ?: null;
    }
}