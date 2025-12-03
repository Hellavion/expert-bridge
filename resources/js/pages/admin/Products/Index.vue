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
import type { Product } from '@/types';

interface Props {
    products: Product[];
}

const props = defineProps<Props>();

const deleteProduct = (id: number) => {
    if (confirm('Вы уверены, что хотите удалить этот продукт?')) {
        router.delete(`/admin/products/${id}`);
    }
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Админ панель', href: '/admin/dashboard' },
            { title: 'Продукты', href: '/admin/products' },
        ]"
    >
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold">Продукты</h1>
                    <p class="text-muted-foreground mt-2">
                        Управление продуктами и их ценами
                    </p>
                </div>
                <Link :href="'/admin/products/create'">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Добавить продукт
                    </Button>
                </Link>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                <Card
                    v-for="product in props.products"
                    :key="product.id"
                    class="relative"
                >
                    <CardHeader>
                        <CardTitle>{{ product.description }}</CardTitle>
                        <CardDescription v-if="product.expert">
                            Эксперт: {{ product.expert.name }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div
                            class="bg-primary/10 text-primary flex items-center justify-center gap-2 rounded-lg p-4"
                        >
                            <span class="text-2xl font-bold">
                                {{ product.price }} ₽
                            </span>
                        </div>

                        <div
                            v-if="product.is_active"
                            class="border-border border-t pt-4"
                        >
                            <p class="text-muted-foreground text-sm">
                                {{ product.is_active }}
                            </p>
                        </div>

                        <div class="border-border flex gap-2 border-t pt-4">
                            <Link
                                :href="
                                    `/admin/products/${product.id}/edit`
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
                                @click="deleteProduct(product.id)"
                            >
                                <Trash2 class="h-4 w-4" />
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <div
                v-if="props.products.length === 0"
                class="border-border bg-muted/50 flex min-h-[400px] flex-col items-center justify-center rounded-lg border border-dashed p-8 text-center"
            >
                <p class="text-muted-foreground text-lg">Продуктов пока нет</p>
                <Link :href="'/admin/products/create'" class="mt-4">
                    <Button>
                        <Plus class="mr-2 h-4 w-4" />
                        Создать первый продукт
                    </Button>
                </Link>
            </div>
        </div>
    </AppLayout>
</template>
