<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat - <?= htmlspecialchars($student->fullname) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4 landscape;
            margin: 0;
        }
        body {
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            font-family: 'Georgia', 'Times New Roman', serif;
        }
        .certificate {
            width: 1050px;
            height: 740px;
            background: #fff;
            position: relative;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
            overflow: hidden;
        }
        .certificate-border {
            position: absolute;
            top: 20px; left: 20px; right: 20px; bottom: 20px;
            border: 3px double #1a3c6e;
            pointer-events: none;
        }
        .certificate-inner {
            position: absolute;
            top: 40px; left: 40px; right: 40px; bottom: 40px;
            border: 1px solid #d4af37;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .gold-line {
            width: 200px;
            height: 3px;
            background: linear-gradient(90deg, transparent, #d4af37, transparent);
            margin: 15px auto;
        }
        .cert-title {
            font-size: 42px;
            font-weight: 700;
            color: #1a3c6e;
            letter-spacing: 8px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .cert-subtitle {
            font-size: 16px;
            color: #6c757d;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        .cert-present {
            font-size: 18px;
            color: #6c757d;
            font-style: italic;
            margin: 20px 0 10px;
        }
        .cert-name {
            font-size: 48px;
            font-weight: 700;
            color: #1a3c6e;
            margin: 5px 0;
            font-family: 'Georgia', serif;
        }
        .cert-course {
            font-size: 20px;
            color: #333;
            margin: 15px 0 5px;
        }
        .cert-course-name {
            font-size: 28px;
            font-weight: 700;
            color: #d4af37;
        }
        .cert-date {
            font-size: 15px;
            color: #6c757d;
            margin-top: 20px;
        }
        .cert-footer {
            display: flex;
            justify-content: space-between;
            width: 80%;
            margin-top: 35px;
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .cert-signature {
            text-align: center;
        }
        .cert-signature .line {
            width: 200px;
            height: 1px;
            background: #333;
            margin-bottom: 5px;
        }
        .cert-signature small {
            color: #6c757d;
            font-size: 12px;
        }
        .cert-number {
            position: absolute;
            bottom: 55px;
            right: 55px;
            font-size: 11px;
            color: #adb5bd;
        }
        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .corner-icon {
            position: absolute;
            font-size: 60px;
            opacity: 0.08;
            color: #1a3c6e;
        }
        .corner-tl { top: 50px; left: 50px; }
        .corner-tr { top: 50px; right: 50px; }
        .corner-bl { bottom: 50px; left: 50px; }
        .corner-br { bottom: 50px; right: 50px; }

        @media print {
            body { background: #fff; }
            .certificate { box-shadow: none; }
            .print-btn { display: none !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="certificate">
    <div class="certificate-border"></div>
    <div class="corner-icon corner-tl">&#9670;</div>
    <div class="corner-icon corner-tr">&#9670;</div>
    <div class="corner-icon corner-bl">&#9670;</div>
    <div class="corner-icon corner-br">&#9670;</div>

    <div class="certificate-inner">
        <div class="cert-title">Certificat</div>
        <div class="gold-line"></div>
        <div class="cert-subtitle">de réussite</div>
        <div class="gold-line"></div>

        <div class="cert-present">Ce certificat est décerné à</div>
        <div class="cert-name"><?= htmlspecialchars($student->fullname) ?></div>

        <div class="cert-course">Pour avoir suivi et terminé avec succès la formation</div>
        <div class="cert-course-name"><?= htmlspecialchars($student->nom_course) ?></div>

        <div class="cert-date">
            Formation du <?= date('d/m/Y', strtotime($student->date_debut)) ?> 
            au <?= date('d/m/Y', strtotime($student->date_defin)) ?>
            <br>
            Délivré le <?= date('d/m/Y') ?>
        </div>

        <div class="cert-footer">
            <div class="cert-signature">
                <div class="line"></div>
                <small>Directeur de <?= htmlspecialchars($site_name) ?></small>
            </div>
            <div class="cert-signature">
                <div class="line"></div>
                <small>Responsable pédagogique</small>
            </div>
        </div>
    </div>

    <div class="cert-number">Certificat N° <?= htmlspecialchars($certificate_no) ?></div>
</div>

<button class="print-btn btn btn-primary btn-lg shadow rounded-pill px-4 no-print" onclick="window.print()">
    <i class="bi bi-printer"></i> Imprimer / PDF
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
