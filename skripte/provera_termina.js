document.querySelector('form[action="sacuvaj_zahtev.php"], form[action="sacuvaj_izmene.php"]')
    ?.addEventListener('submit', function (dogadjaj) {
        const vremeOd = this.elements['vreme_od'].value;
        const vremeDo = this.elements['vreme_do'].value;
        const ucesnici = [...this.querySelectorAll('input[name="ucesnici[]"]')];

        if (vremeOd >= vremeDo) {
            dogadjaj.preventDefault();
            alert('Vreme završetka mora biti posle vremena početka.');
            return;
        }

        if (ucesnici.length === 0) {
            dogadjaj.preventDefault();
            alert('Dodaj bar jednog učesnika.');
            return;
        }

        if (ucesnici.some(polje => polje.value.trim() === '')) {
            dogadjaj.preventDefault();
            alert('Unesi ime i prezime svakog učesnika.');
        }
    });