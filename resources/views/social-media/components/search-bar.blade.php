<style>
    .search-section {
        max-width: 900px;
        width: 100%;
        margin-bottom: 20px;
    }

    .search-section form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .search-bar {
        display: flex;
        width: 100%;
        gap: 10px;
    }

    #donation-type-filter {
        max-width: 900px;
        width: 100%;
        margin-bottom: 20px;
        display: flex;
        gap: 10px;
        align-items: center;
    }

    #donation-type-filter label {
        width: fit-content;
        white-space: nowrap;
        margin: 0 !important;
    }
</style>

<div class="search-section">
    <form action="{{ $searchUrl }}">
        <div class="search-bar">
            <input type="search" id="search-bar" class="form-control" name="search" placeholder="Cari" value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Cari</button>
        </div>

        @if (isset($type) && $type == 'donation')
            <div id="donation-type-filter">
                <label>Jenis Derma</label>
                <select name="donation_type" id="donation-type" class="form-control">
                    <option value="">Semua Derma</option>
                    <option value="8" {{ $donationType == 8 ? 'selected' : '' }}>Derma Khas</option>
                    <option value="3" {{ $donationType == 3 ? 'selected' : '' }}>IPTA / Universiti</option>
                    <option value="2" {{ $donationType == 2 ? 'selected' : '' }}>Masjid/Surau Baru</option>
                    <option value="1" {{ $donationType == 1 ? 'selected' : '' }}>PIBG Sekolah</option>
                    <option value="4" {{ $donationType == 4 ? 'selected' : '' }}>Pusat Tahfiz</option>
                    <option value="5" {{ $donationType == 5 ? 'selected' : '' }}>Imarah Masjid</option>
                    <option value="6" {{ $donationType == 6 ? 'selected' : '' }}>Sedekah Subuh</option>
                    <option value="7" {{ $donationType == 7 ? 'selected' : '' }}>NGO</option>
                </select>
            </div>
        @endif
    </form>
</div>