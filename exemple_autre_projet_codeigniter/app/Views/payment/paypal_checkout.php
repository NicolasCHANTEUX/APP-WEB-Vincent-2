<?= $this->extend('layouts/root_layout') ?>
<?= $this->section('root_content') ?>

<?php

if (!session()->has('pending_reservation')) {
    header('Location: ' . site_url('reservation'));
    exit();
}
$rawAmount = isset($amount) ? (float) $amount : 10.00;
$displayAmount = number_format($rawAmount, 2, ',', ' ');
?>

<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-4xl bg-white rounded-2xl shadow-xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
        <div class="p-8 bg-gradient-to-b from-gray-50 to-white">
            <div class="flex items-center gap-3 mb-6">
                <img src="/images/logo.webp" alt="Logo" class="h-10 w-10 object-contain"
                    onerror="this.style.display='none'" />
                <div>
                    <h3 class="text-lg font-semibold text-black"><?= trans('paypal_titre_residence') ?></h3>
                    <p class="text-sm text-gray-500"><?= trans('paypal_paiement_securise') ?></p>
                </div>
            </div>

            <div class="border border-gray-100 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600"><?= trans('paypal_description') ?></div>
                    <div class="text-sm text-gray-600"><?= trans('paypal_quantite') ?></div>
                </div>
                <div class="mt-2 flex items-center justify-between">
                    <div class="text-sm text-gray-900"><?= trans('paypal_reservation') ?></div>
                    <div class="text-sm text-gray-900">1</div>
                </div>
                <div class="mt-4 border-t pt-4 flex items-center justify-between">
                    <div class="text-base font-medium text-gray-700"><?= trans('paypal_total') ?></div>
                    <div class="text-base font-semibold text-gray-900">€ <span id="amount"
                            data-amount="<?= esc(number_format($rawAmount, 2, '.', '')) ?>"><?= esc($displayAmount) ?></span>
                    </div>
                </div>
            </div>

            <ul class="text-xs text-gray-500 space-y-2">
                <li><?= trans('paypal_sandbox_info') ?></li>
                <li><?= trans('paypal_securite_info') ?></li>
                <li><?= trans('paypal_recu_info') ?></li>
            </ul>
        </div>

        <div class="p-8 flex flex-col justify-between text-black">
            <div>
                <h2 class="text-xl font-semibold mb-1"><?= trans('paypal_payer_maintenant') ?></h2>
                <p class="text-sm text-gray-500 mb-6"><?= trans('paypal_montant_affiche') ?></p>

                <div id="paypal-area" class="mb-4">
                    <?php if (!empty($clientId)): ?>
                        <script src="https://www.paypal.com/sdk/js?client-id=<?= esc($clientId) ?>&currency=EUR"></script>
                    <?php else: ?>
                        <div class="p-3 mb-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded">
                            <?= trans('paypal_cle_manquante') ?> <code>.env</code>.</div>
                    <?php endif; ?>

                    <div id="paypal-button-container" class="mt-4"></div>
                    <div id="paypal-result" class="mt-4 text-center"></div>
                </div>
            </div>

            <div class="text-center mt-6">
                <small class="text-xs text-gray-400"><?= trans('paypal_optimise') ?></small>
            </div>
        </div>
    </div>
</div>

<div id="success-modal-overlay" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center"
    style="z-index:2147483647;">
    <div id="success-modal" class="bg-white max-w-lg w-[92%] mx-auto rounded-lg shadow-lg p-6 relative"
        style="z-index:2147483648;">
        <div class="text-center p-4">
            <div class="text-5xl text-green-600">✓</div>
            <h2 class="mt-3 mb-2 text-xl font-semibold"><?= trans('paypal_succes_titre') ?></h2>
            <p id="success-modal-message" class="text-gray-700 mb-4"><?= trans('paypal_succes_message') ?></p>
            <div class="flex gap-3 justify-center">
                <a id="success-modal-back" href="/"
                    class="inline-block px-5 py-2 rounded-md bg-gray-200 text-gray-800"><?= trans('paypal_retour_accueil') ?></a>
            </div>
        </div>
    </div>
</div>

<script>
    // Translated strings for JavaScript
    const translations = {
        successMessage: '<?= trans('choix_paiement_succes') ?>',
        errorMessage: '<?= trans('choix_paiement_echec') ?>'
    };

    (function () {
        const resultEl = document.getElementById('paypal-result');
        function show(type, msg) { if (!resultEl) return; resultEl.innerHTML = `<div class="p-3 rounded ${type === 'ok' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800'}">${msg}</div>` }

        function formatApiAmount(raw) {
            return parseFloat(raw).toFixed(2);
        }

        function initButtons() {
            if (typeof paypal === 'undefined') {
                if (document.querySelector('script[src*="paypal.com/sdk/js"]')) {
                    setTimeout(initButtons, 300);
                    return;
                }
                show('err', 'PayPal SDK non chargé. Vérifiez la configuration du client id.');
                return;
            }

            const paypalContainer = document.getElementById('paypal-button-container');
            const amountEl = document.getElementById('amount');
            const rawAmount = amountEl && amountEl.dataset ? amountEl.dataset.amount : '10.00';
            const apiAmount = formatApiAmount(rawAmount);

            function setLoading(on) {
                if (!paypalContainer) return;
                paypalContainer.style.opacity = on ? '0.6' : '';
                if (on) paypalContainer.classList.add('pointer-events-none'); else paypalContainer.classList.remove('pointer-events-none');
            }

            function showSuccessModal(message) {
                const overlay = document.getElementById('success-modal-overlay');
                const modal = document.getElementById('success-modal');
                const msgEl = document.getElementById('success-modal-message');

                if (msgEl) msgEl.textContent = message || translations.successMessage;
                if (overlay) overlay.classList.remove('hidden');

                setTimeout(() => {
                    window.location.href = '/';
                }, 6000);
            }

            const overlayEl = document.getElementById('success-modal-overlay');
            if (overlayEl) overlayEl.addEventListener('click', function (e) {
                if (e.target === overlayEl) {
                    window.location.href = '/';
                }
            });

            paypal.Buttons({
                style: { layout: 'vertical', color: 'blue', shape: 'rect', label: 'paypal' },
                createOrder: function (data, actions) {
                    setLoading(true);
                    show('ok', 'Création de la commande...');
                    return fetch('/paypal/create-order', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ amount: apiAmount })
                    }).then(r => r.json()).then(data => {
                        setLoading(false);
                        if (data.orderID) return data.orderID;
                        throw new Error(data.error || translations.errorMessage);
                    }).catch(err => { setLoading(false); show('err', 'Erreur création commande: ' + err.message); throw err; });
                },
                onApprove: function (data, actions) {
                    setLoading(true);
                    show('ok', 'Paiement approuvé, capture en cours...');
                    return fetch('/paypal/capture-order', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: new URLSearchParams({ orderID: data.orderID })
                    }).then(r => r.json()).then(details => {
                        setLoading(false);
                        const capture = details.result?.purchase_units?.[0]?.payments?.captures?.[0] || details.result;
                        const status = capture?.status || details.status || (details.result && details.result.status) || null;
                        if (status === 'COMPLETED') {
                            const paidAmount = amountEl ? amountEl.textContent.trim() : apiAmount;
                            const orderId = data.orderID || (details.id || '');
                            showSuccessModal(`Paiement de ${paidAmount} confirmé. Référence: ${orderId}`);
                            show('ok', 'Paiement réussi.');
                        } else {
                            show('err', 'Paiement non confirmé. Statut: ' + (status || 'inconnu'));
                        }
                    }).catch(err => { setLoading(false); show('err', 'Erreur capture: ' + err.message); console.error(err); });
                },
                onError: function (err) { setLoading(false); show('err',(err && err.toString())); console.error(err); }
            }).render('#paypal-button-container');
        }

        initButtons();
    })();
</script>

<?= $this->endSection() ?>