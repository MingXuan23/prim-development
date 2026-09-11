<style>
    .header {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 25%, #e11d48 100%);
        /* background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);    */
        padding: 20px 20px;
        width: 100%;
        height: auto;
    }

    .breadcrumb-item, .breadcrumb-item a {
        color: rgba(255, 255, 255, 0.75) !important;
        font-weight: 500;
        font-size: 0.875rem;
    }

    .breadcrumb-item a:hover {
        color: #ffffff !important;
        text-decoration: underline !important;
    }

    .breadcrumb-item.active {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .breadcrumb-item::before {
        color: rgba(255, 255, 255, 0.9) !important;
    }
    
    .breadcrumb-item.active::before {
        color: rgba(255, 255, 255, 0.9) !important;
    }

    .category-label {
        background-color: #f59e0b;
        color: #0f172a !important;
        font-size: 0.75rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        padding: 4px 14px;
        border-radius: 50px;
        display: inline-block;
    }

    .click-image {
        border-radius: 10px;
        border: 1px solid white;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        width: 200px;
        cursor: pointer;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .click-image:hover {
        transform: translateY(-4px) scale(1.01);
        box-shadow: 0 10px 20px -5px rgba(0, 0, 0, 0.15), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .image-container {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        backdrop-filter: blur(5px);
        background-color: rgba(0, 0, 0, 0.3);
        justify-content: center;
        align-items: center;
    }

    .modal-wrap {
        position: relative;
        display: inline-block;
        max-width: 90%;
        max-height: 90%;
    }

    .modal-image {
        border: 1px solid white;
        width: 100%;
        height: auto;
        max-height: 85vh;
        border-radius: 8px;
        display: block;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
    }

    .close-btn {
        position: absolute;
        top: 10px;
        right: 15px;
        color: white;
        font-size: 35px;
        font-weight: bold;
        cursor: pointer;
        z-index: 1000;
        line-height: 1;
    }

    .close-btn:hover {
        color: #c6c6c6;
    }

    @media (min-width: 992px) {
        .header {
            padding: 30px 100px 30px 100px;
        }
    }
</style>

<div class="header d-flex flex-column flex-md-row justify-content-between align-items-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('competition.user') }}" class="text-decoration-none">Senarai Pertandingan</a>    
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('competition.info', $competition->id) }}" class="text-decoration-none">{{ $competition->competitionTitle }}</a>    
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Daftar
                </li>
            </ol>
        </nav>
        <p class="mb-0 mt-4 category-label text-uppercase">{{ $competition->category }}</p>
        <h1 class="mb-3 fw-bold text-white">{{ $competition->competitionTitle }}</h1>
        @if($competition->competitionStart > now())
            <p class="badge bg-warning text-dark z-3">UPCOMING</p>
        @elseif($competition->competitionStart <= now() || $competition->competitionEnd >= now())
            <p class="badge bg-success z-3">ONGOING</p>
        @endif

        @if($competition->participateType === 'Team')
            @if($competition->minimumParticipate === $competition->maximumParticipate)
                <span class="badge bg-light ms-2 text-dark border fw-normal"><i class="bi bi-people-fill me-1"></i> {{ $competition->participateType }} ({{$competition->maximumParticipate}} pax)</span>
            @else
                <span class="badge bg-light ms-2 text-dark border fw-normal"><i class="bi bi-people-fill me-1"></i> {{ $competition->participateType }} ({{$competition->minimumParticipate}} - {{$competition->maximumParticipate}} pax)</span>
            @endif
        @else
            <span class="badge bg-light ms-2 text-dark border fw-normal"><i class="bi bi-person-fill me-1"></i> {{ $competition->participateType }}</span>
        @endif
    </div>

    <div>
        <div class="me-5">
            <img src="{{ asset('competition-image/' . $competition->imagePoster) }}" alt="{{ $competition->competitionTitle }}" class="click-image" onclick="openImage(this.src)">
        </div>

        <div class="image-container" id="imageContainer" onclick="closeImage()">
            <div class="modal-wrap">
                <span class="close-btn">&times;</span>
                <img id="modalImage" class="modal-image" onclick="event.stopPropagation()">
            </div>
        </div>
    </div>
</div>

<script>
    function openImage(imageSrc)
    {
        const modal = document.getElementById('imageContainer');
        const modalImg = document.getElementById('modalImage');

        modalImg.src = imageSrc;
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeImage()
    {
        document.getElementById('imageContainer').style.display = 'none';
        document.body.style.overflow = 'auto';
    }
</script>