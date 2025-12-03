<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClientController extends Controller
{
    public function index(): Response
    {
        $clients = Client::with(['promocode.expert', 'promocode.curator'])
            ->latest()
            ->get();

        return Inertia::render('admin/Clients/Index', [
            'clients' => $clients,
        ]);
    }

    public function togglePayment(Client $client)
    {
        $client->update([
            'is_paid' => !$client->is_paid,
        ]);

        $status = $client->is_paid ? 'оплачен' : 'не оплачен';

        return back()->with('success', "Статус клиента изменен на: {$status}");
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('admin.clients.index')
            ->with('success', 'Клиент успешно удален');
    }
}
