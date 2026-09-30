<?php

class Sport
{
    private int $id;
    private string $naziv;

    public function __construct(int $id, string $naziv)
    {
        if ($id < 1 || trim($naziv) === '') {
            throw new InvalidArgumentException('Podaci o sportu nisu ispravni.');
        }

        $this->id = $id;
        $this->naziv = trim($naziv);
    }

    public function id(): int
    {
        return $this->id;
    }

    public function naziv(): string
    {
        return $this->naziv;
    }
}