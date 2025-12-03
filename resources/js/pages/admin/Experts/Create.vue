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
// No props needed for Create page

const form = useForm({
    name: '',
    telegram_login: '',
    comment: '',
    commission_percent: '0',
    expert_bonus: '',
    is_active: true,
});

const submit = () => {
    form.post('/admin/experts');
};
</script>

<template>
    <AppLayout
        :breadcrumbs="[
            { title: 'Админ панель', href: '/admin/dashboard' },
            { title: 'Эксперты', href: '/admin/experts' },
            { title: 'Создать', href: '/admin/experts/create' },
        ]"
    >
        <div class="mx-auto max-w-2xl space-y-6">
            <div>
                <h1 class="text-3xl font-bold">Создать эксперта</h1>
                <p class="text-muted-foreground mt-2">
                    Добавьте нового эксперта в систему
                </p>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Информация об эксперте</CardTitle>
                    <CardDescription>
                        Заполните основные данные эксперта
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
                            <Label for="commission_percent">
                                Процент комиссии
                                <span class="text-destructive">*</span>
                            </Label>
                            <div class="flex items-center gap-2">
                                <Input
                                    id="commission_percent"
                                    v-model="form.commission_percent"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    required
                                    class="flex-1"
                                    :class="{
                                        'border-destructive':
                                            form.errors.commission_percent,
                                    }"
                                />
                                <span class="text-muted-foreground">%</span>
                            </div>
                            <p
                                v-if="form.errors.commission_percent"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.commission_percent }}
                            </p>
                        </div>

                        <div class="space-y-2">
                            <Label for="expert_bonus">Бонус эксперта</Label>
                            <Input
                                id="expert_bonus"
                                v-model="form.expert_bonus"
                                type="text"
                                placeholder="Дополнительный бонус"
                                :class="{
                                    'border-destructive':
                                        form.errors.expert_bonus,
                                }"
                            />
                            <p
                                v-if="form.errors.expert_bonus"
                                class="text-destructive text-sm"
                            >
                                {{ form.errors.expert_bonus }}
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
                                Эксперт активен
                            </Label>
                        </div>

                        <div class="flex gap-4">
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1"
                            >
                                {{ form.processing ? 'Создание...' : 'Создать эксперта' }}
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                @click="$inertia.visit('/admin/experts')"
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
