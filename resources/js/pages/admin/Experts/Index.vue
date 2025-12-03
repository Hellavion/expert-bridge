<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2 } from 'lucide-vue-next';
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
import type { Expert } from '@/types';

interface Props {
    experts: Expert[];
}

const props = defineProps<Props>();

const deleteExpert = (id: number) => {
    if (confirm('Вы уверены, что хотите удалить этого эксперта?')) {
        router.delete(`/admin/experts/${id}`);
    }
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Админ панель', href: '/admin/dashboard' },
            { title: 'Эксперты', href: '/admin/experts' },
        ]"
    >
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Эксперты</h1>
                    <p class="text-muted-foreground mt-2">
                        Управление экспертами и их данными
                    </p>
                </div>
                <Link :href="'/admin/experts/create'">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Добавить эксперта
                    </Button>
                </Link>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="expert in props.experts"
                    :key="expert.id"
                    class="relative"
                >
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <CardTitle class="flex items-center gap-2">
                                    {{ expert.name }}
                                    <Badge
                                        v-if="expert.is_active"
                                        variant="default"
                                    >
                                        Активен
                                    </Badge>
                                    <Badge v-else variant="secondary">
                                        Неактивен
                                    </Badge>
                                </CardTitle>
                                <CardDescription v-if="expert.telegram_login">
                                    @{{ expert.telegram_login }}
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="grid gap-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">
                                    Комиссия:
                                </span>
                                <span class="font-medium">
                                    {{ expert.commission_percent }}%
                                </span>
                            </div>
                            <div
                                v-if="expert.expert_bonus"
                                class="flex justify-between"
                            >
                                <span class="text-muted-foreground">
                                    Бонус:
                                </span>
                                <span class="font-medium">
                                    {{ expert.expert_bonus }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">
                                    Кураторов:
                                </span>
                                <span class="font-medium">
                                    {{ expert.curators_count || 0 }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">
                                    Продуктов:
                                </span>
                                <span class="font-medium">
                                    {{ expert.products_count || 0 }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-muted-foreground">
                                    Промокодов:
                                </span>
                                <span class="font-medium">
                                    {{ expert.promocodes_count || 0 }}
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="expert.comment"
                            class="border-border border-t pt-4"
                        >
                            <p class="text-muted-foreground text-sm">
                                {{ expert.comment }}
                            </p>
                        </div>

                        <div class="border-border flex gap-2 border-t pt-4">
                            <Link
                                :href="`/admin/experts/${expert.id}/edit`"
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
                                @click="deleteExpert(expert.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div
                v-if="props.experts.length === 0"
                class="border-border bg-muted/50 flex min-h-[400px] flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center"
            >
                <p class="text-muted-foreground text-lg">
                    Экспертов пока нет
                </p>
                <Link :href="'/admin/experts/create'" class="mt-4">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Создать первого эксперта
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
