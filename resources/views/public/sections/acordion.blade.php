{{--
    Template Section: Dropdown (acordion)
    =========================================================
    Satu tombol dropdown bergaya accordion:
    klik heading (trigger) untuk membuka/menutup panel konten.

    Ganti 'heading' dengan key field heading kamu.
    Tambah/hapus @field() sesuai kebutuhan.
    =========================================================
--}}
<section>
    <div x-data="{ open: false }">
        <button @click="open = !open">
            @field('heading')
        </button>
        <div x-show="open" x-collapse>
            @field('body')
            @field('content')
        </div>
    </div>
</section>
