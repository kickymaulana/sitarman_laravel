<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { IconFlask, IconChecklist, IconAlertCircle, IconUsers, IconDoor, IconArrowRight, IconFlame } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    stats: {
        total_sampel: number; ok_180: number; ng_180: number; belum_180: number;
        ok_200: number; belum_200: number; pct_180: number; pct_200: number;
    };
    pintuStats: { id: number; thermal_pintu: string; thermal_shock_details_count: number }[];
    recentEntries: { id: number; hari_tgl: string; thermal_pintu: { thermal_pintu: string } | null; user: { name: string } | null; customer: { customer: string } | null }[];
    totalUsers: number;
}>();

const getPctColor = (pct: number) => pct >= 80 ? 'text-emerald-600' : pct >= 50 ? 'text-amber-600' : 'text-rose-600';
</script>

<template>
    <Head title="Dashboard - SITARMAN" />
    <div class="flex flex-col gap-6 p-4 md:p-8">
        <div>
            <h2 class="text-2xl font-bold tracking-tight flex items-center gap-2">
                <IconFlask class="size-7 text-primary" />
                Dashboard SITARMAN
            </h2>
            <p class="text-sm text-muted-foreground mt-1">Ringkasan data Thermal Shock dan sistem</p>
        </div>

        <!-- Stat Cards -->
        <div class="grid gap-4 grid-cols-2 md:grid-cols-4">
            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">Total Sampel</CardTitle>
                    <IconFlask class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-bold">{{ stats.total_sampel }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">User Terdaftar</CardTitle>
                    <IconUsers class="size-4 text-muted-foreground" />
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-bold">{{ totalUsers }}</div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">OK 180°C</CardTitle>
                    <IconChecklist class="size-4 text-emerald-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-bold text-emerald-600">{{ stats.ok_180 }}</div>
                    <p class="text-xs text-muted-foreground">{{ stats.pct_180 }}% dari total</p>
                </CardContent>
            </Card>
            <Card>
                <CardHeader class="flex flex-row items-center justify-between pb-2">
                    <CardTitle class="text-sm font-medium">OK 200°C</CardTitle>
                    <IconChecklist class="size-4 text-amber-500" />
                </CardHeader>
                <CardContent>
                    <div class="text-3xl font-bold text-amber-600">{{ stats.ok_200 }}</div>
                    <p class="text-xs text-muted-foreground">{{ stats.pct_200 }}% dari total</p>
                </CardContent>
            </Card>
        </div>

        <!-- Grafik Sederhana Per Pintu -->
        <Card>
            <CardHeader>
                <CardTitle class="text-sm font-bold flex items-center gap-2">
                    <IconDoor class="size-4" /> Data Per Pintu
                </CardTitle>
            </CardHeader>
            <CardContent>
                <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-4">
                    <div v-for="p in pintuStats" :key="p.id" class="border rounded-lg p-3 bg-zinc-50/50 dark:bg-zinc-900/30">
                        <div class="text-sm font-bold text-zinc-700 dark:text-zinc-300 flex items-center gap-1.5">
                            <IconFlame class="size-3.5 text-amber-500" /> {{ p.thermal_pintu }}
                        </div>
                        <div class="text-2xl font-black mt-1">{{ p.thermal_shock_details_count }}</div>
                        <div class="text-[11px] text-muted-foreground">sampel terdaftar</div>
                    </div>
                </div>
            </CardContent>
        </Card>

        <!-- Status Test -->
        <div class="grid gap-4 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="text-sm font-bold">Status Uji 180°C</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-emerald-600 font-semibold">OK ({{ stats.ok_180 }})</span>
                                <span class="font-bold" :class="getPctColor(stats.pct_180)">{{ stats.pct_180 }}%</span>
                            </div>
                            <div class="h-2 bg-zinc-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full transition-all" :style="{ width: stats.pct_180 + '%' }"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-rose-600 font-semibold">NG ({{ stats.ng_180 }})</span>
                                <span v-if="stats.total_sampel > 0" class="text-rose-600 font-bold">{{ Math.round((stats.ng_180 / stats.total_sampel) * 100) }}%</span>
                            </div>
                            <div class="h-2 bg-zinc-100 rounded-full overflow-hidden">
                                <div class="h-full bg-rose-500 rounded-full transition-all" :style="{ width: (stats.total_sampel > 0 ? (stats.ng_180 / stats.total_sampel) * 100 : 0) + '%' }"></div>
                            </div>
                        </div>
                        <div v-if="stats.belum_180 > 0" class="text-xs text-muted-foreground">{{ stats.belum_180 }} sampel belum di-test</div>
                    </div>
                </CardContent>
            </Card>
            <Card>
                <CardHeader>
                    <CardTitle class="text-sm font-bold">Status Uji 200°C</CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-xs mb-1">
                                <span class="text-amber-600 font-semibold">OK ({{ stats.ok_200 }})</span>
                                <span class="font-bold" :class="getPctColor(stats.pct_200)">{{ stats.pct_200 }}%</span>
                            </div>
                            <div class="h-2 bg-zinc-100 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500 rounded-full transition-all" :style="{ width: stats.pct_200 + '%' }"></div>
                            </div>
                        </div>
                        <div v-if="stats.belum_200 > 0" class="text-xs text-muted-foreground mt-2">{{ stats.belum_200 }} sampel belum di-test 200°C</div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Recent Entries -->
        <Card>
            <CardHeader class="flex flex-row items-center justify-between">
                <CardTitle class="text-sm font-bold">Entry Terbaru</CardTitle>
                <Link :href="route('thermalshock.index')" class="text-xs text-primary font-semibold hover:underline flex items-center gap-1">
                    Lihat Semua <IconArrowRight class="size-3" />
                </Link>
            </CardHeader>
            <CardContent>
                <div v-if="!recentEntries.length" class="text-sm text-muted-foreground text-center py-4">Belum ada data</div>
                <div v-for="item in recentEntries" :key="item.id" class="flex items-center justify-between py-2 border-b last:border-0 text-sm">
                    <div class="flex flex-col">
                        <span class="font-medium">{{ item.customer?.customer || '-' }}</span>
                        <span class="text-xs text-muted-foreground">{{ item.thermal_pintu?.thermal_pintu }} • {{ item.hari_tgl }} • {{ item.user?.name }}</span>
                    </div>
                    <Button as-child variant="ghost" size="sm">
                        <Link :href="route('thermalshock.strukRingkasan', { ids: item.id })">Detail</Link>
                    </Button>
                </div>
            </CardContent>
        </Card>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-2">
            <Button as-child class="bg-emerald-600 hover:bg-emerald-700">
                <Link :href="route('thermalshock.create')"><IconFlame class="mr-1.5 size-4" /> Input Baru</Link>
            </Button>
            <Button as-child variant="outline">
                <Link :href="route('thermalshock.menuTembak')">Menu Tembak</Link>
            </Button>
            <Button as-child variant="outline">
                <Link :href="route('thermalshock.strukFilter')">Struk Filter</Link>
            </Button>
        </div>
    </div>
</template>
