<?php

namespace App\Libraries;

class DolibarrApi
{
    private string $baseUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->baseUrl  = rtrim(cfg('dolibarr_api_url', ''), '/') . '/api/index.php';
        $this->apiToken = cfg('dolibarr_api_token', '');
    }

    // -------------------------------------------------------------------------
    // Tiers (Thirdparties)
    // -------------------------------------------------------------------------

    public function getThirdparties(array $params = []): array
    {
        return $this->request('GET', '/thirdparties', $params);
    }

    public function getThirdparty(int $id): array
    {
        return $this->request('GET', "/thirdparties/{$id}");
    }

    public function createThirdparty(array $data): array
    {
        return $this->request('POST', '/thirdparties', $data);
    }

    public function updateThirdparty(int $id, array $data): array
    {
        return $this->request('PUT', "/thirdparties/{$id}", $data);
    }

    public function deleteThirdparty(int $id): array
    {
        return $this->request('DELETE', "/thirdparties/{$id}");
    }

    public function getThirdpartyByEmail(string $email): array
    {
        return $this->request('GET', "/thirdparties/email/{$email}");
    }

    // -------------------------------------------------------------------------
    // Factures (Invoices)
    // -------------------------------------------------------------------------

    public function getInvoices(array $params = []): array
    {
        return $this->request('GET', '/invoices', $params);
    }

    public function getInvoice(int $id): array
    {
        return $this->request('GET', "/invoices/{$id}");
    }

    public function createInvoice(array $data): array
    {
        return $this->request('POST', '/invoices', $data);
    }

    public function updateInvoice(int $id, array $data): array
    {
        return $this->request('PUT', "/invoices/{$id}", $data);
    }

    public function deleteInvoice(int $id): array
    {
        return $this->request('DELETE', "/invoices/{$id}");
    }

    // -------------------------------------------------------------------------
    // Contacts
    // -------------------------------------------------------------------------

    public function getContacts(array $params = []): array
    {
        return $this->request('GET', '/contacts', $params);
    }

    public function getContact(int $id): array
    {
        return $this->request('GET', "/contacts/{$id}");
    }

    public function getContactByEmail(string $email): array
    {
        return $this->request('GET', "/contacts/email/{$email}");
    }

    public function createContact(array $data): array
    {
        return $this->request('POST', '/contacts', $data);
    }

    public function updateContact(int $id, array $data): array
    {
        return $this->request('PUT', "/contacts/{$id}", $data);
    }

    public function deleteContact(int $id): array
    {
        return $this->request('DELETE', "/contacts/{$id}");
    }

    // -------------------------------------------------------------------------
    // Agenda
    // -------------------------------------------------------------------------

    public function getAgendaEvents(array $params = []): array
    {
        return $this->request('GET', '/agendaevents', $params);
    }

    public function getAgendaEvent(int $id): array
    {
        return $this->request('GET', "/agendaevents/{$id}");
    }

    public function createAgendaEvent(array $data): array
    {
        return $this->request('POST', '/agendaevents', $data);
    }

    // -------------------------------------------------------------------------
    // Devis (Proposals)
    // -------------------------------------------------------------------------

    public function getProposals(array $params = []): array
    {
        return $this->request('GET', '/proposals', $params);
    }

    public function getProposal(int $id): array
    {
        return $this->request('GET', "/proposals/{$id}");
    }

    public function createProposal(array $data): array
    {
        return $this->request('POST', '/proposals', $data);
    }

    public function updateProposal(int $id, array $data): array
    {
        return $this->request('PUT', "/proposals/{$id}", $data);
    }

    public function deleteProposal(int $id): array
    {
        return $this->request('DELETE', "/proposals/{$id}");
    }

    // -------------------------------------------------------------------------
    // Commandes (Orders)
    // -------------------------------------------------------------------------

    public function getOrders(array $params = []): array
    {
        return $this->request('GET', '/orders', $params);
    }

    public function getOrder(int $id): array
    {
        return $this->request('GET', "/orders/{$id}");
    }

    public function createOrder(array $data): array
    {
        return $this->request('POST', '/orders', $data);
    }

    public function updateOrder(int $id, array $data): array
    {
        return $this->request('PUT', "/orders/{$id}", $data);
    }

    public function deleteOrder(int $id): array
    {
        return $this->request('DELETE', "/orders/{$id}");
    }

    // -------------------------------------------------------------------------
    // Documents
    // -------------------------------------------------------------------------

    public function getDocuments(array $params = []): array
    {
        return $this->request('GET', '/documents', $params);
    }

    public function downloadDocument(string $modulepart, string $original_file): array
    {
        return $this->request('GET', '/documents/download', [
            'modulepart'    => $modulepart,
            'original_file' => $original_file,
        ]);
    }

    public function uploadDocument(array $data): array
    {
        return $this->request('POST', '/documents/upload', $data);
    }

    public function buildDocument(string $modulepart, string $originalFile): array
    {
        return $this->request('PUT', '/documents/builddoc', [
            'modulepart'    => $modulepart,
            'original_file' => $originalFile,
        ]);
    }

    // -------------------------------------------------------------------------
    // Statut de l'instance
    // -------------------------------------------------------------------------

    public function getStatus(): array
    {
        return $this->request('GET', '/status');
    }

    // -------------------------------------------------------------------------
    // Tickets
    // -------------------------------------------------------------------------

    public function getTickets(array $params = []): array
    {
        return $this->request('GET', '/tickets', $params);
    }

    public function getTicket(int $id): array
    {
        return $this->request('GET', "/tickets/{$id}");
    }

    public function getTicketByRef(string $ref): array
    {
        return $this->request('GET', "/tickets/ref/{$ref}");
    }

    public function createTicket(array $data): array
    {
        return $this->request('POST', '/tickets', $data);
    }

    public function updateTicket(int $id, array $data): array
    {
        return $this->request('PUT', "/tickets/{$id}", $data);
    }

    public function deleteTicket(int $id): array
    {
        return $this->request('DELETE', "/tickets/{$id}");
    }

    // -------------------------------------------------------------------------
    // Expéditions (Shipments)
    // -------------------------------------------------------------------------

    public function getShipments(array $params = []): array
    {
        return $this->request('GET', '/shipments', $params);
    }

    public function getShipment(int $id): array
    {
        return $this->request('GET', "/shipments/{$id}");
    }

    public function getOrderShipments(int $orderId): array
    {
        return $this->request('GET', "/orders/{$orderId}/shipment");
    }

    public function createOrderShipment(int $orderId, int $warehouseId): array
    {
        return $this->request('POST', "/orders/{$orderId}/shipment", ['warehouse_id' => $warehouseId]);
    }

    // -------------------------------------------------------------------------
    // Certificats clients
    // -------------------------------------------------------------------------

    public function getCertificate(int $id): array
    {
        return $this->request('GET', "/certificatsclients/{$id}");
    }

    public function getOrderCertificates(int $orderId): array
    {
        return $this->request('GET', "/certificatsclients/orders/{$orderId}");
    }

    public function downloadCertificate(int $id): array
    {
        return $this->request('GET', "/certificatsclients/{$id}/document");
    }

    // -------------------------------------------------------------------------
    // Setup
    // -------------------------------------------------------------------------

    public function getModules(): array
    {
        return $this->request('GET', '/setup/modules');
    }

    public function hasModule(string $moduleName): bool
    {
        $result = $this->getModules();

        if (isset($result['error']) || ! is_array($result)) {
            return false;
        }

        $modules = array_map('strtolower', $result);

        return in_array(strtolower($moduleName), $modules, true);
    }

    public function getConf(): array
    {
        return $this->request('GET', '/setup/conf');
    }

    // -------------------------------------------------------------------------
    // Requête HTTP
    // -------------------------------------------------------------------------

    private function request(string $method, string $endpoint, array $data = []): array
    {
        $url = $this->baseUrl . $endpoint;

        if ($method === 'GET' && ! empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => [
                'DOLAPIKEY: ' . $this->apiToken,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 10,
        ]);

        if (in_array($method, ['POST', 'PUT']) && ! empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response  = curl_exec($ch);
        $status    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        if ($response === false) {
            log_message('error', "[DolibarrApi] cURL {$method} {$endpoint} : {$curlError}");
            return ['error' => 'Erreur cURL', 'message' => $curlError, 'status' => 0];
        }

        $decoded = json_decode($response, true);

        if ($decoded !== null) {
            // Dolibarr renvoie parfois une erreur JSON avec un champ 'error' ou 'message'
            if ($status >= 400) {
                $raw     = $decoded['error'] ?? $decoded['message'] ?? "Erreur HTTP {$status}";
                $message = is_array($raw) ? json_encode($raw, JSON_UNESCAPED_UNICODE) : (string)$raw;
                log_message('error', "[DolibarrApi] {$method} {$endpoint} → {$status} : {$message}");
                return ['error' => $message, 'status' => $status];
            }

            return $decoded;
        }

        // Réponse non-JSON (HTML de rate-limit, page d'erreur serveur…)
        $httpErrors = [
            429 => 'Trop de requêtes (rate limit Dolibarr)',
            401 => 'Clé API invalide ou absente',
            403 => 'Accès refusé',
            404 => 'Endpoint introuvable',
            500 => 'Erreur interne du serveur Dolibarr',
            503 => 'Dolibarr indisponible',
        ];

        $message = $httpErrors[$status] ?? "Réponse non-JSON (HTTP {$status})";
        log_message('error', "[DolibarrApi] {$method} {$endpoint} → {$status} : {$message}");
        
        return ['error' => $message, 'status' => $status];
    }
}
