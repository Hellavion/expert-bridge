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
import type { Expert, Curator } from '@/types';

interface Props {
    experts: Pick<Expert, 'id' | 'name'>[];
    curators: Pick<Curator, 'id' | 'name'>[];
}

const props = defineProps<Props>();

const form = useForm({
    prefix: '',
    expert_id: props.experts[0]?.id || '',
    curator_id: '',
    is_active: true,
});

const submit = () => {
    form.post('/admin/promocodes');
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Админ панель', href: '/admin/dashboard' },
            { title: 'Промокоды', href: '/admin/promocodes' },
            { title: 'Создать', href: '/admin/promocodes/create' },
        ]"
    >
        <div class="mx-auto max-w-2xl space-y-6">
            <div>
                <h1 class="text-3xl font-bold">Создать промокод</h1>
                <p class="text-muted-foreground mt-2">
                    Сгенерируйте новый промокод для эксперта
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Информация о промокоде</CardTitle>
                    <CardDescription>
                        Укажите префикс, будет сгенерирован код вида PREFIX-12345
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="prefix">
                                Префикс <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="prefix"
                                v-model="form.prefix"
                                type="text"
                                required
                                placeholder="EXPERT"
                                :class="{
                                    'border-destructive': form.errors.prefix,
                                }"
                            />
                            <p class="text-muted-foreground text-xs">
                                Будет автоматически преобразован в заглавные буквы и дополнен случайным числом
                            </p>
                            <p
                                v-if="form.errors.prefix"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.prefix }}
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
                            <Label for="curator_id">
                                Куратор (опционально)
                            </Label>
                            <select
                                id="curator_id"
                                v-model="form.curator_id"
                                class="border-input focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-input/30 flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs outline-none transition-[color,box-shadow] focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                :class="{
                                    'border-destructive':
                                        form.errors.curator_id,
                                }"
                            >
                                <option value="">Без куратора</option>
                                <option
                                    v-for="curator in props.curators"
                                    :key="curator.id"
                                    :value="curator.id"
                                >
                                    {{ curator.name }}
                                </option>
                            </select>
                            <p
                                v-if="form.errors.curator_id"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.curator_id }}
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
                                Промокод активен
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
                                        : 'Создать промокод'
                                }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="
                                    $inertia.visit(
                                        '/admin/promocodes',
                                    )
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
