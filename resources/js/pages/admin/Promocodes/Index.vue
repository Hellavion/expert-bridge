<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2, Copy, Check } from 'lucide-vue-next';
import { ref } from 'vue';
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
import type { Promocode } from '@/types';

interface Props {
    promocodes: Promocode[];
}

const props = defineProps<Props>();

const copiedId = ref<number | null>(null);

const deletePromocode = (id: number) => {
    if (confirm('Вы уверены, что хотите удалить этот промокод?')) {
        router.delete(`/admin/promocodes/${id}`);
    }
};

const copyToClipboard = async (code: string, id: number) => {
    try {
        await navigator.clipboard.writeText(code);
        copiedId.value = id;
        setTimeout(() => {
            copiedId.value = null;
        }, 2000);
    } catch (err) {
        console.error('Failed to copy:', err);
    }
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Админ панель', href: '/admin/dashboard' },
            { title: 'Промокоды', href: '/admin/promocodes' },
        ]"
    >
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Промокоды</h1>
                    <p class="text-muted-foreground mt-2">
                        Управление промокодами и их использованием
                    </p>
                </div>
                <Link :href="'/admin/promocodes/create'">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Создать промокод
                    </Button>
                </Link>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="promocode in props.promocodes"
                    :key="promocode.id"
                    class="relative"
                >
                    <CardHeader>
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1">
                                <CardTitle class="flex items-center gap-2">
                                    <code
                                        class="bg-muted rounded px-2 py-1 font-mono text-sm"
                                    >
                                        {{ promocode.code }}
                                    </code>
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        class="h-6 w-6"
                                        @click="
                                            copyToClipboard(
                                                promocode.code,
                                                promocode.id,
                                            )
                                        "
                                    >
                                        <Check
                                            v-if="copiedId === promocode.id"
                                            class="text-green-500 h-4 w-4"
                                        />
                                        <Copy v-else class="h-4 w-4" />
                                    </Button>
                                </CardTitle>
                            </div>
                            <Badge
                                :variant="
                                    promocode.is_active ? 'default' : 'secondary'
                                "
                            >
                                {{ promocode.is_active ? 'Активен' : 'Неактивен' }}
                            </Badge>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2 text-sm">
                            <div
                                v-if="promocode.expert"
                                class="bg-muted/50 rounded-lg p-3"
                            >
                                <p class="text-muted-foreground mb-1 text-xs">
                                    Эксперт
                                </p>
                                <p class="font-medium">
                                    {{ promocode.expert.name }}
                                </p>
                            </div>

                            <div
                                v-if="promocode.curator"
                                class="bg-muted/50 rounded-lg p-3"
                            >
                                <p class="text-muted-foreground mb-1 text-xs">
                                    Куратор
                                </p>
                                <p class="font-medium">
                                    {{ promocode.curator.name }}
                                </p>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-muted-foreground">
                                    Использований:
                                </span>
                                <span class="font-medium">
                                    {{ promocode.usage_count }}
                                </span>
                            </div>
                        </div>

                        <div class="border-border flex gap-2 border-t pt-4">
                            <Link
                                :href="`/admin/promocodes/${promocode.id}/edit`"
                                class="flex-1"
                            >
                                <Button variant="outline" class="w-full">
                                    <Pencil class="mr-2 h-4 w-4" />
                                    Редактировать
                                </Button>
                            </Link>
                            <Button
                                variant="destructive"
                                size="icon"
                                @click="deletePromocode(promocode.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div
                v-if="props.promocodes.length === 0"
                class="border-border bg-muted/50 flex min-h-[400px] flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center"
            >
                <p class="text-muted-foreground text-lg">
                    Промокодов пока нет
                </p>
                <Link :href="'/admin/promocodes/create'" class="mt-4">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Создать первый промокод
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
