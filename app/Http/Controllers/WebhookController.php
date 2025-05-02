<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WebhookHandlerService;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    private $webhookHandlerService;

    public function __construct(WebhookHandlerService $webhookHandlerService)
    {
        $this->webhookHandlerService = $webhookHandlerService;
    }

    public function __invoke(Request $request)
    {
        $data = $request->all();

        log::info("message",$request->all());
        if (isset($data['leads']['add'])) {
            foreach ($data['leads']['add'] as $lead) {
                $this->webhookHandlerService->handleAddEvent('lead', $lead);
            }
        }

        if (isset($data['leads']['update'])) {
            foreach ($data['leads']['update'] as $lead) {
                $this->webhookHandlerService->handleUpdateEvent('lead', $lead);
            }
        }

        if (isset($data['contacts']['add'])) {
            foreach ($data['contacts']['add'] as $contact) {
                $this->webhookHandlerService->handleAddEvent('contact', $contact);
            }
        }

        if (isset($data['contacts']['update'])) {
            foreach ($data['contacts']['update'] as $contact) {
                $this->webhookHandlerService->handleUpdateEvent('contact', $contact);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
