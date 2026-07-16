<style>
    .search-section {
        max-width: 900px;
        width: 100%;
        margin-bottom: 20px;
    }

    .search-section form {
        display: flex;
        gap: 10px;
    }

    .search-bar {
        width: 100%;
    }
</style>

<div class="search-section">
    <form action="{{ $searchUrl }}">
        <input type="search" class="form-control" name="search" placeholder="Cari" value="{{ request('search') }}">
        <button class="btn btn-primary" type="submit">Cari</button>
    </form>
</div>