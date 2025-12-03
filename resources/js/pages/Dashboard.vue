<script setup lang="ts">
import { Link, Head } from '@inertiajs/vue3';
import { Users, UserCog, Package, Ticket, UserCheck } from 'lucide-vue-next';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Button } from '@/components/ui/button';
</script>

<template>
    <Head title="Админ панель" />

    <AppLayout
        :breadcrumbs="[
            { title: 'Админ панель', href: '/admin/dashboard' },
        ]"
    >
        <div class="space-y-6">
            <div>
                <h1 class="text-3xl font-bold">Добро пожаловать в админ панель</h1>
                <p class="text-muted-foreground mt-2">
                    Выберите раздел для управления
                </p>
            </div>

            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                <Card class="hover:border-primary transition-colors">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="bg-primary/10 text-primary rounded-lg p-2">
                                <Users class="h-6 w-6" />
                            </div>
                            <div>
                                <CardTitle>Эксперты</CardTitle>
                                <CardDescription>
                                    Управление экспертами
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <p class="text-muted-foreground text-sm">
                            Создавайте и редактируйте профили экспертов,
                            настраивайте комиссии и бонусы
                        </p>
                        <Link href="/admin/experts">
                            <Button class="w-full">Перейти</Button>
                        </Link>
                    </CardContent>
                </Card>

                <Card class="hover:border-primary transition-colors">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-500/10 text-blue-500 rounded-lg p-2">
                                <UserCog class="h-6 w-6" />
                            </div>
                            <div>
                                <CardTitle>Кураторы</CardTitle>
                                <CardDescription>
                                    Управление кураторами
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <p class="text-muted-foreground text-sm">
                            Добавляйте кураторов и привязывайте их к экспертам
                        </p>
                        <Link href="/admin/curators">
                            <Button class="w-full">Перейти</Button>
                        </Link>
                    </CardContent>
                </Card>

                <Card class="hover:border-primary transition-colors">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="bg-green-500/10 text-green-500 rounded-lg p-2">
                                <Package class="h-6 w-6" />
                            </div>
                            <div>
                                <CardTitle>Продукты</CardTitle>
                                <CardDescription>
                                    Управление продуктами
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <p class="text-muted-foreground text-sm">
                            Создавайте продукты, устанавливайте цены и
                            привязывайте к экспертам
                        </p>
                        <Link href="/admin/products">
                            <Button class="w-full">Перейти</Button>
                        </Link>
                    </CardContent>
                </Card>

                <Card class="hover:border-primary transition-colors">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-500/10 text-purple-500 rounded-lg p-2">
                                <Ticket class="h-6 w-6" />
                            </div>
                            <div>
                                <CardTitle>Промокоды</CardTitle>
                                <CardDescription>
                                    Управление промокодами
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <p class="text-muted-foreground text-sm">
                            Генерируйте промокоды для экспертов и кураторов,
                            отслеживайте использование
                        </p>
                        <Link href="/admin/promocodes">
                            <Button class="w-full">Перейти</Button>
                        </Link>
                    </CardContent>
                </Card>

                <Card class="hover:border-primary transition-colors">
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div class="bg-orange-500/10 text-orange-500 rounded-lg p-2">
                                <UserCheck class="h-6 w-6" />
                            </div>
                            <div>
                                <CardTitle>Клиенты</CardTitle>
                                <CardDescription>
                                    Управление клиентами
                                </CardDescription>
                            </div>
                        </div>
                    </CardHeader>
                    <CardContent class="space-y-2">
                        <p class="text-muted-foreground text-sm">
                            Просматривайте список клиентов и отмечайте статус
                            оплаты
                        </p>
                        <Link href="/admin/clients">
                            <Button class="w-full">Перейти</Button>
                        </Link>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
