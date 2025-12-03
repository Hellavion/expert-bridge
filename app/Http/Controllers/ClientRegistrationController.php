<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Promocode;
use App\Models\PromocodeUsageHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ClientRegistrationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('ClientRegistration', [
            'expert' => session('expert'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'telegram_login' => 'required|string|max:255',
            'promocode' => 'required|string|max:255',
        ]);

        // Проверяем существование и активность промокода
        $promocode = Promocode::with('expert')
            ->where('code', $validated['promocode'])
            ->where('is_active', true)
            ->first();

        if (!$promocode) {
            return back()->withErrors([
                'promocode' => 'Промокод не найден или неактивен.',
            ])->withInput();
        }

        // Проверяем, не зарегистрирован ли уже этот логин с этим промокодом
        $existingClient = Client::where('telegram_login', $validated['telegram_login'])
            ->where('promocode_id', $promocode->id)
            ->first();

        if ($existingClient) {
            return back()->withErrors([
                'telegram_login' => 'Этот Telegram логин уже зарегистрирован с данным промокодом.',
            ])->withInput();
        }

        try {
            DB::beginTransaction();

            // Создаем клиента
            $client = Client::create([
                'telegram_login' => $validated['telegram_login'],
                'promocode_id' => $promocode->id,
                'is_paid' => false,
            ]);

            // Записываем в историю использования
            PromocodeUsageHistory::create([
                'promocode_id' => $promocode->id,
                'client_id' => $client->id,
            ]);

            // Увеличиваем счетчик использований промокода
            $promocode->incrementUsage();

            DB::commit();

            // Возвращаем контакты эксперта
            return redirect()->route('client.registration')
                ->with('expert', [
                    'name' => $promocode->expert->name,
                    'telegram_login' => $promocode->expert->telegram_login,
                ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'error' => 'Произошла ошибка. Попробуйте снова.',
            ])->withInput();
        }
    }
}
