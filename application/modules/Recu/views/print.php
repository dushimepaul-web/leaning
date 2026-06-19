<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reçu N° <?= $recu['numero_recu'] ?></title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Segoe UI', Arial, sans-serif; color: #333; background: #f5f5f5; padding: 20px; }
  .receipt { max-width: 800px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
  .header { text-align: center; border-bottom: 2px solid #1a56db; padding-bottom: 15px; margin-bottom: 20px; }
  .header h1 { color: #1a56db; font-size: 24px; margin-bottom: 5px; }
  .header .school { font-size: 18px; font-weight: 600; }
  .header .sub { font-size: 13px; color: #666; }
  .receipt-title { text-align: center; font-size: 18px; font-weight: 700; margin: 15px 0; text-transform: uppercase; letter-spacing: 2px; }
  .receipt-no { text-align: right; font-size: 14px; font-weight: 600; margin-bottom: 20px; }
  .section { margin-bottom: 15px; }
  .section h3 { font-size: 14px; color: #1a56db; border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; text-transform: uppercase; }
  .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px 20px; }
  .info-item { display: flex; }
  .info-item .label { font-weight: 600; width: 130px; font-size: 13px; }
  .info-item .value { font-size: 13px; }
  table { width: 100%; border-collapse: collapse; margin: 15px 0; }
  table th { background: #1a56db; color: #fff; padding: 8px 10px; text-align: left; font-size: 12px; text-transform: uppercase; }
  table td { padding: 8px 10px; border-bottom: 1px solid #eee; font-size: 13px; }
  table .total td { font-weight: 700; font-size: 15px; border-top: 2px solid #1a56db; }
  .footer { margin-top: 30px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
  .signature { text-align: center; }
  .signature .line { margin-top: 50px; border-top: 1px solid #333; padding-top: 5px; font-size: 12px; }
  .stamp { text-align: center; font-size: 12px; color: #999; margin-top: 20px; border: 1px dashed #ccc; padding: 10px; }
  .print-btn { text-align: center; margin: 20px 0; }
  .print-btn button { background: #1a56db; color: #fff; border: none; padding: 10px 30px; font-size: 16px; border-radius: 6px; cursor: pointer; }
  .print-btn button:hover { background: #1e40af; }
  @media print {
    body { background: #fff; padding: 0; }
    .receipt { box-shadow: none; border-radius: 0; max-width: 100%; }
    .print-btn { display: none; }
    @page { margin: 10mm; }
  }
</style>
</head>
<body>
  <div class="print-btn"><button onclick="window.print()">Imprimer le reçu</button></div>
  <div class="receipt">
    <div class="header">
      <div class="school"><?= htmlspecialchars($ecole['nom'] ?? 'VIP SCHOOL') ?></div>
      <div class="sub"><?= htmlspecialchars($ecole['adresse'] ?? '') ?></div>
      <div class="sub"><?= htmlspecialchars($ecole['telephone'] ?? '') ?> | <?= htmlspecialchars($ecole['email'] ?? '') ?></div>
    </div>
    <div class="receipt-title">Reçu de Paiement</div>
    <div class="receipt-no">N° <?= htmlspecialchars($recu['numero_recu']) ?></div>

    <div class="section">
      <h3>Informations de l'étudiant</h3>
      <div class="info-grid">
        <div class="info-item"><span class="label">Nom complet :</span><span class="value"><?= htmlspecialchars($recu['etudiant_nom'] ?? '-') ?></span></div>
        <div class="info-item"><span class="label">Matricule :</span><span class="value"><?= htmlspecialchars($recu['matricule'] ?? '-') ?></span></div>
        <div class="info-item"><span class="label">Classe :</span><span class="value"><?= htmlspecialchars($recu['classe_libelle'] ?? '-') ?></span></div>
        <div class="info-item"><span class="label">Année scolaire :</span><span class="value"><?= htmlspecialchars($recu['annee_libelle'] ?? '-') ?></span></div>
      </div>
    </div>

    <div class="section">
      <h3>Détails du paiement</h3>
      <table>
        <thead>
          <tr><th>Type de frais</th><th>Mode de paiement</th><th>Référence</th><th>Montant</th></tr>
        </thead>
        <tbody>
          <?php foreach ($paiements as $p): ?>
          <tr>
            <td><?= htmlspecialchars($p['type_frais'] ?? '-') ?></td>
            <td><?= htmlspecialchars(ucfirst($p['mode_paiement'] ?? '-')) ?></td>
            <td><?= htmlspecialchars($p['reference'] ?? '-') ?></td>
            <td><?= number_format($p['montant'], 2) ?> FC</td>
          </tr>
          <?php endforeach; ?>
          <tr class="total">
            <td colspan="3" style="text-align:right;">Total :</td>
            <td><?= number_format($recu['montant_total'], 2) ?> FC</td>
          </tr>
        </tbody>
      </table>
      <div class="info-grid">
        <div class="info-item"><span class="label">Date de paiement :</span><span class="value"><?= date('d/m/Y', strtotime($recu['date_edition'] ?? 'now')) ?></span></div>
        <div class="info-item"><span class="label">Émis par :</span><span class="value"><?= htmlspecialchars($recu['utilisateur_nom'] ?? '-') ?></span></div>
      </div>
    </div>

    <div class="footer">
      <div class="signature">
        <div class="line">Signature du caissier</div>
      </div>
      <div class="signature">
        <div class="line">Signature du bénéficiaire</div>
      </div>
    </div>

    <div class="stamp">
      Document généré automatiquement - <?= date('d/m/Y H:i') ?><br>
      Ce reçu atteste du paiement effectué et tient lieu de justificatif.
    </div>
  </div>
  <div class="print-btn"><button onclick="window.print()">Imprimer le reçu</button></div>
  <script>window.onload = function() { window.print(); };</script>
</body>
</html>
