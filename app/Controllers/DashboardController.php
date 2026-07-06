<?php

namespace App\Controllers;

use App\Models\LogModel;

class DashboardController extends BaseController
{
    protected $helpers = ['url', 'vite'];

    // Liste des devis du tiers connecté (cache par user)
    public function proposals(): string
    {
        $dolibarr = service('dolibarr');
        $partyId  = session()->get('party_id');

        $proposals = [];

        if ($partyId) {
            $cacheKey  = 'proposals_' . session()->get('user_id');
            $proposals = cache($cacheKey);

            if ($proposals === null) {
                $result = $dolibarr->getProposals([
                    'thirdparty_ids' => $partyId,
                    'limit'          => 50,
                    'sortfield'      => 't.rowid',
                    'sortorder'      => 'DESC',
                ]);

                $proposals = isset($result['error']) ? [] : $result;
                if (! isset($result['error'])) {
                    cache()->save($cacheKey, $proposals, (int) cfg('time_cache', 5));
                }
            }
        }

        return view('dashboard/proposals', ['proposals' => $proposals]);
    }

    // Téléchargement PDF d'un devis (statuts 1 et 2 uniquement, appartenance vérifiée)
    public function downloadProposal(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $dolibarr = service('dolibarr');
        $partyId  = session()->get('party_id');
        $proposal = $dolibarr->getProposal($id);

        if (isset($proposal['error'])) {
            return redirect()->to('dashboard/proposals')->with('error', 'Devis introuvable.');
        }

        if ((string)($proposal['socid'] ?? '') !== (string)$partyId) {
            return redirect()->to('dashboard/proposals')->with('error', 'Accès refusé.');
        }

        if (! in_array((int)($proposal['statut'] ?? 0), [1, 2])) {
            return redirect()->to('dashboard/proposals')->with('error', 'Ce devis n\'est pas disponible au téléchargement.');
        }

        $originalFile = preg_replace('#^propale/#', '', (string)($proposal['last_main_doc'] ?? ''));

        if (! $originalFile) {
            return redirect()->to('dashboard/proposals')->with('error', 'Aucun document PDF disponible.');
        }

        $result = $dolibarr->downloadDocument('propale', $originalFile);

        if (isset($result['error']) || empty($result['content'])) {
            return redirect()->to('dashboard/proposals')->with('error', 'Impossible de télécharger le document.');
        }

        $pdf      = base64_decode($result['content']);
        $filename = basename($originalFile);

        model(LogModel::class)->record((int) session()->get('user_id'), 'download_proposal', [
            'id'  => $id,
            'ref' => $proposal['ref'] ?? null,
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Content-Length', (string)strlen($pdf))
            ->setBody($pdf);
    }

    // Liste des commandes du tiers connecté (cache par user)
    public function orders(): string
    {
        $dolibarr = service('dolibarr');
        $partyId  = session()->get('party_id');
        $orders   = [];

        if ($partyId) {
            $cacheKey = 'orders_' . session()->get('user_id');
            $orders   = cache($cacheKey);

            if ($orders === null) {
                $result = $dolibarr->getOrders([
                    'thirdparty_ids' => $partyId,
                    'limit'          => 50,
                    'sortfield'      => 't.rowid',
                    'sortorder'      => 'DESC',
                ]);

                $orders = isset($result['error']) ? [] : $result;

                if (! isset($result['error']) && cfg('certificatsclients_enabled', false) && $dolibarr->hasModule('certificatsclients')) {
                    foreach ($orders as &$order) {
                        $certificates = $dolibarr->getOrderCertificates((int) $order['id']);
                        $order['certificates'] = isset($certificates['error']) ? [] : $certificates;
                    }
                    unset($order);
                }

                if (! isset($result['error']) && cfg('expedition_enabled', false) && $dolibarr->hasModule('expedition')) {
                    foreach ($orders as &$order) {
                        $shipments = $dolibarr->getOrderShipments((int) $order['id']);
                        $order['shipments'] = isset($shipments['error']) ? [] : $shipments;
                    }
                    unset($order);
                }

                if (! isset($result['error'])) {
                    cache()->save($cacheKey, $orders, (int) cfg('time_cache', 5));
                }
            }
        }

        return view('dashboard/orders', ['orders' => $orders]);
    }

    // Téléchargement PDF d'une commande (statuts 1, 2 et 3 uniquement, appartenance vérifiée)
    public function downloadOrder(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $dolibarr = service('dolibarr');
        $partyId  = session()->get('party_id');
        $order    = $dolibarr->getOrder($id);

        if (isset($order['error'])) {
            return redirect()->to('dashboard/orders')->with('error', 'Commande introuvable.');
        }

        if ((string)($order['socid'] ?? '') !== (string)$partyId) {
            return redirect()->to('dashboard/orders')->with('error', 'Accès refusé.');
        }

        if (! in_array((int)($order['statut'] ?? 0), [1, 2, 3])) {
            return redirect()->to('dashboard/orders')->with('error', 'Cette commande n\'est pas disponible au téléchargement.');
        }

        $originalFile = preg_replace('#^commande/#', '', (string)($order['last_main_doc'] ?? ''));

        if (! $originalFile) {
            return redirect()->to('dashboard/orders')->with('error', 'Aucun document PDF disponible.');
        }

        $result = $dolibarr->downloadDocument('commande', $originalFile);

        if ((isset($result['error']) || empty($result['content'])) && cfg('rebuild_pdf_on_failure', false)) {
            $result = $dolibarr->buildDocument('commande', $originalFile);
        }

        if (isset($result['error']) || empty($result['content'])) {
            return redirect()->to('dashboard/orders')->with('error', 'Impossible de télécharger le document.');
        }

        $pdf = base64_decode($result['content']);

        model(LogModel::class)->record((int) session()->get('user_id'), 'download_order', [
            'id'  => $id,
            'ref' => $order['ref'] ?? null,
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . basename($originalFile) . '"')
            ->setHeader('Content-Length', (string)strlen($pdf))
            ->setBody($pdf);
    }

    // Téléchargement PDF d'une expédition liée à une commande (appartenance vérifiée)
    public function downloadShipment(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $dolibarr = service('dolibarr');
        $partyId  = session()->get('party_id');

        if (! cfg('expedition_enabled', false) || ! $dolibarr->hasModule('expedition')) {
            return redirect()->to('dashboard/orders')->with('error', 'Fonctionnalité indisponible.');
        }

        $shipment = $dolibarr->getShipment($id);

        if (isset($shipment['error'])) {
            return redirect()->to('dashboard/orders')->with('error', 'Expédition introuvable.');
        }

        if ((string)($shipment['socid'] ?? '') !== (string)$partyId) {
            return redirect()->to('dashboard/orders')->with('error', 'Accès refusé.');
        }

        if ((int)($shipment['statut'] ?? 0) === 0) {
            return redirect()->to('dashboard/orders')->with('error', 'Cette expédition n\'est pas disponible au téléchargement.');
        }

        $ref          = (string)($shipment['ref'] ?? '');
        $originalFile = ! empty($shipment['last_main_doc'])
            ? preg_replace('#^expedition/#', '', (string)$shipment['last_main_doc'])
            : $ref . '/' . $ref . '.pdf';

        $result = $dolibarr->downloadDocument('expedition', $originalFile);

        if ((isset($result['error']) || empty($result['content'])) && cfg('rebuild_shipment_pdf_on_failure', false)) {
            $result = $dolibarr->buildDocument('expedition', $originalFile);
        }

        if (isset($result['error']) || empty($result['content'])) {
            return redirect()->to('dashboard/orders')->with('error', 'Impossible de télécharger le document.');
        }

        $pdf = base64_decode($result['content']);

        model(LogModel::class)->record((int) session()->get('user_id'), 'download_shipment', [
            'id'  => $id,
            'ref' => $ref,
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . basename($originalFile) . '"')
            ->setHeader('Content-Length', (string)strlen($pdf))
            ->setBody($pdf);
    }

    // Téléchargement PDF d'un certificat lié à une commande (appartenance vérifiée)
    public function downloadCertificate(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $dolibarr = service('dolibarr');
        $partyId  = session()->get('party_id');

        if (! cfg('certificatsclients_enabled', false) || ! $dolibarr->hasModule('certificatsclients')) {
            return redirect()->to('dashboard/orders')->with('error', 'Fonctionnalité indisponible.');
        }

        $certificate = $dolibarr->getCertificate($id);

        if (isset($certificate['error'])) {
            return redirect()->to('dashboard/orders')->with('error', 'Certificat introuvable.');
        }

        $order = $dolibarr->getOrder((int) $certificate['order_id']);

        if (isset($order['error']) || (string)($order['socid'] ?? '') !== (string)$partyId) {
            return redirect()->to('dashboard/orders')->with('error', 'Accès refusé.');
        }

        $result = $dolibarr->downloadCertificate($id);

        if (isset($result['error']) || empty($result['content'])) {
            return redirect()->to('dashboard/orders')->with('error', 'Impossible de télécharger le document.');
        }

        $pdf      = base64_decode($result['content']);
        $filename = $result['filename'] ?? $certificate['filename'] ?? 'certificat.pdf';

        model(LogModel::class)->record((int) session()->get('user_id'), 'download_certificate', [
            'id'       => $id,
            'order_id' => $certificate['order_id'] ?? null,
        ]);

        return $this->response
            ->setHeader('Content-Type', $result['content-type'] ?? 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . basename($filename) . '"')
            ->setHeader('Content-Length', (string)strlen($pdf))
            ->setBody($pdf);
    }

    // Liste des factures du tiers connecté (cache par user)
    public function invoices(): string
    {
        $dolibarr = service('dolibarr');
        $partyId  = session()->get('party_id');
        $invoices = [];

        if ($partyId) {
            $cacheKey = 'invoices_' . session()->get('user_id');
            $invoices = cache($cacheKey);

            if ($invoices === null) {
                $result = $dolibarr->getInvoices([
                    'thirdparty_ids' => $partyId,
                    'limit'          => 50,
                    'sortfield'      => 't.rowid',
                    'sortorder'      => 'DESC',
                ]);

                $invoices = isset($result['error']) ? [] : $result;
                if (! isset($result['error'])) {
                    cache()->save($cacheKey, $invoices, (int) cfg('time_cache', 5));
                }
            }
        }

        return view('dashboard/invoices', ['invoices' => $invoices]);
    }

    // Téléchargement PDF d'une facture (statuts 1 et 2 uniquement, appartenance vérifiée)
    public function downloadInvoice(int $id): \CodeIgniter\HTTP\Response|\CodeIgniter\HTTP\RedirectResponse
    {
        $dolibarr = service('dolibarr');
        $partyId  = session()->get('party_id');
        $invoice  = $dolibarr->getInvoice($id);

        if (isset($invoice['error'])) {
            return redirect()->to('dashboard/invoices')->with('error', 'Facture introuvable.');
        }

        if ((string)($invoice['socid'] ?? '') !== (string)$partyId) {
            return redirect()->to('dashboard/invoices')->with('error', 'Accès refusé.');
        }

        if (! in_array((int)($invoice['statut'] ?? 0), [1, 2])) {
            return redirect()->to('dashboard/invoices')->with('error', 'Cette facture n\'est pas disponible au téléchargement.');
        }

        $originalFile = preg_replace('#^facture/#', '', (string)($invoice['last_main_doc'] ?? ''));

        if (! $originalFile) {
            return redirect()->to('dashboard/invoices')->with('error', 'Aucun document PDF disponible.');
        }

        $result = $dolibarr->downloadDocument('facture', $originalFile);

        if (isset($result['error']) || empty($result['content'])) {
            return redirect()->to('dashboard/invoices')->with('error', 'Impossible de télécharger le document.');
        }

        $pdf = base64_decode($result['content']);

        model(LogModel::class)->record((int) session()->get('user_id'), 'download_invoice', [
            'id'  => $id,
            'ref' => $invoice['ref'] ?? null,
        ]);

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . basename($originalFile) . '"')
            ->setHeader('Content-Length', (string)strlen($pdf))
            ->setBody($pdf);
    }

    // Tableau de bord : résumé des 5 derniers documents + activité récente (cache par user)
    public function index(): string
    {
        $dolibarr = service('dolibarr');
        $partyId  = session()->get('party_id');

        $invoices  = [];
        $proposals = [];
        $orders    = [];

        if ($partyId) {
            $params   = ['thirdparty_ids' => $partyId, 'limit' => 5, 'sortfield' => 't.rowid', 'sortorder' => 'DESC'];
            $cacheKey = 'dashboard_' . session()->get('user_id');
            $cached   = cache($cacheKey);

            if ($cached) {
                ['invoices' => $invoices, 'proposals' => $proposals, 'orders' => $orders] = $cached;
            } else {
                $invoices  = $dolibarr->getInvoices($params);
                $proposals = $dolibarr->getProposals($params);
                $orders    = $dolibarr->getOrders($params);

                $hasError = isset($invoices['error']) || isset($proposals['error']) || isset($orders['error']);

                if (isset($invoices['error']))  $invoices  = [];
                if (isset($proposals['error'])) $proposals = [];
                if (isset($orders['error']))    $orders    = [];

                if (! cfg('show_drafts', false)) {
                    $proposals = array_values(array_filter($proposals, fn($p) => (int)($p['statut'] ?? 0) !== 0));
                }

                if (! $hasError) {
                    cache()->save($cacheKey, compact('invoices', 'proposals', 'orders'), (int) cfg('time_cache', 5));
                }
            }
        }

        $recentLogs = model(LogModel::class)->getForUser(session()->get('user_id'), 10);

        return view('dashboard/index', [
            'invoices'   => $invoices,
            'proposals'  => $proposals,
            'orders'     => $orders,
            'recentLogs' => $recentLogs,
        ]);
    }

    // Affiche les informations de la société (cache par user)
    public function company(): string
    {
        $dolibarr  = service('dolibarr');
        $partyId   = (int) session()->get('party_id');
        $cacheKey  = 'company_' . session()->get('user_id');
        $company   = cache($cacheKey);

        if ($company === null) {
            $result  = $dolibarr->getThirdparty($partyId);
            $company = isset($result['error']) ? [] : $result;
            if (! isset($result['error'])) {
                cache()->save($cacheKey, $company, (int) cfg('time_cache', 5));
            }
        }

        return view('dashboard/company', ['company' => $company]);
    }

    // Met à jour les champs éditables de la société via Dolibarr API
    public function updateCompany(): \CodeIgniter\HTTP\RedirectResponse
    {
        $dolibarr = service('dolibarr');
        $partyId  = (int) session()->get('party_id');

        $fields = ['name', 'name_alias', 'phone', 'phone_mobile', 'address', 'zip', 'town', 'url'];
        $data   = [];

        foreach ($fields as $field) {
            $data[$field] = $this->request->getPost($field) ?? '';
        }

        if (! empty($data['url'])) {
            $data['url'] = 'https://' . ltrim($data['url'], '/');
        }

        $result = $dolibarr->updateThirdparty($partyId, $data);

        if (isset($result['error'])) {
            return redirect()->to('dashboard/company')->with('error', 'Erreur lors de la mise à jour : ' . $result['error']);
        }

        cache()->delete('company_' . session()->get('user_id'));

        model(LogModel::class)->record((int) session()->get('user_id'), 'update_company', $data);

        return redirect()->to('dashboard/company')->with('success', 'Informations mises à jour.');
    }

    // Vérifie un N° TVA via VIES — retourne JSON (GET, pas de modification)
    public function validateTva(): \CodeIgniter\HTTP\ResponseInterface
    {
        $tva    = strtoupper(trim($this->request->getGet('tva') ?? ''));
        $vies   = new \App\Libraries\ViesApi();
        $result = $vies->validate($tva);

        return $this->response->setJSON($result);
    }

    // Enregistre le N° TVA après re-validation serveur
    public function updateTva(): \CodeIgniter\HTTP\RedirectResponse
    {
        $dolibarr = service('dolibarr');
        $partyId  = (int) session()->get('party_id');
        $tva      = strtoupper(trim($this->request->getPost('tva_intra') ?? ''));

        if (empty($tva)) {
            return redirect()->to('dashboard/company')->with('error', 'N° TVA vide.');
        }

        $vies  = new \App\Libraries\ViesApi();
        $check = $vies->validate($tva);

        if (! $check['valid']) {
            return redirect()->to('dashboard/company')->with('error', 'N° TVA invalide : ' . $check['error']);
        }

        $payload = ['tva_intra' => $tva];

        $name = trim($this->request->getPost('name') ?? '');
        if (! empty($name)) {
            $payload['name'] = $name;
        }

        $result = $dolibarr->updateThirdparty($partyId, $payload);

        if (isset($result['error'])) {
            return redirect()->to('dashboard/company')->with('error', 'Erreur lors de la mise à jour : ' . $result['error']);
        }

        cache()->delete('company_' . session()->get('user_id'));

        model(LogModel::class)->record((int) session()->get('user_id'), 'update_tva', ['tva_intra' => $tva]);

        $successMsg = ! empty($name)
            ? 'N° TVA et raison sociale mis à jour.'
            : 'N° TVA mis à jour.';

        return redirect()->to('dashboard/company')->with('success', $successMsg);
    }
}
