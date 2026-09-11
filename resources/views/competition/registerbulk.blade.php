<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <title>PRiM | Competition</title>

    <style>
        body {
            margin: 0;
            margin-bottom: 50px;
            background-color: #f8fafc;
            overflow-x: hidden;
        }
        
        .btn-download {
            background-color: transparent;
            color: #0f172a;
            border: 1px solid #0f172a;
            font-weight: 500;
            transition: all 0.25 ease-in-out;
        }
        
        .btn-download:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 25%, #e11d48 100%);
            color: white;
            border-color: transparent;
            box-shadow: 0 4px 14px rgba(124, 58, 237, 0.3);
        }

        .btn-submit {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: white;
            padding: 10px 10px;
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

        #layout {
            width: 90%;
            margin: 0 auto;
        }

        @media (min-width: 992px) {
            #layout {
                width: 60%;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    @include('competition.component.nav')

    @include('competition.component.headercompetition')

    <div id="layout">
        <div class="mt-3">
            <div class="g-3 p-4 mt-4 mb-4 border rounded bg-light">
                <h3>Langkah 1</h3>
                <p>Jika anda ingin mendaftarkan peserta secara serentak, sila muat turun format penyertaan yang disediakan dan
                    lengkapkan maklumat semua peserta mengikut format yang ditetapkan. Pastikan setiap maklumat
                    diisi dengan tepat bagi mengelakkan ralat semasa proses import.</p>
                    <p class="mb-0 text-danger fw-semibold">Perhatian:</p>
                    <ul class="ps-3 mb-4">
                        <li>Gunakan format fail yang disediakan sahaja</li>
                        <li>Jangan ubah susunan atau nama lajur dalam fail tersebut</li>
                        <li>Pastikan semua maklumat peserta adalah lengkap dan tepat</li>
                    </ul>
                <a href="{{ route('competition.downloadtemplatecompetition', $competition->id) }}" class="btn btn-download"><i class="bi-download me-1"></i> Muat Turun Format Penyertaan (.xlsx)</a>
            </div>

            <form id="bulkForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="competitionId" value="{{ $competition->id }}">
                <div class="g-3 p-4 mt-4 mb-4 border rounded bg-light">
                    <h3>Langkah 2</h3>
                    <p>Selepas melengkapkan fail tersebut, muat naik fail tersebut menggunakan butang di bawah. Sistem akan
                        memproses dan mengimport maklumat peserta secara automatik.</p>
                    <p class="mb-0 text-danger fw-semibold">Perhatian:</p>
                        <ul class="ps-3 mb-4">
                            <li>Hanya fail Microsoft Excel (.xlsx) dibenarkan</li>
                            <li>Pastikan fail mengikut format yang dimuat turun</li>
                            <li>Semak semula maklumat sebelum menghantar</li>
                        </ul>
                    <input type="hidden" name="register_type" value="bulk">
                    <input type="file" id="file" name="file" accept=".xlsx" class="form-control @error('file') is-invalid @enderror">

                    <div class="invalid-feedback"></div>
                </div>

                <div class="d-flex justify-content-between gap-3">
                    <a href="{{ route('competition.info', $competition->id) }}" class="btn btn-submit px-4"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                    <button type="submit" class="btn btn-submit px-4"><i class="bi-floppy-fill me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function hideMessage()
        {
            $('.hide-message').fadeOut(500, function () {
                $(this).remove();
            });    
        }

        window.addEventListener('pageshow', function (event) {

            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) 
            {
                let file = document.getElementById('file');

                if (file)
                {
                    file.value = '';
                    file.classList.remove('is-invalid');
                }
            }
        });

        document.addEventListener('change', function (e) {
            if (e.target.classList.contains('is-invalid') && e.target.type === 'file')
            {
                e.target.classList.remove('is-invalid');

                let container = e.target.closest('.border') || e.target.parentElement;
                let feedback = container.querySelector('.invalid-feedback');
                
                if (feedback)
                {
                    feedback.style.display = 'none';
                }
            }
        });

        document.getElementById('bulkForm').addEventListener('submit', function (e) {
            e.preventDefault();

            let form = this;    
            let formData = new FormData(form);

            document.querySelectorAll('.is-invalid').forEach(function (input) {
                input.classList.remove('is-invalid');
            });

            document.querySelectorAll('.invalid-feedback').forEach(function (error) {
                error.innerHTML = "";
            });
            
            fetch("{{ route('competition.storeregister') }}", {
                method: "POST",
                body: formData,
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(async response => {
                let data = await response.json();

                if (response.status === 422)
                {
                    if(data.errors)
                    {
                        Object.keys(data.errors).forEach(function (field) {

                            let input = form.querySelector(`[name="${field}"]`);
                            
                            if (input)
                            {
                                input.classList.add('is-invalid');
                                let feedback = input.parentElement.querySelector('.invalid-feedback');

                                if (feedback)
                                {
                                    feedback.innerHTML = data.errors[field][0];
                                    feedback.style.display = 'block';
                                }
                            }
                        });

                        let firstError = document.querySelector('.is-invalid');
                        
                        if (firstError) 
                        {
                            setTimeout(() => firstError.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center'     
                            }), 100);
                        }
                    }
                    return null;
                }

                if (!response.ok)
                {
                    throw new Error(data.message);
                }
                return data;
            })
            .then(data => {

                if (!data) return;

                Swal.fire({
                    icon: data.status,
                    title: data.title,
                    text: data.message,
                    confirmButtonColor: '#4f46e5',
                    confirmButtonText: 'OK'
                })
                .then((result) => {
                    if (result.isConfirmed) 
                    {
                        if (data.redirect)
                        {
                            window.location.href = data.redirect;
                        }
                        else
                        {
                            form.reset();
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Ralat Sistem',
                    text: 'Terdapat ralat semasa memproses fail.',
                    confirmButtonColor: '#4f46e5'
                });
            });
        });
    </script>
</body>
</html>