<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"/>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>PRiM | Competition</title>

    <style>
        body {
            margin: 0;
            margin-bottom: 50px;
            background-color: #f8fafc;
            overflow-x: hidden;
        }
        
        h5 {
            font-size: 1rem;
        }

        .headerform {
            background-color: #f5f3ff;
            height: 50px;
            padding-left: 20px;
            border-bottom: 1px solid gainsboro;
            border-radius: 4px 4px 0px 0px;
        }

        .badge-color {
            color: #6d28d9 !important;
            background-color: #ede9fe !important;
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

        #personal, #member, #academic, #academicSupervise, #pay, #button {
            width: 90%;
            margin: 0 auto;
        }

        label:not(.small, .form-check-label)::after {
            content: " *";
            color: red;
        }

        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .btn-academic {
            background-color: #4f46e5;
            border-color: #4f46e5;
            color: white;
            padding: 5px 10px;
            font-weight: 500;
            transition: all 0.2s ease-in-out;
        }
        
        .btn-academic:hover {
            background-color: #4338ca;
            color: white;
            border-color: #4338ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        }

        input[type=number] {
            appearance: textfield;
        }

        #academic, #btnAcademic, #academicSupervise {
            display: none;
        }
        
        @media (min-width: 992px) {
            #personal, #member, #academic, #academicSupervise, #pay, #button {
                width: 60%;
                margin: 0 auto;
            }
        }
    </style>
</head>
<body>
    @include('competition.component.nav')

    @include('competition.component.headercompetition')

    <form id="registerForm" novalidate>
        @csrf
        <input type="hidden" name="register_type" value="individual">
        <input type="hidden" name="competitionId" value="{{ $competition->id }}">
        
        @if($competition->participateType === 'Team')
        <!-- Kumpulan -->
        <input type="hidden" name="participant_type" value="team">

        <div id="personal" class="mt-4 border rounded bg-white">
            <div class="col-12 headerform m-0 d-flex align-items-center">
                <div class="badge badge-color text-dark p-2 rounded-3 me-3">
                    <i class="bi-person-vcard fs-6"></i>
                </div>
                <h5 class="fw-bold m-0">Maklumat Kumpulan dan Ketua Kumpulan</h5>
            </div>

            <div class="row g-3 p-4">
                <div class="col-12 mb-1">
                    <label for="groupName" class="form-label fw-semibold">Nama Kumpulan</label>
                    <input type="text" name="groupName" id="groupName" placeholder="Masukkan nama penuh" class="form-control @error('groupName') is-invalid @enderror" value="{{ old('groupName') }}">
                            
                    <div class="invalid-feedback"></div>
                </div>

                <hr>

                <div class="col-12 mt-0">
                    <div class="d-flex">
                        <span class="badge bg-secondary-subtle rounded-3 mb-2 text-secondary"><i class="bi bi-person-fill me-1"></i> Ahli 1 - Ketua</span>
                    </div>
                    <label for="name" class="form-label fw-semibold">Nama Penuh</label>
                    <input type="text" name="members[0][name]" placeholder="Masukkan nama penuh" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', Auth::user()->name ?? '') }}">
                            
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="icNo" class="form-label fw-semibold">No IC / No Pasport</label>
                    <input type="text" name="members[0][icNo]" placeholder="Contoh: 012021105432 / A19232534" max="25" class="form-control @error('icNo') is-invalid @enderror" value="{{ old('icNo') }}">
                            
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="noTel" class="form-label fw-semibold">No Telefon</label>
                    <input type="number" name="members[0][noTel]" placeholder="Contoh: 0123456789" min="0" class="form-control  @error('noTel') is-invalid @enderror" value="{{ old('noTel', Auth::user()->telno ?? '') }}">

                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" name="members[0][email]" placeholder="contoh@example.com" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email ?? '') }}">

                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-6">
                    <label for="gender" class="form-label fw-semibold d-block">Jantina</label>
                    <div class="form-check form-check-inline mt-1">
                        <input type="radio" name="members[0][gender]" id="male_0" value="Lelaki" class="form-check-input @error('gender') is-invalid @enderror" {{ old('gender') == "Lelaki" ? 'checked' : '' }}>
                        <label for="male_0" class="form-check-label">Lelaki</label>
                    </div>
                    
                    <div class="form-check form-check-inline mt-1">
                        <input type="radio" name="members[0][gender]" id="female_0" value="Perempuan" class="form-check-input @error('gender') is-invalid @enderror" {{ old('gender') == "Perempuan" ? 'checked' : '' }}>
                        <label for="female_0" class="form-check-label">Perempuan</label>
                    </div>

                    <div class="invalid-feedback d-block"></div>
                </div>
            </div>
        </div>

        <!-- Team Member -->
        <div id="member" class="mt-4 border rounded bg-white">
            <div class="col-12 headerform m-0 d-flex align-items-center">
                <div class="badge badge-color text-dark p-2 rounded-3 me-3">
                    <i class="bi bi-people-fill fs-6"></i>
                </div>
                <h5 class="fw-bold m-0">Maklumat Ahli Kumpulan</h5>
            </div>

            <div class="p-4" id="memberContainer">
                <div class="row g-3 bg-light rounded-3 pb-3">
                    <div class="col-12">
                        <div class="d-flex">
                            <span class="badge bg-secondary-subtle rounded-3 mb-2 text-secondary"><i class="bi bi-person-fill me-1"></i> Ahli 2</span>
                        </div>
                        <label for="name" class="form-label fw-semibold">Nama Penuh</label>
                        <input type="text" name="members[1][name]" placeholder="Masukkan nama penuh" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                                
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="icNo" class="form-label fw-semibold">No IC / No Pasport</label>
                        <input type="text" name="members[1][icNo]" placeholder="Contoh: 012021105432 / A19232534" max="25" class="form-control @error('icNo') is-invalid @enderror" value="{{ old('icNo') }}">
                                
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="noTel" class="form-label fw-semibold">No Telefon</label>
                        <input type="number" name="members[1][noTel]" placeholder="Contoh: 0123456789" min="0" class="form-control  @error('noTel') is-invalid @enderror" value="{{ old('noTel') }}">

                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Alamat Email</label>
                        <input type="email" name="members[1][email]" placeholder="contoh@example.com" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">

                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="col-md-6">
                        <label for="gender" class="form-label fw-semibold d-block">Jantina</label>
                        <div class="form-check form-check-inline mt-1">
                            <input type="radio" name="members[1][gender]" id="male_1" value="Lelaki" class="form-check-input @error('gender') is-invalid @enderror" {{ old('gender') == "Lelaki" ? 'checked' : '' }}>
                            <label for="male_1" class="form-check-label">Lelaki</label>
                        </div>
                        
                        <div class="form-check form-check-inline mt-1">
                            <input type="radio" name="members[1][gender]" id="female_1" value="Perempuan" class="form-check-input @error('gender') is-invalid @enderror" {{ old('gender') == "Perempuan" ? 'checked' : '' }}>
                            <label for="female_1" class="form-check-label">Perempuan</label>
                        </div>

                        <div class="invalid-feedback d-block"></div>
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <button type="button" class="btn btn-outline-primary p-1 w-100 addMemberBtn"><i class="bi bi-plus-circle me-1"></i> Ahli Kumpulan</button>
                </div>
            </div>
        </div>

        @else
        <!-- Personal -->
        <div id="personal" class="mt-4 border rounded bg-white">
            <div class="col-12 headerform m-0 d-flex align-items-center">
                <div class="badge badge-color text-dark p-2 rounded-3 me-3">
                    <i class="bi-person-vcard fs-6"></i>
                </div>
                <h5 class="fw-bold m-0">Maklumat Peserta</h5>
            </div>

            <div class="row g-3 p-4">
                <div class="col-12">
                    <label for="name" class="form-label fw-semibold">Nama Penuh</label>
                    <input type="text" name="name" id="name" placeholder="Masukkan nama penuh" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', Auth::user()->name ?? '') }}">
                            
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="icNo" class="form-label fw-semibold">No IC / No Pasport</label>
                    <input type="text" name="icNo" id="icNo" placeholder="Contoh: 012021105432 / A19232534" max="25" class="form-control @error('icNo') is-invalid @enderror" value="{{ old('icNo') }}">
                            
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="noTel" class="form-label fw-semibold">No Telefon</label>
                    <input type="number" name="noTel" id="noTel" placeholder="Contoh: 0123456789" min="0" class="form-control  @error('noTel') is-invalid @enderror" value="{{ old('noTel', Auth::user()->telno ?? '') }}">

                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Alamat Email</label>
                    <input type="email" name="email" id="email" placeholder="contoh@example.com" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', Auth::user()->email ?? '') }}">

                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="gender" class="form-label fw-semibold d-block">Jantina</label>
                    <div class="form-check form-check-inline mt-1">
                        <input type="radio" name="gender" id="male" value="Lelaki" class="form-check-input @error('gender') is-invalid @enderror" {{ old('gender') == "Lelaki" ? 'checked' : '' }}>
                        <label for="male" class="form-check-label">Lelaki</label>
                    </div>
                    
                    <div class="form-check form-check-inline mt-1">
                        <input type="radio" name="gender" id="female" value="Perempuan" class="form-check-input @error('gender') is-invalid @enderror" {{ old('gender') == "Perempuan" ? 'checked' : '' }}>
                        <label for="female" class="form-check-label">Perempuan</label>
                    </div>

                    <div class="invalid-feedback d-block"></div>
                </div>
            </div>
        </div>
        @endif

        <!-- Academic -->
        <div id="academic" class="mt-4 border rounded bg-white">
            <div class="headerform m-0 d-flex align-items-center">
                <div class="badge badge-color text-dark p-2 rounded-3 me-3">
                    <i class="bi-mortarboard-fill fs-6"></i>
                </div>
                <h5 class="fw-bold m-0">Maklumat Akademik</h5>
            </div>

            <div class="row g-3 p-4">
                <div class="col-12">
                    <label for="instituteType" class="form-label fw-semibold">Jenis Institusi</label>
                    <select name="instituteType" id="instituteType" class="form-select @error('instituteType') is-invalid @enderror">
                        <option value="" selected disabled>Pilih Jenis Institusi</option>
                        <option value="Sekolah Rendah" {{ old('instituteType') == 'Sekolah Rendah' ? 'selected' : '' }}>Sekolah Rendah</option>
                        <option value="Sekolah Menengah" {{ old('instituteType') == 'Sekolah Menengah' ? 'selected' : '' }}>Sekolah Menengah</option>   
                        <option value="Pra-Universiti"{{ old('instituteType') == 'Pra-Universiti' ? 'selected' : '' }}>Pra-Universiti</option>
                        <option value="Kolej Vokasional & Politeknik"{{ old('instituteType') == 'Kolej Vokasional & Politeknik' ? 'selected' : '' }}>Kolej Vokasional & Politeknik</option>
                        <option value="Universiti"{{ old('instituteType') == 'Universiti' ? 'selected' : '' }}>Universiti</option>
                    </select>
                            
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <div>
                        <label for="instituteName" class="form-label fw-semibold">Nama Institusi</label>
                        <select name="instituteName" id="instituteName" class="form-select @error('instituteName') is-invalid @enderror">
                            <option value="" selected disabled>Pilih Nama Institusi</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div>
                        <div class="mt-3 d-none" id="add-institution">
                            <label for="add-institute-name" class="form-label fw-semibold">Nyatakan Nama Institusi</label>
                            <input type="text" name="add-institute-name" id="add-institute-name" class="form-control @error('add-institute-name') is-invalid @enderror" placeholder="Masukkan nama institusi" value="{{ old('add-institute-name') }}">
                            <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="currentGrade" class="form-label fw-semibold">Pengajian Semasa</label>
                    <select name="currentGrade" id="currentGrade" class="form-select @error('currentGrade') is-invalid @enderror" value="{{ old('currentGrade') }}">
                        <option value="" selected disabled>Pilih Pengajian Semasa</option>
                    </select>
                            
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>

        <div id="academicSupervise" class="mt-4 border rounded bg-white">
            <div class="headerform m-0 d-flex align-items-center">
                <div class="badge badge-color text-dark p-2 rounded-3 me-3">
                    <i class="bi-person-workspace fs-6"></i>
                </div>
                <h5 class="fw-bold m-0">Maklumat Penyelia</h5>
            </div>

            <div class="row g-3 p-4">
                <div class="col-12">
                    <label for="supervisorName" class="form-label fw-semibold">Nama Penyelia</label>
                    <input type="text" name="supervisorName" id="supervisorName" placeholder="Contoh: Dr. Abu bin Ali" class="form-control @error('supervisorName') is-invalid @enderror" value="{{ old('supervisorName') }}">

                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="supervisorEmail" class="form-label fw-semibold">Alamat Email Penyelia</label>
                    <input type="email" name="supervisorEmail" id="supervisorEmail" placeholder="Contoh: abu@email.com" class="form-control @error('supervisorEmail') is-invalid @enderror" value="{{ old('supervisorEmail') }}">

                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="supervisorNoTel" class="form-label fw-semibold">No Telefon Penyelia</label>
                    <input type="number" name="supervisorNoTel" id="supervisorNoTel" placeholder="Contoh: 0123456789" min="0" class="form-control @error('supervisorNoTel') is-invalid @enderror" value="{{ old('supervisorNoTel') }}">
                    
                    <div class="invalid-feedback"></div>
                </div>
            </div>
        </div>

        <div class="card bg-white border border-light-subtle rounded-3 p-3 mt-3" id="pay">
            <div>
                <div>
                    <small class="text-muted fw-semibold">Yuran Pembayaran: </small>
                    <span class="fs-5 ms-1 fw-bold" id="displayFee" style="color: #4f46e5;">RM 0.00</span>
                </div>

                <div class="mt-2 alert alert-info mb-0 py-2 border-0 d-flex align-items-center" style="font-size: 13px;">
                    <i class="bi bi-info-circle-fill text-info fs-5 me-2"></i>
                    <span><strong>Nota:</strong> Anda boleh mendaftar dahulu dan membuat pembayaran kemudian di menu <strong>Pendaftaran Saya</strong>.</span>
                </div>
            </div>
        </div>

        <div class="mt-4" id="button">
            <div class="d-flex justify-content-end">
                <button type="button" id="btnPersonal" class="btn btn-academic" onclick="showAcademic()">Teruskan ke Maklumat Akademik <i class="bi bi-arrow-right ms-1"></i></button>
            </div>

            <div id="btnAcademic" class="justify-content-between">
                <button type="button" class="btn btn-academic" onclick="showPersonal()"><i class="bi bi-arrow-left me-1"></i> Kembali</button>
                <button type="submit" class="btn btn-academic"><i class="bi-send-fill me-1"></i> Daftar Sekarang</button>
            </div>
        </div>
    </form>
    
    <!-- Script -->
    <script>
        let education = document.getElementById('instituteType');
        let educationName = document.getElementById('instituteName');
        let grade = document.getElementById('currentGrade');
        let oldGrade = "{{ old('currentGrade') }}";
        let oldInstitution = "{{ old('instituteName') }}";
        const allInstitutions = JSON.parse('@json($institutions)');

        function callSelect (selectEducate, targetGrade = null) {
            grade.innerHTML = `<option value="" selected disabled>Pilih Pengajian Semasa</option>`;    

            if (selectEducate === "Sekolah Rendah")
            {
                for (let i = 1; i <= 6; i++)
                {
                    grade.innerHTML += `<option value="Tahun ${i}" ${targetGrade === `Tahun ${i}` ? 'selected' : ''}>Tahun ${i}</option>`;
                }
            }
            else if (selectEducate === "Sekolah Menengah")
            {
                for (let i = 1; i <= 5; i++)
                {
                    grade.innerHTML += `<option value="Tingkatan ${i}" ${targetGrade === `Tingkatan ${i}` ? 'selected' : ''}>Tingkatan ${i}</option>`;
                }
            }
            else if (selectEducate === "Pra-Universiti")
            {
                grade.innerHTML += `<option value="Tingkatan 6" ${targetGrade === `Tingkatan 6` ? 'selected' : ''}>Tingkatan 6</option>`;
                grade.innerHTML += `<option value="Matrikulasi"${targetGrade === `Matrikulasi` ? 'selected' : ''}>Matrikulasi</option>`;
                grade.innerHTML += `<option value="Asasi" ${targetGrade === `Asasi` ? 'selected' : ''}>Asasi</option>`;
            }
            else if (selectEducate === "Kolej Vokasional & Politeknik")
            {
                for (let i = 1; i <= 2; i++)
                {
                    grade.innerHTML += `<option value="Sijil Vokasional Malaysia (SVM) Tahun ${i}" ${targetGrade === `Sijil Vokasional Malaysia (SVM) Tahun ${i}` ? 'selected' : ''}>Sijil Vokasional Malaysia (SVM) Tahun ${i}</option>`;
                }
                for (let i = 1; i <= 2; i++)
                {
                    grade.innerHTML += `<option value="Diploma Vokasional Malaysia (DVM) Tahun ${i}" ${targetGrade === `Diploma Vokasional Malaysia (DVM) Tahun ${i}` ? 'selected' : ''}>Diploma Vokasional Malaysia (DVM) Tahun ${i}</option>`;
                }
                for (let i = 1; i <= 2; i++)
                {
                    grade.innerHTML += `<option value="Politeknik / Kolej Komuniti Diploma Tahun ${i}" ${targetGrade === `Politeknik / Kolej Komuniti Diploma Tahun ${i}` ? 'selected' : ''}>Politeknik / Kolej Komuniti Diploma Tahun ${i}</option>`;
                }
            }
            else if (selectEducate === "Universiti")
            {
                for (let i = 1; i <= 3; i++)
                {
                    grade.innerHTML += `<option value="Diploma Tahun ${i}" ${targetGrade === `Diploma Tahun ${i}` ? 'selected' : ''}>Diploma Tahun ${i}</option>`;
                }

                for (let i = 1; i <= 5; i++)
                {
                    grade.innerHTML += `<option value="Degree Tahun ${i}" ${targetGrade === `Degree Tahun ${i}` ? 'selected' : ''}>Degree Tahun ${i}</option>`;
                }
            }
        }

        function filterInstitutionName(education, targetInsutitute = null)
        {
            const $educationName = $('#instituteName');

            let option = `<option value="" selected disabled>Pilih Nama Institusi</option>`;

            const filter = allInstitutions.filter(institution => institution.instituteType === education);

            filter.forEach(institution => {
                let isSelect = (targetInsutitute && String(targetInsutitute) === String(institution.id)) ? 'selected' : '';
                option += `<option value="${institution.id}" ${isSelect}>${institution.instituteName}</option>`;
            });

            const otherSelect = (String(targetInsutitute) === 'Lain-Lain') ? 'selected' : '';
            option  += `<option value="Lain-Lain" ${otherSelect}>Lain-Lain</option>`;

            $educationName.html(option);

            if (targetInsutitute)
            {
                $educationName.val(targetInsutitute);
            }

            $educationName.trigger('change.select2');
        }

        $(document).ready(function () {
            $('#instituteName').on('change.select2', function () {
                const value = $(this).val();
                const $wrapInput = $('#add-institution');
                const $inputAdd = $('#add-institute-name');
                const $parent = $(this).closest('div');

                if ($(this).val() === 'Lain-Lain')
                {
                    $wrapInput.removeClass('d-none');
                    $inputAdd.prop('required', true);
                }
                else
                {
                    $wrapInput.addClass('d-none');
                    $inputAdd.prop('required', false).val('');
                }

                if (value && value !== '')
                {
                    $(this).removeClass('is-invalid');
                    $parent.find('.invalid-feedback').removeClass('d-block').hide().text('');

                    const $select2 = $parent.find('.select2-container');

                    if ($select2.length)
                    {
                        $select2.removeClass('is-invalid');
                        $select2.find('.select2-selection').removeClass('is-invalid').css('border-color', '');
                    }
                }
                else
                {
                    $parent.find('.invalid-feedback').addClass('d-block');
                }
            });

            $('#instituteName').select2({
                theme: 'bootstrap-5',
                placeholder: "Pilih nama institusi",
                allowClear: true,
                width: '100%'
            }); 
        });

        education.addEventListener('change', function () {
            callSelect(this.value);
            filterInstitutionName(this.value);
        });

        if (education.value)
        {
            callSelect(education.value, oldGrade);
            filterInstitutionName(education.value, oldInstitution);
        }

        function showAcademic()
        {
            document.getElementById('personal').style.display = 'none';
            let member = document.getElementById('member');
            document.getElementById('btnPersonal').style.display = 'none';
            document.getElementById('academic').style.display = 'block';
            document.getElementById('academicSupervise').style.display = 'block';
            document.getElementById('btnAcademic').style.display = 'flex';

            if(member) member.style.display = 'none';
        }

        function showPersonal()
        {
            document.getElementById('personal').style.display = 'block';
            let member = document.getElementById('member');
            document.getElementById('btnPersonal').style.display = 'block';
            document.getElementById('academic').style.display = 'none';
            document.getElementById('academicSupervise').style.display = 'none';
            document.getElementById('btnAcademic').style.display = 'none';

            if(member) member.style.display = 'block';
        }

        document.getElementById('registerForm').addEventListener('submit', function (e) {
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
            .then (response => response.json())
            .then (data => {
                if (data.status === false)
                {
                    let firstError = null;
                    let minError = [];

                    if (data.errors)
                    {
                        Object.keys(data.errors).forEach(function (field, index) {
                            if (field === 'members')
                            {
                                minError.push(data.errors[field][0]);
                                return;
                            }

                            let inputName = field.replace(/\.([^.]+)/g, '[$1]');
                            let input = document.querySelector(`[name="${inputName}"]`);

                            if (input)
                            {
                                if (!firstError)
                                {
                                    firstError = input;
                                }

                                if (input.type === 'radio')
                                {
                                    document.querySelectorAll(`[name="${inputName}"]`).forEach(function (radio) {
                                        radio.classList.add('is-invalid');
                                    });

                                    let container = input.closest('.col-12, .col-md-6, .col-6');
                                    let feedback = container.querySelector('.invalid-feedback');

                                    if (feedback)
                                    {
                                        feedback.textContent = data.errors[field][0];
                                    }

                                    if (index === 0)
                                    {
                                        firstError = input;
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
                            else
                            {
                                minError.push(data.errors[field][0]);
                            }
                        });

                        if (minError.length > 0)
                        {
                            let errorList = minError.map(err => `<li>${err}</li>`).join('');

                            Swal.fire({
                                icon: 'warning',
                                title: 'Sila Semak Borang',
                                html: `<ul class="text-start mb-0 ps-3">${errorList}</ul>`,
                                confirmButtonColor: '#4f46e5',
                                confirmButtonText: 'OK'
                            });
                        }

                        if (firstError)
                        {
                            let stepForm = firstError.closest('#personal, #member') !== null;

                            if (stepForm && typeof showPersonal === 'function')
                            {
                                showPersonal();
                            }
                        
                            setTimeout(function () {
                                firstError.scrollIntoView({
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
                        if (result.isConfirmed) window.location.href = "{{ route('competition.userrecord') }}";
                    });
                }
            })
            .catch(error => {
               console.error('Error: ', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Ralat Sistem',   
                    text: 'Terdapat ralat semasa menghantar borang.',
                    confirmButtonColor: '#4f46e5',
                    confirmButtonText: 'OK'
                });
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

            if (e.target.name === 'icNo' || (e.target.name && e.target.name.includes('[icNo]')))
            {   
                let national = parseFloat("{{ $competition->nationalFees ?? 0 }}");
                let international = parseFloat("{{ $competition->internationalFees ?? 0 }}");
                let total = 0;

                const ICInput = document.querySelectorAll('input[name="icNo"], input[name*="[icNo]"]');

                ICInput.forEach(input => {
                    let value = input.value.trim();
                    if (value === '') return;

                    let cleanIc = input.value.replace(/[^0-9]/g, '');

                    if (cleanIc.length === 12 && !/[a-zA-Z]/.test(value))
                    {
                        total += national;
                    }
                    else
                    {
                        total += international;
                    }
                });
                
                const display = document.getElementById('displayFee');

                if (display)
                {
                    display.textContent = 'RM ' + total.toFixed(2);
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

            if (e.target.tagName === 'SELECT')
            {
                if (e.target.value !== '')
                {
                    e.target.classList.remove('is-invalid');
                    let container = e.target.closest('.col-12, .col-md-6, .col-6') || e.target.parentElement;
                    let select2 = container.querySelector('.select2-selection');

                    if (select2)
                    {
                        select2.classList.remove('is-invalid');
                        select2.style.borderColor = '';
                    }

                    let feedback = container.querySelector('.invalid-feedback');

                    if (feedback)
                    {
                        feedback.classList.remove('d-block');
                        feedback.style.display = 'none';
                    }
                }
            }
        });

        let index = 2;
        $('.addMemberBtn').on('click', function() {
            let addBtn = $(this);

            let memberHTML = `
            <div class="row g-3 bg-light mt-2 rounded-3 pb-3 member-card" id="member-card-${index}" data-max="{{ $competition->maximumParticipate }}">
                <input type="hidden" name="members[${index}][id]" value="">

                <div class="col-12">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-secondary-subtle rounded-3 text-secondary member-index"><i class="bi bi-person-fill me-1"></i> Ahli ${index + 1}</span>
                        </div>
                    <button type="button" class="btn btn-sm btn-outline-danger border-0 removeMemberBtn"><i class="bi bi-trash-fill me-1"></i> Padam</button>
                </div>
                
                    <label for="name_${index}" class="form-label fw-semibold">Nama Penuh</label>
                    <input type="text" id="name_${index}" name="members[${index}][name]" placeholder="Masukkan nama penuh" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                            
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="icNo_${index}" class="form-label fw-semibold">No IC / No Pasport</label>
                    <input type="text" id="icNo_${index}" name="members[${index}][icNo]" placeholder="Contoh: 012021105432 / A19232534" max="25" class="form-control @error('icNo') is-invalid @enderror" value="{{ old('icNo') }}">
                            
                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="noTel_${index}" class="form-label fw-semibold">No Telefon</label>
                    <input type="number" id="noTel_${index}" name="members[${index}][noTel]" placeholder="Contoh: 0123456789" min="0" class="form-control  @error('noTel') is-invalid @enderror" value="{{ old('noTel') }}">

                    <div class="invalid-feedback"></div>
                </div>
                
                <div class="col-md-6">
                    <label for="email_${index}" class="form-label fw-semibold">Alamat Email</label>
                    <input type="email" id="email_${index}" name="members[${index}][email]" placeholder="contoh@example.com" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">

                    <div class="invalid-feedback"></div>
                </div>

                <div class="col-md-6">
                    <label for="gender" class="form-label fw-semibold d-block">Jantina</label>
                    <div class="form-check form-check-inline mt-1">
                        <input type="radio" name="members[${index}][gender]" id="male_${index}" value="Lelaki" class="form-check-input @error('gender') is-invalid @enderror" {{ old('gender') == "Lelaki" ? 'checked' : '' }}>
                        <label for="male_${index}" class="form-check-label">Lelaki</label>
                    </div>
                    
                    <div class="form-check form-check-inline mt-1">
                        <input type="radio" name="members[${index}][gender]" id="female_${index}" value="Perempuan" class="form-check-input @error('gender') is-invalid @enderror" {{ old('gender') == "Perempuan" ? 'checked' : '' }}>
                        <label for="female_${index}" class="form-check-label">Perempuan</label>
                    </div>

                    <div class="invalid-feedback d-block"></div>
                </div>
            </div>`;

            addBtn.before(memberHTML);
            $('.max').hide();
            index++;
            
            let max = parseInt($('.member-card').data('max'));

            if (index === max)
            {
                $('.addMemberBtn').hide();
            }
        });

        function totalPrice()
        {
            let national = parseFloat("{{ $competition->nationalFees ?? 0 }}");
            let international = parseFloat("{{ $competition->internationalFees ?? 0 }}");
            let total = 0;

            $('input[name*="[icNo]"]').each(function () {
                let cleanIc = $(this).val().replace(/[^0-9]/g, '');

                if (cleanIc.length === 12)
                {
                    total += national;
                }
                else
                {
                    total += international;
                }
            });

            const display = document.getElementById('displayFee');

            if (display)
            {
                display.textContent = 'RM ' + total.toFixed(2);
            }
        }

        $(document).on('click', '.removeMemberBtn', function() {
            $(this).closest('.member-card').remove();
            reIndex();
            totalPrice();

            let max = parseInt($('.member-card').data('max'));

            if (index !== max)
            {
                $('.addMemberBtn').show();
            }
        });

        function reIndex()
        {
            let startIndex = 2;

            $('.member-card').each(function (count) {
                let newCount = startIndex + count + 1;
                let card = $(this);

                card.attr('id', `member-card-${newCount}`);
                card.find('.member-index').html(`<i class="bi bi-person-fill me-1"></i> Ahli ${newCount}`);

                card.find('input').each(function () {
                    let input = $(this);
                    let name = input.attr('name');

                    if (name)
                    {
                        let updateName = name.replace(/members\[\d+\]/, `members[${newCount}]`);
                        input.attr('name', updateName);
                    }

                    let oldId = input.attr('id');

                    if (oldId)
                    {
                        let newId = oldId.replace(/_\d+$/, `_${arrayIndex}`);

                        input.attr('id', newId);
                    }
                });

                card.find('label').each(function () {
                    let label = $(this);
                    let forAttr = label.attr('for');

                    if (forAttr)
                    {
                        label.attr('for', forAttr.replace(/_\d+$/, `_${arrayIndex}`));
                    }
                });
            });

            index = startIndex + $('.member-card').length;
        }
    </script>
</body>
</html>