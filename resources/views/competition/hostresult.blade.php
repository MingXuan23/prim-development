<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>PRiM | Competition</title>

    <style>
        body {
            margin: 0;
            margin-bottom: 50px;
            background-color: #f8fafc;
            overflow-x: hidden;
        }

        .btn-add {
            width: 230px;
            background-color: #facc15;
            border-color: #facc15;
        }

        .btn-add:hover {
            background-color: #eab308;
            border-color: #eab308;
        }
    </style>
</head>

<body>
    @include('competition.component.nav')

    <div class="container mt-5">
        <div>
            <h5 class="modal-title fw-bold text-dark">Kemaskini Keputusan Pertandingan</h5>
            <p class="text-muted small mb-0">Sila tentukan pemenang untuk pertandingan yang dianjurkan.</p>
        </div>

        <form id="updateResult">
            @csrf

            <div class="py-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label for="title" class="form-label fw-semibold text-secondary">Nama Pertandingan</label>
                        <div class="fw-bold">{{ $competition->competitionTitle }}</div>
                        <div class="fw-bold">Jumlah Peserta {{ $competition->participant_registration->count() }}</div>
                        <div class="fw-bold">Status Keputusan {{ $competition->statusResult }}</div>
                    </div>

                    @if($competition->winnerType === 'Medal')
                    <!-- Winner Medal -->
                        <div class="col-12 col-md-6">
                            @foreach(['Emas' => '🥇 Emas', 'Perak' => '🥈 Perak', 'Gangsa' => '🥉 Gangsa'] as $key => $medal)
                            <div class="p-2 border rounded mb-3 winner-group" data-type="{{ $key }}">
                                <label for="{{ $key }}" class="form-label fw-semibold fs-5 mb-3">{{ $medal }}</label>

                                <div class="winner-list"></div>

                                <div class="d-flex">
                                    <select name="{{ $key }}" id="{{ $key }}" class="form-select winner-select">
                                        <option value="">--- Pilih Pemenang {{ $key }} ---</option>
                                        @foreach($competition->participant_registration as $competitions)
                                            @if($competition->participateType === 'Individual')
                                            <option value="{{ $competitions->participant->first()->teamId }}" data-name="{{ $competitions->participant->first()->name }}" data-subtext="{{ $competitions->participant->first()->icNo }}">{{ $competitions->participant->first()->name ?? '' }}</option>
                                            @else
                                            <option value="{{ $competitions->participant->first()->teamId }}" data-name="{{ $competitions->groupName }}" data-subtext="{{ $competitions->participant->pluck('name')->implode(', ') }}">{{ $competitions->groupName ?? '' }} - {{ $competitions->participant->pluck('name')->implode(', ') }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="button" class="btn btn-sm ms-2 fw-bold btn-add text-nowrap"><i class="bi bi-plus-lg"></i> Tambah Pemenang</button>
                                </div>
                            </div>
                            @endforeach

                            <div id="extra-placements-container"></div>

                            <button type="button" id="btn-add-placement" class="btn btn-outline-primary w-100 mb-3 fw-semibold">
                                <i class="bi bi-plus-circle"></i> Tambah Tempat Seterusnya
                            </button>
                        </div>
                    @else
                    <!-- Winner Number -->
                        <div class="col-12 col-md-6">
                            <div class="p-2 border rounded winner-group">
                                @for($index = 1; $index <= $competition->participant_registration->count() ; $index++)
                                        @php
                                            $trophyColor = [
                                                1 => '#eab308',
                                                2 => '#94a3b8',
                                                3 => '#d97706',
                                            ];
                                            $trophyColors = $trophyColor[$index] ?? '#64748b';
                                        @endphp
                                    <div class="mb-3 winner-group" data-type="{{ $index }}">
                                        <label for="No_{{ $index }}" class="form-label fw-semibold"><i class="bi bi-trophy-fill me-1" style="color: {{ $trophyColors }};"></i> No.{{ $index }}: </label>

                                        <div class="winner-list mt-2"></div>
                                        
                                        <div class="d-flex">
                                            <select name="No_{{ $index }}" id="No_{{ $index }}" class="form-select winner-select">
                                                <option value="">--- Pilih Pemenang No.{{ $index }} ---</option>
                                                @foreach($competition->participant_registration as $competitions)
                                                    @if($competition->participateType === 'Individual')
                                                    <option value="{{ $competitions->participant->first()->teamId }}" data-name="{{ $competitions->participant->first()->name }}" data-subtext="{{ $competitions->participant->first()->icNo }}">{{ $competitions->participant->first()->name ?? '' }}</option>
                                                    @else
                                                    <option value="{{ $competitions->participant->first()->teamId }}" data-name="{{ $competitions->groupName }}" data-subtext="{{ $competitions->participant->pluck('name')->implode(', ') }}">{{ $competitions->groupName ?? '' }} - {{ $competitions->participant->pluck('name')->implode(', ') }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <button type="button" class="btn btn-sm ms-2 fw-bold btn-add text-nowrap"><i class="bi bi-plus-lg"></i> Tambah Pemenang</button>
                                        </div>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="border-0 pt-0">
                <button type="submit" name="action" value="draft" class="btn btn-primary px-4 me-3"><i class="bi bi-pencil-square me-1"></i> Draft Keputusan</button>
                <button type="submit" name="action" value="publish" class="btn btn-primary px-4"><i class="bi bi-trophy-fill me-1"></i> Simpan Keputusan</button>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function() {
            let placementCount = 3;
            const totalParticipants = parseInt("{{ $competition->participant_registration->count() }}");

            const participantOption = 
                `<option value="">--- Pilih Pemenang ---</option>
                @foreach($competition->participant_registration as $competitions)
                    @if($competition->participateType === 'Individual')
                        <option value="{{ $competitions->participant->first()->teamId }}" data-name="{{ $competitions->participant->first()->name }}" data-subtext="{{ $competitions->participant->first()->icNo }}">{{ $competitions->participant->first()->name ?? '' }}</option>
                    @else
                        <option value="{{ $competitions->participant->first()->teamId }}" data-name="{{ $competitions->groupName }}" data-subtext="{{ $competitions->participant->pluck('name')->implode(', ') }}">{{ $competitions->groupName ?? '' }} - {{ $competitions->participant->pluck('name')->implode(', ') }}</option>
                    @endif
                @endforeach`;

            function getParticipant() 
            {
                return participantOption;
            }

            $('#btn-add-placement').on('click', function() {
                placementCount++;
                const rankKey = `Tempat_${placementCount}`;
                const rankLabel = `Tempat Ke-${placementCount}`;

                const newRow = `
                    <div class="p-2 border rounded mb-3 winner-group" data-type="${rankKey}">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold fs-5 mb-0">🏅 ${rankLabel}</label>
                            <button type="button" class="btn btn-sm text-danger remove-placement-group p-0"><i class="bi bi-trash fs-5"></i></button>
                        </div>

                        <div class="winner-list"></div>

                        <div class="d-flex">
                            <select name="${rankKey}" id="${rankKey}" class="form-select winner-select">
                                ${getParticipant()}
                            </select>
                            <button type="button" class="btn btn-sm ms-2 fw-bold btn-add text-nowrap"><i class="bi bi-plus-lg"></i> Tambah Pemenang</button>
                        </div>
                    </div>
                `;

                $('#extra-placements-container').append(newRow);
                updateDropdown();
            });

            $(document).on('click', '.remove-placement-group', function() {
                $(this).closest('.winner-group').remove();
                reIndexPlacement();
                updateDropdown();
            });

            function reIndexPlacement() 
            {
                let count = 3;
                
                $('#extra-placements-container .winner-group').each(function () {
                    count++;
                    const rankKey = `Tempat_${count}`;
                    const rankLabel = `Tempat Ke-${count}`;

                    $(this).attr('data-type', rankKey);
                    $(this).find('.form-label').html(`🏅 ${rankLabel}`);
                    $(this).find('select.winner-select').attr('name', rankKey).attr('id', rankKey);
                });
                placementCount = count;
            }

            function updateDropdown()
            {
                let selectValue = [];
                $('input[type="hidden"]').each(function() {
                    let val = $(this).val();

                    if (val)
                    {
                        selectValue.push(val);
                    }
                });

                $('.winner-select').each(function() {
                    let currentSelect = $(this);
                    let currentValue = currentSelect.val();

                    let otherSelect = [];

                    $('.winner-select').not(currentSelect).each(function () {
                        let val = $(this).val();

                        if (val) 
                        {
                            otherSelect.push(val);
                        }
                    });

                    currentSelect.find('option').each(function () {
                        let optionVal = $(this).val();

                        if (optionVal === "") return;

                        if (selectValue.includes(optionVal) || otherSelect.includes(optionVal) && optionVal !== currentValue)
                        {
                            $(this).prop('disabled', true);
                        }
                        else
                        {
                            $(this).prop('disabled', false);
                        } 
                    });
                });
            }

            $(document).on('change', '.winner-select', function () {
                updateDropdown();
            });

            $(document).on('click', '.btn-add', function() {
                const $group = $(this).closest('.winner-group');
                const medalType = $group.data('type');
                const $select = $group.find('.winner-select');
                const $selectOption = $select.find('option:selected');

                const value = $selectOption.val();
                const name = $selectOption.data('name');
                const subtext = $selectOption.data('subtext');

                if (value === '') return;

                const cardHtml =
                    `<div class="winner-card d-flex align-items-center justify-content-between p-2 mb-2 rounded border">
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <div class="fw-bold text-dark">${name}</div>

                                @if($competition->participateType === 'Individual')
                                    <small class="text-muted d-block">IC No: <span class="text-dark fw-semibold">${subtext}</span></small>
                                @else
                                    <small class="text-muted d-block">Team Members: <span class="text-dark fw-semibold">${subtext}</span></small>
                                @endif
                            </div>
                        </div>

                        <input type="hidden" name="winners[${medalType}][]" value="${value}">

                        <button type="button" class="btn btn-link text-danger remove-winner p-0 ms-2">
                            <i class="bi bi-trash fs-5"></i>
                        </button>
                    </div>`;

                $group.find('.winner-list').append(cardHtml);
                $select.val('');
                updateDropdown();
            });

            $(document).on('click', '.remove-winner', function() {
                $(this).closest('.winner-card').remove();
                updateDropdown();
            });
        });

        $('#updateResult').on('submit', function (e) {
            e.preventDefault();

            let form = this;
            let formData = new FormData(form);

            let submitter = e.originalEvent.submitter;
            if (submitter)
            {
                formData.append('action', submitter.value);
            }

            $.ajax ({
                url: "{{ route('competition.storeresult', $competition->id) }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                success: function (data) {
                    if (data.status === 'success')
                    {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berjaya.',
                            text: data.message,
                            confirmButtonColor: '#4f46e5'
                        }).then (() => {
                            location.reload();
                        });
                    }
                    else
                    {
                        throw new Error(data.message);
                    }
                },
                error: function (xhr, status, error)
                {
                    console.error("Error: ", error);
                    Swal.fire ({
                        icon: 'error',
                        title: 'Ralat',
                        text: 'Terdapat ralat semasa mengemaskini keputusan.',
                        confirmButtonColor: '#4f46e5'
                    });
                }
            });
        });
    </script>
</body>

</html>