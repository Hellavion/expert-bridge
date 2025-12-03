<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Trash2, Check, X } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import type { Client } from '@/types';

interface Props {
    clients: Client[];
}

const props = defineProps<Props>();

const togglePayment = (clientId: number) => {
    router.post(`/admin/clients/${clientId}/toggle-payment`);
};

const deleteClient = (id: number) => {
    if (confirm('Вы уверены, что хотите удалить этого клиента?')) {
        router.delete(`/admin/clients/${id}`);
    }
};

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('ru-RU', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Админ панель', href: '/admin/dashboard' },
            { title: 'Клиенты', href: '/admin/clients' },
        ]"
    >
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Клиенты</h1>
                    <p class="text-muted-foreground mt-2">
                        Список зарегистрированных клиентов с отметкой об
                        оплате
                    </p>
                </div>
                <div class="text-muted-foreground text-sm">
                    Всего клиентов:
                    <span class="font-semibold">{{ props.clients.length }}</span>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="client in props.clients"
                    :key="client.id"
                    class="relative"
                >
                    <CardHeader>
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1">
                                <CardTitle class="flex items-center gap-2">
                                    <span>{{ client.telegram_login }}</span>
                                </CardTitle>
                                <CardDescription class="mt-2">
                                    ID: {{ client.id }}
                                </CardDescription>
                            </div>
                            <Badge
                                :variant="
                                    client.is_paid ? 'default' : 'secondary'
                                "
                                class="flex items-center gap-1"
                            >
                                <Check v-if="client.is_paid" class="h-3 w-3" />
                                <X v-else class="h-3 w-3" />
                                {{ client.is_paid ? 'Оплачено' : 'Не оплачено' }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-3 text-sm">
                            <div
                                v-if="client.promocode"
                                class="bg-muted/50 rounded-lg p-3"
                            >
                                <p class="text-muted-foreground mb-2 text-xs">
                                    Промокод
                                </p>
                                <code
                                    class="bg-background rounded border px-2 py-1 font-mono text-xs"
                                >
                                    {{ client.promocode.code }}
                                </code>
                                <div
                                    v-if="client.promocode.expert"
                                    class="mt-2 space-y-1"
                                >
                                    <p
                                        class="text-muted-foreground text-xs"
                                    >
                                        Эксперт:
                                        <span class="font-medium">
                                            {{ client.promocode.expert.name }}
                                        </span>
                                    </p>
                                    <p
                                        v-if="client.promocode.curator"
                                        class="text-muted-foreground text-xs"
                                    >
                                        Куратор:
                                        <span class="font-medium">
                                            {{ client.promocode.curator.name }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <div class="border-border border-t pt-3">
                                <p class="text-muted-foreground text-xs">
                                    Зарегистрирован:
                                    {{ formatDate(client.created_at) }}
                                </p>
                            </div>
                        </div>

                        <div class="border-border flex gap-2 border-t pt-4">
                            <Button
                                :variant="
                                    client.is_paid ? 'outline' : 'default'
                                "
                                class="flex-1"
                                @click="togglePayment(client.id)"
                            >
                                {{
                                    client.is_paid
                                        ? 'Отменить оплату'
                                        : 'Отметить оплату'
                                }}
                            </Button>
                            <Button
                                variant="destructive"
                                size="icon"
                                @click="deleteClient(client.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div
                v-if="props.clients.length === 0"
                class="border-border bg-muted/50 flex min-h-[400px] flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center"
            >
                <p class="text-muted-foreground text-lg">Клиентов пока нет</p>
                <p class="text-muted-foreground mt-2 text-sm">
                    Клиенты появятся после регистрации через публичную форму
                </p>
            </div>
        </div>
    </AppLayout>
</template>
