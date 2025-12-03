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
import type { Curator, Expert } from '@/types';

interface Props {
    curator: Curator;
    experts: Pick<Expert, 'id' | 'name'>[];
}

const props = defineProps<Props>();

const form = useForm({
    name: props.curator.name,
    telegram_login: props.curator.telegram_login || '',
    curator_bonus: props.curator.curator_bonus || '',
    expert_id: props.curator.expert_id,
    comment: props.curator.comment || '',
    is_active: props.curator.is_active,
});

const submit = () => {
    form.put(`/admin/curators/${props.curator.id}`);
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Админ панель', href: '/admin/dashboard' },
            { title: 'Кураторы', href: '/admin/curators' },
            {
                title: 'Редактировать',
                href: `/admin/curators/${curator.id}/edit`,
            },
        ]"
    >
        <div class="mx-auto max-w-2xl space-y-6">
            <div>
                <h1 class="text-3xl font-bold">Редактировать куратора</h1>
                <p class="text-muted-foreground mt-2">
                    Обновите данные куратора {{ curator.name }}
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Информация о кураторе</CardTitle>
                    <CardDescription>
                        Измените данные куратора и сохраните изменения
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="space-y-2">
                            <Label for="name">
                                Имя <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                :class="{
                                    'border-destructive': form.errors.name,
                                }"
                            />
                            <p
                                v-if="form.errors.name"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.name }}
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
                            <Label for="telegram_login">Telegram Login</Label>
                            <div class="flex items-center">
                                <span class="text-muted-foreground mr-2">
                                    @
                                </span>
                                <Input
                                    id="telegram_login"
                                    v-model="form.telegram_login"
                                    type="text"
                                    placeholder="username"
                                    :class="{
                                        'border-destructive':
                                            form.errors.telegram_login,
                                    }"
                                />
                            </div>
                            <p
                                v-if="form.errors.telegram_login"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.telegram_login }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="curator_bonus">Бонус куратора</Label>
                            <Input
                                id="curator_bonus"
                                v-model="form.curator_bonus"
                                type="text"
                                placeholder="Дополнительный бонус"
                                :class="{
                                    'border-destructive':
                                        form.errors.curator_bonus,
                                }"
                            />
                            <p
                                v-if="form.errors.curator_bonus"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.curator_bonus }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="comment">Комментарий</Label>
                            <textarea
                                id="comment"
                                v-model="form.comment"
                                rows="3"
                                class="border-input focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive dark:bg-input/30 flex w-full min-w-0 rounded-md border bg-transparent px-3 py-2 text-base shadow-xs outline-none transition-[color,box-shadow] placeholder:text-muted-foreground focus-visible:ring-[3px] disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                :class="{
                                    'border-destructive': form.errors.comment,
                                }"
                            ></textarea>
                            <p
                                v-if="form.errors.comment"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.comment }}
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
                                Куратор активен
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
                                        ? 'Сохранение...'
                                        : 'Сохранить изменения'
                                }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="
                                    $inertia.visit('/admin/curators')
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
