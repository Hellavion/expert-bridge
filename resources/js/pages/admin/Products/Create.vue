<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Checkbox } from '@/components/ui/checkbox';
import type { Expert } from '@/types';

interface Props {
    experts: Pick<Expert, 'id' | 'name'>[];
}

const props = defineProps<Props>();

const form = useForm({
    description: '',
    price: '',
    expert_id: props.experts[0]?.id || '',
    is_active: true,
});

const submit = () => {
    form.post('/admin/products');
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Админ панель', href: '/admin/dashboard' },
            { title: 'Продукты', href: '/admin/products' },
            { title: 'Создать', href: '/admin/products/create' },
        ]"
    >
        <div class="mx-auto max-w-2xl space-y-6">
            <div>
                <h1 class="text-3xl font-bold">Создать продукт</h1>
                <p class="text-muted-foreground mt-2">
                    Добавьте новый продукт в систему
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Информация о продукте</CardTitle>
                    <CardDescription>
                        Заполните основные данные продукта
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="description">
                                Описание <span class="text-destructive">*</span>
                            </Label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                required
                                class="border-input focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-input/30 flex w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                :class="{
                                    'border-destructive': form.errors.description,
                                }"
                            ></textarea>
                            <p
                                v-if="form.errors.description"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.description }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="expert_id">
                                Эксперт <span class="text-destructive">*</span>
                            </Label>
                            <select
                                id="expert_id"
                                v-model="form.expert_id"
                                required
                                class="border-input focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-input/30 flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs outline-none transition-[color,box-shadow] focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                :class="{
                                    'border-destructive': form.errors.expert_id,
                                }"
                            >
                                <option
                                    v-for="expert in props.experts"
                                    :key="expert.id"
                                    :value="expert.id"
                                >
                                    {{ expert.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.expert_id"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.expert_id }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="price">
                                Цена <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="price"
                                v-model="form.price"
                                type="text"
                                required
                                placeholder="1000"
                                :class="{
                                    'border-destructive': form.errors.price,
                                }"
                            />
                            <p
                                v-if="form.errors.price"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.price }}
                            </p>
                        </div>

                        <div class="flex items-center space-x-2">
                            <Checkbox
                                id="is_active"
                                :checked="form.is_active"
                                @update:checked="
                                    (value: boolean) => (form.is_active = value)
                                "
                            />
                            <Label
                                for="is_active"
                                class="cursor-pointer font-normal"
                            >
                                Продукт активен
                            </Label>
                        </div>

                        <div class="flex gap-4">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                {{
                                    form.processing
                                        ? 'Создание...'
                                        : 'Создать продукт'
                                }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="
                                    $inertia.visit('/admin/products')
                                "
                            >
                                Отмена
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
