<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>PRiM | Competition</title>

    <style>
        body {
            margin: 0;
            margin-bottom: 50px;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .add, h3 {
            width: 90%;
            margin: 0 auto;
        }

        .form-control, input:not([type=radio]) {
            color: #0f172a !important;
            font-size: 0.95rem;
            font-weight: 400;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 10px 14px;
        }

        input::placeholder {
            color: #94a3b8 !important;
            font-weight: 400;
            opacity: 1;
        }

        label:not(.small, .form-check-label, .no-required)::after {
            content: " *";
            color: red;
        }

        .btn-submit {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: white;
            padding: 6px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        
        .btn-submit:hover {
            background-color: #4338ca;
            color: white;
            border-color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        @media (min-width: 992px) {
            .add, h3 {
                width: 60%;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    @include('competition.component.nav')

    <h3 class="mt-5">Tambah Pertandingan</h3>

    <form id="addForm" enctype="multipart/form-data" novalidate>
        @csrf

        <div class="row g-3 p-4 mt-4 border rounded bg-white add">
            <div class="col-12">
                <label for="competitionTitle" class="form-label fw-semibold">Nama Pertandingan</label>
                <input type="text" name="competitionTitle" id="competitionTitle" placeholder="Contoh: Programming Competition 2026" class="form-control @error('competitionTitle') is-invalid @enderror" value="{{ old('competitionTitle') }}">

                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <label for="category" class="form-label fw-semibold">Kategori Pertandingan</label>
                <select name="category" id="category" class="form-select @error('category') is-invalid @enderror">
                    <option value="" selected disabled>Pilih Kategori Pertandingan</option>
                    @foreach(['Teknologi', 'Inovasi', 'Sains', 'Akademik', 'Multimedia & Kreatif', 'Reka Bentuk', 
                              'Bahasa & Komunikasi', 'Sukan & Rekreasi', 'Seni & Kebudayaan', 'Lain-Lain'] as $cat)
                        <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>

                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <label for="venue" class="form-label fw-semibold">Tempat Pertandingan</label>
                <input type="text" name="venue" id="venue" placeholder="Contoh: UTeM" class="form-control @error('venue') is-invalid @enderror" value="{{ old('venue') }}">

                <div class="invalid-feedback"></div>
            </div>
            
            <div class="col-12">
                <label for="registerOpen" class="form-label fw-semibold">Pendaftaran Dibuka</label>
                <input type="datetime-local" name="registerOpen" id="registerOpen" class="form-control @error('registerOpen') is-invalid @enderror" value="{{ old('registerOpen') }}">

                <div class="invalid-feedback"></div>
            </div>
            
            <div class="col-12">
                <label for="registerClose" class="form-label fw-semibold">Pendaftaran Ditutup</label>
                <input type="datetime-local" name="registerClose" id="registerClose" class="form-control @error('registerClose') is-invalid @enderror" value="{{ old('registerClose') }}">

                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold">Tarikh Pertandingan</label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="competitionStart" class="form-label text-muted small">Mula</label>
                        <input type="date" name="competitionStart" id="competitionStart" class="form-control @error('competitionStart') is-invalid @enderror" value="{{ old('competitionStart') }}">

                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="competitionEnd" class="form-label text-muted small">Akhir</label>
                        <input type="date" name="competitionEnd" id="competitionEnd" class="form-control @error('competitionEnd') is-invalid @enderror" value="{{ old('competitionEnd') }}">

                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold no-required">Had Umur</label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="minimumAge" class="form-label text-muted small">Minimum Umur</label>
                        <input type="number" name="minimumAge" id="minimumAge" placeholder="Contoh: 12" min="1" class="form-control @error('minimumAge') is-invalid @enderror" value="{{ old('minimumAge') }}">

                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="maximumAge" class="form-label text-muted small">Maksimum Umur</label>
                        <input type="number" name="maximumAge" id="maximumAge" placeholder="Contoh: 15" min="1" class="form-control @error('maximumAge') is-invalid @enderror" value="{{ old('maximumAge') }}">

                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label class="form-label fw-semibold no-required">Yuran (per individu)</label>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nationalFees" class="form-label text-muted small">Warganegara</label>
                        <div class="input-group">
                            <span class="input-group-text">RM</span>
                            <input type="text" name="nationalFees" id="nationalFees" placeholder="0.00" class="form-control currency @error('nationalFees') is-invalid @enderror" inputmode="decimal" value="{{ old('nationalFees') }}">
                                 
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="internationalFees" class="form-label text-muted small">Bukan Warganegara</label>
                        <div class="input-group">
                            <span class="input-group-text">RM</span>
                            <input type="text" name="internationalFees" id="internationalFees" placeholder="0.00" class="form-control currency @error('internationalFees') is-invalid @enderror" inputmode="decimal" value="{{ old('internationalFees') }}">
                                                             
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <label for="participateType" class="form-label fw-semibold d-block">Jenis Penyertaan</label>
                <div class="form-check form-check-inline mt-1">
                    <input type="radio" name="participateType" id="individual" value="Individual" class="form-check-input @error('participateType') is-invalid @enderror" {{ old('participateType') == 'Individual' ? 'checked' : '' }}>
                    <label for="individual" class="form-check-label">Individu</label>
                </div>

                <div class="form-check form-check-inline mt-1">
                    <input type="radio" name="participateType" id="team" value="Team" class="form-check-input @error('participateType') is-invalid @enderror" {{ old('participateType') == 'Team' ? 'checked' : '' }}>
                    <label for="team" class="form-check-label">Berkumpulan</label>
                </div>

                <div class="invalid-feedback d-block"></div>
            </div>

            <div class="col-6" id="minParticipate">
                <label for="minimumParticipate" class="form-label text-muted small">Minimum Peserta</label>
                <div class="input-group">
                    <input type="number" name="minimumParticipate" id="minimumParticipate" placeholder="Contoh: 3" min="2" class="form-control @error('minimumParticipate') is-invalid @enderror" value="{{ old('minimumParticipate') }}">                  
                    
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="col-6" id="maxParticipate">
                <label for="maximumParticipate" class="form-label text-muted small">Maksimum Peserta</label>
                <div class="input-group">
                    <input type="number" name="maximumParticipate" id="maximumParticipate" placeholder="Contoh: 6" min="3" class="form-control @error('maximumParticipate') is-invalid @enderror" value="{{ old('maximumParticipate') }}">
                    
                    <div class="invalid-feedback"></div>
                </div>
            </div>

            <div class="col-12">
                <label for="description" class="form-label fw-semibold">Penerangan Pertandingan</label>
                <textarea name="description" id="description" rows="10" placeholder="Tulis penerangan mengenai pertandingan..." class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                
                <div class="invalid-feedback"></div>
            </div>

            <div class="col-12">
                <label for="imagePoster" class="form-label fw-semibold">Poster Pertandingan</label>
                @if(isset($competition) && $competition->imagePoster)
                <div class="mb-2 p-2 border rounded bg-light d-flex align-items-center justify-content-between">
                    <span class="text-muted small">
                        <strong>Fail Semasa:</strong> {{ $competition->imagePoster }}
                    </span>
                    <a href="{{ asset('competition-image/' . $competition->imagePoster) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Fail</a>
                </div>
                @endif
                <input type="file" name="imagePoster" id="imagePoster" accept="image/*" class="form-control @error('imagePoster') is-invalid @enderror">
                <div class="form-text">Format fail: PNG, JPG, JPEG (Maksimum 2MB).</div>

                <div class="invalid-feedback"></div>
            </div>

            <div class="mt-4 row g-2 justify-content-md-end">
                <div class="col-6 col-md-auto">
                    <button type="submit" name="action" value="draft" class="btn btn-submit px-md-4 w-100"><i class="bi bi-file-earmark-text me-1"></i> Draft</button>
                </div>
                <div class="col-6 col-md-auto">
                    <button type="submit" name="action" value="publish" class="btn btn-submit px-md-4 w-100"><i class="bi bi-send-fill me-1"></i> Publish</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Script -->
    <script>
        const today = new Date().toISOString().split('T')[0];

        const dateOpen = document.getElementById('registerOpen');
        const dateClose = document.getElementById('registerClose');
        
        const dateStart = document.getElementById('competitionStart');
        const dateEnd = document.getElementById('competitionEnd');

        dateOpen.min = today;
        dateClose.min = today;

        dateStart.min = today;
        dateEnd.min = today;

        dateOpen.addEventListener('change', function () {
            if (this.value)
            {
                dateClose.min = this.value;
            }
            else 
            {
                dateClose.min = today;   
            }
        });

        dateClose.addEventListener('change', function () {
            if (this.value)
            {
                dateStart.min = this.value;
            }
            else 
            {
                dateStart.min = today;   
            }
        });

        dateStart.addEventListener('change', function () {
            if (this.value)
            {
                dateEnd.min = this.value;
            }
            else 
            {
                dateEnd.min = dateClose.value || today;   
            }
        });

        document.querySelectorAll('.currency').forEach(input => {
            input.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');

                if (!value)
                {
                    this.value = '';
                    return;
                }

                this.value = (parseInt(value, 10) / 100).toFixed(2);
            });
        });

        $(document).ready(function () {
            $('#minParticipate, #maxParticipate').hide();

            $('input[name="participateType"]').on('change', function() {

                if ($(this).val() === 'Team')
                {
                    $('#minParticipate, #maxParticipate').show();
                }
                else
                {
                    $('#minParticipate, #maxParticipate').hide();
                }
            });
        });

        document.getElementById('addForm').addEventListener('submit', function (e) {
            e.preventDefault();

            let form = this;    
            let formData = new FormData(form);

            formData.append('action', e.submitter.value);

            document.querySelectorAll('.is-invalid').forEach(function (input) {
                input.classList.remove('is-invalid');
            });

            document.querySelectorAll('.invalid-feedback').forEach(function (error) {
                error.innerHTML = "";
            });

            fetch("{{ route('competition.storecompetition') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "Accept": "application/json"
                }
            })
            .then (response => response.json())
            .then (data => {
                if (data.status === false)
                {
                    let firstError = null;

                    if (data.errors)
                    {
                        Object.keys(data.errors).forEach(function (field) {
                            let input = document.querySelector(`[name="${field}"]`);

                            if (!firstError)
                            {
                                firstError = input;
                            }

                            if (input)
                            {
                                if (input.type === 'radio')
                                {
                                    document.querySelectorAll(`[name="${field}"]`).forEach(function (radio) {
                                        radio.classList.add('is-invalid');
                                    });

                                    let container = input.closest('.col-12, .col-md-6, .col-6');
                                    let feedback = container.querySelector('.invalid-feedback');

                                    if (feedback)
                                    {
                                        feedback.textContent = data.errors[field][0];
                                    }
                                }
                                else
                                {
                                    input.classList.add('is-invalid');
                                    let feedback = input.parentElement.querySelector('.invalid-feedback');

                                    if (feedback)
                                    {
                                        feedback.innerHTML = data.errors[field][0];
                                    }
                                }
                            }
                        });

                        if (firstError)
                        {
                            setTimeout(function () {
                                firstError.scrollIntoView ({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                if (firstError.type !== 'radio')
                                {
                                    firstError.focus();
                                }
                            }, 100);
                        }
                    }
                    else if (data.message)
                    {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ralat Borang',
                            text: data.message,
                            confirmButtonColor: '#4f46e5',
                            confirmButtonText: 'OK'
                        });
                    }
                }
                else
                {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berjaya',   
                        text: data.message,
                        confirmButtonColor: '#4f46e5',
                        confirmButtonText: 'OK'
                    }).then ((result) => {
                        if (result.isConfirmed) window.location.href = "{{ route('competition.host') }}";
                    });
                }
            });
        });

        document.addEventListener('input', function (e) {
            if (e.target.classList.contains('is-invalid'))
            {
                e.target.classList.remove('is-invalid');

                let container = e.target.closest('.col-12, .col-md-6, .col-6') || e.target.parentElement;
                let feedback = container.querySelector('.invalid-feedback');
                
                if (feedback)
                {
                    feedback.style.display = 'none';
                }
            }
        });

        document.addEventListener('change', function(e) {
            if (e.target.type === 'radio')
            {
                document.querySelectorAll(`input[name="${e.target.name}"]`).forEach(r => r.classList.remove('is-invalid'));
                
                let container = e.target.closest('.col-12, .col-md-6, .col-6') || e.target.parentElement;
                let feedback = container.querySelector('.invalid-feedback');

                if (feedback)
                {
                    feedback.classList.remove('d-block');
                    feedback.style.display = 'none';
                }
            }
        });
    </script>
</body>
</html>