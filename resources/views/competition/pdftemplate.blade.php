<!DOCTYPE html>
<html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600&display=swap" rel="stylesheet">

    <title>Sijil_ {{ $participant->name }}</title>

    <style>
        @page {
            margin: 0;
        }

        .bg-img {
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        body.landscape {
            width: 297mm;
            height: 210mm;
        }

        body.portrait {
            width: 210mm;
            height: 297mm;
        }
        
        .pdf-overlay {
            width: 100%;
            text-align: center;
            word-wrap: break-word;
            white-space: normal;
            line-height: 1.2;
        }

        .pdf-winner {
            margin-bottom: 5mm;
        }

        .pdf-participation {
            margin-bottom: 7mm;
        }

        /* Template1 */
        .pdf-container-1 {
            position: relative;
            width: 80%;
            margin: 0 auto;
            padding-top: 36mm;
            text-align: center;
        }

        .pdf-sijil1 {
            font-family: 'Droid Serif', Georgia, Times, serif;
            color: #003892;
            font-size: 32pt;
            font-weight: bold;
            margin-bottom: 7mm;
            text-transform: uppercase;
        }

        .pdf-label-1 {
            font-family: 'Droid Serif', Georgia, Times, serif;
            color: #0b0f20;
            font-size: 15pt;
        }

        .pdf-name1 {
            font-size: 15pt;
            font-weight: bold;
            color: #003892;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .pdf-ic1 {
            font-size: 15pt;
            font-weight: bold;
            color: black;
            letter-spacing: 0.2mm;
        }

        .pdf-winner1 {
            font-size: 17pt;
            font-weight: bold;
            color: black;
        }

        .pdf-title1 {
            font-size: 16pt;
            font-weight: bold;
            color: #003892  ;
        }

        .pdf-date1 {
            font-size: 15pt;
            font-weight: bold;
            color: black;
        }

        .pdf-venue1 {
            font-size: 15pt;
            font-weight: bold;
            color: black;
        }

        .pdf-footer-1 {
            position: absolute;
            bottom: 16mm;
            left: 10%;
            width: 80%;
            text-align: center;
        }

        .pdf-organizer1 {
            max-width: 30%;
            font-size: 13pt;
            font-weight: bold;
            color: #4b5563;
            margin: 0 auto 1mm auto;
        }

        .pdf-footer-1 hr {
            width: 65mm;
            margin: 0 auto 0mm auto;
            border: 0;
            border-top: 2px solid #0b0f20;
        }

        .pdf-text-5-1 {
            font-family: 'Droid Serif', Georgia, Times, serif;
            color: #0b0f20;
            font-size: 15pt;
        }

        /* Template2 */
        .pdf-container-2 {
            position: relative;
            width: 80%;
            margin: 0 auto;
            padding-top: 25mm;
            text-align: center;
        }

        .pdf-sijil2 {
            font-family: 'Droid Serif', Georgia, Times, serif;
            color: #470b0b;
            font-size: 32pt;
            font-weight: bold;
            margin-bottom: 9mm;
            text-transform: uppercase;
        }

        .pdf-label-2 {
            font-family: 'Droid Serif', Georgia, Times, serif;
            color: #470b0b;
            font-size: 15pt;
        }

        .pdf-name2 {
            font-size: 15pt;
            font-weight: bold;
            color: #875203;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .pdf-ic2 {
            font-size: 15pt;
            font-weight: bold;
            color: black;
            letter-spacing: 0.2mm;
        }

        .pdf-winner2 {
            font-size: 17pt;
            font-weight: bold;
            color: black;
        }

        .pdf-title2 {
            font-size: 16pt;
            font-weight: bold;
            color: #875203;
        }

        .pdf-date2 {
            font-size: 14pt;
            font-weight: bold;
            color: black;
        }

        .pdf-venue2 {
            font-size: 14pt;
            font-weight: bold;
            color: black;
        }

        .pdf-footer-2 {
            position: absolute;
            bottom: 17mm;
            left: 10%;
            width: 80%;
            text-align: center;
        }

        .pdf-organizer2 {
            max-width: 30%;
            font-size: 13pt;
            font-weight: bold;
            color: #875203;
            margin: 0 auto 1mm auto;
        }

        .pdf-footer-2 hr {
            width: 65mm;
            margin: 0 auto 0 auto;
            border: 0;
            border-top: 2px solid #470b0b;
        }

        .pdf-text-5-2 {
            font-family: 'Droid Serif', Georgia, Times, serif;
            color: #470b0b;
            font-size: 15pt;
        }

        /* Template3 */
        .pdf-container-3 {
            position: relative;
            width: 80%;
            margin: 0 auto;
            padding-top: 52mm;
            text-align: center;
        }

        .pdf-sijil3, .pdf-label-3, .pdf-name3, .pdf-ic3, .pdf-winner3, 
        .pdf-title3, .pdf-date3, .pdf-venue3, .pdf-organizer3, .pdf-text-5-3 {
            font-family: 'Montserrat', 'Helvetica Neue', sans-serif;
        }

        .pdf-sijil3 {
            color: #21246b;
            font-size: 32pt;
            font-weight: 500;
            margin-bottom: 16mm;
            letter-spacing: 6px;
            text-transform: uppercase;
        }

        .pdf-label-3 {
            color: #21246b;
            font-size: 15pt;
        }

        .pdf-name3 {
            font-size: 15pt;
            font-weight: bold;
            color: #21246b;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .pdf-ic3 {
            font-size: 15pt;
            font-weight: bold;
            color: black;
            letter-spacing: 0.2mm;
        }

        .pdf-winner3 {
            font-size: 17pt;
            font-weight: bold;
            color: black;
        }

        .pdf-title3 {
            font-size: 17pt;
            font-weight: bold;
            color: #21246b;
        }

        .pdf-date3 {
            font-size: 14pt;
            font-weight: bold;
            color: black;
        }

        .pdf-venue3 {
            font-size: 14pt;
            font-weight: bold;
            color: black;
        }

        .pdf-footer-3 {
            position: absolute;
            bottom: 27mm;
            left: 10%;
            width: 80%;
            text-align: center;
        }

        .pdf-organizer3 {
            max-width: 55%;
            font-size: 13pt;
            font-weight: bold;
            color: #21246b;
            margin: 0 auto 1mm auto;
        }

        .pdf-footer-3 hr {
            width: 65mm;
            margin: 0 auto 0mm auto;
            border: 0;
            border-top: 2px solid #21246b;
        }

        .pdf-text-5-3 {
            color: #21246b;
            font-size: 15pt;
        }

        /* Template4 */
        .pdf-container-4 {
            position: relative;
            width: 80%;
            margin: 0 auto;
            padding-top: 80mm;
            text-align: center;
        }

        .pdf-sijil4, .pdf-label-4, .pdf-name4, .pdf-ic4, .pdf-winner4, 
        .pdf-title4, .pdf-date4, .pdf-venue4, .pdf-organizer4, .pdf-text-5-4 {
            font-family: 'Aleo', serif;
        }

        .pdf-sijil4 {
            color: white;
            font-size: 32pt;
            font-weight: bold;
            margin-bottom: 12mm;
            text-transform: uppercase;
        }

        .pdf-label-4 {
            color: white;
            font-size: 15pt;
        }

        .pdf-name4 {
            font-size: 15pt;
            font-weight: bold;
            color: #f7b814;
            text-transform: uppercase;
            margin-bottom: 2mm;
        }

        .pdf-ic4 {
            font-size: 15pt;
            font-weight: bold;
            color: white;
            letter-spacing: 0.2mm;
        }

        .pdf-winner4 {
            font-size: 17pt;
            font-weight: bold;
            color: white;
        }

        .pdf-title4 {
            font-size: 17pt;
            font-weight: bold;
            color: #f7b814;
        }

        .pdf-date4 {
            font-size: 14pt;
            font-weight: bold;
            color: white;
        }

        .pdf-venue4 {
            font-size: 14pt;
            font-weight: bold;
            color: white;
        }

        .pdf-footer-4 {
            position: absolute;
            bottom: 40mm;
            left: 10%;
            width: 80%;
            text-align: center;
        }

        .pdf-organizer4 {
            max-width: 40%;
            font-size: 13pt;
            font-weight: bold;
            color: #f7b814;
            margin: 0 auto 1mm auto;
        }

        .pdf-footer-4 hr {
            width: 65mm;
            margin: 0 auto 0mm auto;
            border: 0;
            border-top: 2px solid white;
        }

        .pdf-text-5-4 {
            color: white;
            font-size: 15pt;
        }
    </style>
</head>
<body class="{{ $orientation }}">
    <img src="{{ public_path('assets/images/template-sijil-competition/' . $templateId . '.png') }}" class="bg-img">

    @php
        $startDate = $participant->team->team_registration->first()->competitionStart ?? null;
        $endDate = $participant->team->team_registration->first()->competitionEnd ?? null;

        if($startDate && $endDate)
        {
            $date = \Carbon\Carbon::parse($startDate)->format('d F Y') . ' - ' . \Carbon\Carbon::parse($endDate)->format('d F Y');
        }
        else if($startDate)
        {
            $date = \Carbon\Carbon::parse($startDate)->format('d F Y');
        }
        else
        {
            $date = \Carbon\Carbon::parse($endDate)->format('d F Y');
        }

        $title = $participant->team->team_registration->first()->winnerType;
        $winnerPlacement = $participant->team->team_registration->first()->pivot->winnerPlacement;
        $isWinner = !empty($winnerPlacement);
        $spacing = $isWinner ? 'pdf-winner' : 'pdf-participation';

        $sijilName = 'Sijil Pencapaian';
        $sijilDeclare = 'Sijil ini dianugerahkan kepada';
        $sijilComp = 'Dalam';
        $sijilWinner = 'Atas kejayaan mendapat';

        $number = [
            1 => 'Pertama',
            2 => 'Kedua',
            3 => 'Ketiga',
            4 => 'Keempat',
            5 => 'Kelima',
            6 => 'Keenam',
            7 => 'Ketujuh',
            8 => 'Kelapan',
            9 => 'Kesembilan',
            10 => 'Kesepuluh',
        ];

        preg_match('/\d+/', $winnerPlacement, $matches);
        $rankNum = isset($matches[0]) ? (int)$matches[0] : null;

        if($title == 'Medal')
        {
            if (stripos($winnerPlacement, 'Gold Medal') !== false) 
            {
                $winner = 'Pingat Emas';
            } 
            else if (stripos($winnerPlacement, 'Silver Medal') !== false) 
            {
                $winner = 'Pingat Perak';
            } 
            else if (stripos($winnerPlacement, 'Bronze Medal') !== false) 
            {
                $winner = 'Pingat Gangsa';
            } 
            else if ($rankNum) 
            {
                $Num = $number[$rankNum] ?? "Ke-{$rankNum}";
                $winner = "Pingat Tempat {$Num}";
            } 
            else 
            {
                $winner = $winnerPlacement;
            }
        }
        else if ($title == 'Number')
        {
            if ($rankNum && isset($number[$rankNum]))
            {
                $winner = 'Tempat ' . $number[$rankNum];
            } 
            else if ($rankNum) 
            {
                $winner = "Tempat Ke-{$rankNum}";
            } 
            else 
            {
                $winner = $winnerPlacement;
            }
        }
        else
        {
            $sijilName = 'Sijil Penyertaan';
            $sijilDeclare = 'Dengan ini diperakui bahawa';
            $sijilComp = 'Telah menyertai';
        }
    @endphp

    @if($templateId === 'Template1')
        <div class="pdf-container-1">
            <div class="pdf-overlay pdf-sijil1">{{ $sijilName }}</div>
                
            <div class="pdf-overlay pdf-label-1 {{ $spacing }}">{{ $sijilDeclare }}</div>
            <div class="pdf-overlay pdf-name1">{{ $participant->name }}</div>
            <div class="pdf-overlay pdf-ic1 {{ $spacing }}">{{ $participant->icNo }}</div>
            
            @if($title == 'Medal' || $title == 'Number')
                <div class="pdf-overlay pdf-label-1 {{ $spacing }}">{{ $sijilWinner }}</div>
                <div class="pdf-overlay pdf-winner1 {{ $spacing }}">{{ $winner }}</div>
            @endif

            <div class="pdf-overlay pdf-label-1 {{ $spacing }}">{{ $sijilComp }}</div>
            <div class="pdf-overlay pdf-title1 {{ $spacing }}">{{ $participant->team->team_registration->first()->competitionTitle }}</div>

            <div class="pdf-overlay pdf-label-1 {{ $spacing }}">Pada</div>
            <div class="pdf-overlay pdf-date1 {{ $spacing }}">{{ $date }}</div>

            <div class="pdf-overlay pdf-label-1 {{ $spacing }}">Bertempat di</div>
            <div class="pdf-overlay pdf-venue1">
                {{ $participant->team->team_registration->first()->venue }}
            </div>
        </div>
        
        <div class="pdf-footer-1">
            <div class="pdf-overlay pdf-organizer1">
                {{ $participant->team->team_registration->first()->organization->nama }}
            </div>
            <hr>
            <div class="pdf-text-5-1">Organizer Competition</div>
        </div>

    @elseif($templateId === 'Template2')
        <div class="pdf-container-2">
            <div class="pdf-overlay pdf-sijil2">{{ $sijilName }}</div>
            
            <div class="pdf-overlay pdf-label-2 {{ $spacing }}">{{ $sijilDeclare }}</div>
            <div class="pdf-overlay pdf-name2">{{ $participant->name }}</div>
            <div class="pdf-overlay pdf-ic2 {{ $spacing }}">{{ $participant->icNo }}</div>

            @if($title == 'Medal' || $title == 'Number')
                <div class="pdf-overlay pdf-label-2 {{ $spacing }}">{{ $sijilWinner }}</div>
                <div class="pdf-overlay pdf-winner2 {{ $spacing }}">{{ $winner }}</div>
            @endif

            <div class="pdf-overlay pdf-label-2 {{ $spacing }}">{{ $sijilComp }}</div>
            <div class="pdf-overlay pdf-title2 {{ $spacing }}">{{ $participant->team->team_registration->first()->competitionTitle }}</div>
            
            <div class="pdf-overlay pdf-label-2 {{ $spacing }}">Pada</div>
            <div class="pdf-overlay pdf-date2 {{ $spacing }}">{{ $date }}</div>

            <div class="pdf-overlay pdf-label-2 {{ $spacing }}">Bertempat di</div>
            <div class="pdf-overlay pdf-venue2">
                {{ $participant->team->team_registration->first()->venue }}
            </div>
        </div>

        <div class="pdf-footer-2">
            <div class="pdf-overlay pdf-organizer2">
                {{ $participant->team->team_registration->first()->organization->nama }}
            </div>
            <hr>
            <div class="pdf-text-5-2">Organizer Competition</div>
        </div>

    @elseif($templateId === 'Template3')
        <div class="pdf-container-3">
            <div class="pdf-overlay pdf-sijil3">{{ $sijilName }}</div>
                
            <div class="pdf-overlay pdf-label-3 {{ $spacing }}">{{ $sijilDeclare }}</div>
            <div class="pdf-overlay pdf-name3">{{ $participant->name }}</div>
            <div class="pdf-overlay pdf-ic3 {{ $spacing }}">{{ $participant->icNo }}</div>

            @if($title == 'Medal' || $title == 'Number')
                <div class="pdf-overlay pdf-label-3 {{ $spacing }}">{{ $sijilWinner }}</div>
                <div class="pdf-overlay pdf-winner3 {{ $spacing }}">{{ $winner }}</div>
            @endif

            <div class="pdf-overlay pdf-label-3 {{ $spacing }}">{{ $sijilComp }}</div>
            <div class="pdf-overlay pdf-title3 {{ $spacing }}">{{ $participant->team->team_registration->first()->competitionTitle }}</div>

            <div class="pdf-overlay pdf-label-3 {{ $spacing }}">Pada</div>
            <div class="pdf-overlay pdf-date3 {{ $spacing }}">{{ $date }}</div>

            <div class="pdf-overlay pdf-label-3 {{ $spacing }}">Bertempat di</div>
            <div class="pdf-overlay pdf-venue3">
                {{ $participant->team->team_registration->first()->venue }}
            </div>

        <div class="pdf-footer-3">
            <div class="pdf-overlay pdf-organizer3">
                {{ $participant->team->team_registration->first()->organization->nama }}
            </div>
            <hr>
            <div class="pdf-text-5-3">Organizer Competition</div>
        </div>

    @elseif($templateId === 'Template4')
        <div class="pdf-container-4">
            <div class="pdf-overlay pdf-sijil4">{{ $sijilName }}</div>
            
            <div class="pdf-overlay pdf-label-4 {{ $spacing }}">{{ $sijilDeclare }}</div>
            <div class="pdf-overlay pdf-name4">{{ $participant->name }}</div>
            <div class="pdf-overlay pdf-ic4 {{ $spacing }}">{{ $participant->icNo }}</div>

            @if($title == 'Medal' || $title == 'Number')
                <div class="pdf-overlay pdf-label-4 {{ $spacing }}">{{ $sijilWinner }}</div>
                <div class="pdf-overlay pdf-winner4 {{ $spacing }}">{{ $winner }}</div>
            @endif

            <div class="pdf-overlay pdf-label-4 {{ $spacing }}">{{ $sijilComp }}</div>
            <div class="pdf-overlay pdf-title4 {{ $spacing }}">{{ $participant->team->team_registration->first()->competitionTitle }}</div>

            <div class="pdf-overlay pdf-label-4 {{ $spacing }}">Pada</div>
            <div class="pdf-overlay pdf-date4 {{ $spacing }}">{{ $date }}</div>

            <div class="pdf-overlay pdf-label-4 {{ $spacing }}">Bertempat di</div>
            <div class="pdf-overlay pdf-venue4">
                {{ $participant->team->team_registration->first()->venue }}
            </div>
        </div>

        <div class="pdf-footer-4">
            <div class="pdf-organizer4">
                {{ $participant->team->team_registration->first()->organization->nama }}
            </div>
            <hr>
            <div class="pdf-text-5-4">Organizer Competition</div>
        </div>
    @endif
</body>
</html>