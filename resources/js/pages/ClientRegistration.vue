<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { Head, useForm } from '@inertiajs/vue3';

interface Props {
    expert?: {
        name: string;
        telegram_login: string;
    } | null;
}

const props = defineProps<Props>();

const form = useForm({
    telegram_login: '',
    promocode: '',
});

const submit = () => {
    form.post('/register-client');
};
</script>

<template>
    <Head title="Получить контакты" />

    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10">
        <div class="w-full max-w-sm">
            <div
                v-if="props.expert"
                class="mb-6 rounded-lg border border-green-200 bg-green-50 p-6 dark:border-green-800 dark:bg-green-900/20"
            >
                <h2 class="mb-3 text-lg font-semibold text-green-900 dark:text-green-100">
                    Контакты эксперта
                </h2>
                <div class="space-y-2 text-sm text-green-800 dark:text-green-200">
                    <div>
                        <span class="font-medium">Имя:</span> {{ props.expert.name }}
                    </div>
                    <div>
                        <span class="font-medium">Telegram:</span>
                        <a
                            :href="`https://t.me/${props.expert.telegram_login?.replace('@', '')}`"
                            target="_blank"
                            class="ml-1 underline hover:text-green-600 dark:hover:text-green-400"
                        >
                            {{ props.expert.telegram_login }}
                        </a>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="flex flex-col gap-6">
                <div class="grid gap-6">
                    <div class="grid gap-2">
                        <Label for="telegram_login">Telegram логин</Label>
                        <Input
                            id="telegram_login"
                            v-model="form.telegram_login"
                            type="text"
                            name="telegram_login"
                            required
                            autofocus
                            placeholder="@username"
                            :disabled="form.processing"
                        />
                        <InputError :message="form.errors.telegram_login" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="promocode">Промокод</Label>
                        <Input
                            id="promocode"
                            v-model="form.promocode"
                            type="text"
                            name="promocode"
                            required
                            placeholder="ALINA-12345"
                            :disabled="form.processing"
                        />
                        <InputError :message="form.errors.promocode" />
                    </div>
                </div>

                <Button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full"
                >
                    <Spinner v-if="form.processing" class="mr-2" />
                    {{ form.processing ? 'Отправка...' : 'Получить контакты' }}
                </Button>
            </form>
        </div>
    </div>
</template>
