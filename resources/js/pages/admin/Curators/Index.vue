<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
import { Pencil, Plus, Trash2, User } from 'lucide-vue-next';
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
import type { Curator } from '@/types';

interface Props {
    curators: Curator[];
}

const props = defineProps<Props>();

const deleteCurator = (id: number) => {
    if (confirm('Вы уверены, что хотите удалить этого куратора?')) {
        router.delete(`/admin/curators/${id}`);
    }
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Админ панель', href: '/admin/dashboard' },
            { title: 'Кураторы', href: '/admin/curators' },
        ]"
    >
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Кураторы</h1>
                    <p class="text-muted-foreground mt-2">
                        Управление кураторами и их связью с экспертами
                    </p>
                </div>
                <Link :href="'/admin/curators/create'">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Добавить куратора
                    </Button>
                </Link>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="curator in props.curators"
                    :key="curator.id"
                    class="relative"
                >
                    <CardHeader>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <CardTitle class="flex items-center gap-2">
                                    {{ curator.name }}
                                </CardTitle>
                                <CardDescription
                                    v-if="curator.telegram_login"
                                >
                                    @{{ curator.telegram_login }}
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div
                            v-if="curator.expert"
                            class="bg-muted/50 flex items-center gap-2 rounded-lg p-3"
                        >
                            <User class="text-muted-foreground h-4 w-4" />
                            <div class="flex-1">
                                <p class="text-sm font-medium">
                                    Эксперт: {{ curator.expert.name }}
                                </p>
                                <p
                                    v-if="curator.expert.telegram_login"
                                    class="text-muted-foreground text-xs"
                                >
                                    @{{ curator.expert.telegram_login }}
                                </p>
                            </div>
                            <Badge
                                v-if="curator.expert.is_active"
                                variant="default"
                                class="text-xs"
                            >
                                Активен
                            </Badge>
                        </div>

                        <div
                            v-if="curator.comment"
                            class="border-border border-t pt-4"
                        >
                            <p class="text-muted-foreground text-sm">
                                {{ curator.comment }}
                            </p>
                        </div>

                        <div class="border-border flex gap-2 border-t pt-4">
                            <Link
                                :href="
                                    `/admin/curators/${curator.id}/edit`
                                "
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
                                @click="deleteCurator(curator.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div
                v-if="props.curators.length === 0"
                class="border-border bg-muted/50 flex min-h-[400px] flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center"
            >
                <p class="text-muted-foreground text-lg">
                    Кураторов пока нет
                </p>
                <Link :href="'/admin/curators/create'" class="mt-4">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Создать первого куратора
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
