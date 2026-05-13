<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import {
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    CardDescription,
} from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Badge } from "@/components/ui/badge";
import {
    IconFlask,
    IconClock,
    IconChecklist,
    IconPlus,
    IconChartBar,
    IconArrowRight,
    IconMicroscope,
    IconCircleCheck,
    IconAlertCircle,
} from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

// Warna disesuaikan dengan tema Emerald & Amber
const stats = [
    {
        label: "Total Sampel",
        value: "124",
        icon: IconFlask,
        color: "text-emerald-600 dark:text-emerald-400",
        bg: "bg-emerald-50 dark:bg-emerald-950/30",
    },
    {
        label: "Sedang Diproses",
        value: "12",
        icon: IconClock,
        color: "text-amber-600 dark:text-amber-400",
        bg: "bg-amber-50 dark:bg-amber-950/30",
    },
    {
        label: "Butuh Persetujuan",
        value: "5",
        icon: IconChecklist,
        color: "text-teal-600 dark:text-teal-400",
        bg: "bg-teal-50 dark:bg-teal-950/30",
    },
    {
        label: "Selesai Hari Ini",
        value: "8",
        icon: IconCircleCheck,
        color: "text-cyan-600 dark:text-cyan-400",
        bg: "bg-cyan-50 dark:bg-cyan-950/30",
    },
];

const recentSamples = [
    {
        code: "KDSPT29",
        customer: "ARTALEGA",
        status: "Proses",
        date: "12 Apr 2026",
    },
    {
        code: "KDSPT28",
        customer: "MARK DYNAMICS",
        status: "Selesai",
        date: "11 Apr 2026",
    },
    {
        code: "KDSPT27",
        customer: "GLOVE CORP",
        status: "Draft",
        date: "11 Apr 2026",
    },
    {
        code: "KDSPT26",
        customer: "SUNG SHIN",
        status: "Selesai",
        date: "10 Apr 2026",
    },
];
</script>

<template>
    <Head title="Dashboard SITARMAN" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-6 transition-colors duration-500 bg-slate-50/50 min-h-screen">

        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="h-6 w-1.5 bg-emerald-500 rounded-full"></div>
                    <h1 class="text-2xl font-black tracking-tight text-slate-900 dark:text-white uppercase">
                        Dashboard SITARMAN
                    </h1>
                </div>
                <p class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold uppercase tracking-[0.2em] ml-3">
                    Sistem Thermal Akurasi Report Mutu Analisa
                </p>
            </div>

            <div class="flex gap-2">
                <Button
                    variant="outline"
                    size="sm"
                    class="font-bold uppercase text-[10px] h-9 border-emerald-200 dark:border-emerald-800 hover:bg-emerald-50 text-emerald-700"
                >
                    <IconChartBar class="mr-2 size-4" /> Laporan Mutu
                </Button>
                <Button
                    size="sm"
                    class="font-black uppercase text-[10px] h-9 bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-200 dark:shadow-none"
                >
                    <IconPlus class="mr-2 size-4" /> Entry Sampel
                </Button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <Card
                v-for="stat in stats"
                :key="stat.label"
                class="border-none shadow-sm overflow-hidden bg-white dark:bg-slate-900 ring-1 ring-slate-100 dark:ring-slate-800"
            >
                <CardContent class="p-6 flex items-center justify-between relative overflow-hidden">
                    <!-- Decorate Background Icon -->
                    <component :is="stat.icon" class="absolute -right-2 -bottom-2 size-16 opacity-5 text-slate-900" />

                    <div class="relative z-10">
                        <p class="text-[9px] font-black uppercase text-slate-500 tracking-widest mb-1">
                            {{ stat.label }}
                        </p>
                        <h3 class="text-3xl font-black tracking-tighter text-slate-900 dark:text-white">
                            {{ stat.value }}
                        </h3>
                    </div>
                    <div
                        :class="[stat.bg, stat.color]"
                        class="size-12 rounded-2xl flex items-center justify-center shadow-inner relative z-10"
                    >
                        <component :is="stat.icon" class="size-6" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Activity -->
            <Card class="lg:col-span-2 border-none shadow-sm bg-white dark:bg-slate-900 ring-1 ring-slate-100 dark:ring-slate-800">
                <CardHeader class="pb-2 flex flex-row items-center justify-between space-y-0">
                    <CardTitle class="text-xs font-black uppercase tracking-widest flex items-center gap-2 text-slate-800 dark:text-slate-200">
                        <IconMicroscope class="size-4 text-emerald-600" />
                        Analisa Sampel Terbaru
                    </CardTitle>
                    <Badge variant="outline" class="text-[9px] border-emerald-200 text-emerald-700 uppercase">Live Update</Badge>
                </CardHeader>
                <CardContent class="space-y-3 pt-4">
                    <div
                        v-for="sample in recentSamples"
                        :key="sample.code"
                        class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-950/40 border border-transparent hover:border-emerald-200 hover:bg-emerald-50/30 transition-all cursor-pointer group"
                    >
                        <div class="flex items-center gap-4">
                            <div class="size-10 rounded-lg bg-white dark:bg-slate-800 shadow-sm flex items-center justify-center font-black text-[10px] text-emerald-600 border border-slate-100 dark:border-slate-700 italic group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                LAB
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-tight text-slate-900 dark:text-white">
                                    {{ sample.code }}
                                </p>
                                <p class="text-[9px] text-slate-500 font-bold uppercase">
                                    {{ sample.customer }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <Badge
                                :class="[
                                    sample.status === 'Selesai'
                                    ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-100'
                                    : 'bg-amber-100 text-amber-700 hover:bg-amber-100'
                                ]"
                                class="text-[8px] font-black uppercase px-2 py-0 border-none"
                            >
                                {{ sample.status }}
                            </Badge>
                            <IconArrowRight class="size-4 text-slate-300 group-hover:text-emerald-600 transition-transform group-hover:translate-x-1" />
                        </div>
                    </div>
                </CardContent>
            </Card>

            <!-- Right Column: Info Panels -->
            <div class="space-y-4">
                <!-- Highlight Card -->
                <Card class="border-none shadow-lg bg-gradient-to-br from-emerald-600 to-teal-700 text-white overflow-hidden relative group">
                    <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform duration-500">
                        <IconFlask class="size-32" />
                    </div>
                    <CardHeader class="pb-2">
                        <CardTitle class="text-xs font-black uppercase tracking-widest opacity-80">Antrean Lab</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p class="text-3xl font-black mb-4 tracking-tighter">
                            12 <span class="text-[10px] font-bold opacity-70 uppercase ml-1 tracking-normal">Analisa Aktif</span>
                        </p>
                        <Button
                            variant="secondary"
                            size="sm"
                            class="w-full font-black uppercase text-[9px] h-8 bg-white text-emerald-700 hover:bg-emerald-50 border-none shadow-sm"
                        >
                            Mulai Analisa <IconArrowRight class="ml-2 size-3" />
                        </Button>
                    </CardContent>
                </Card>

                <!-- Status Update Card -->
                <Card class="border-none shadow-sm bg-white dark:bg-slate-900 ring-1 ring-slate-100 dark:ring-slate-800">
                    <CardHeader class="pb-2">
                        <CardTitle class="text-[9px] font-black uppercase tracking-widest text-slate-500 italic">Informasi Mutu</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4 pt-2">
                        <div class="flex gap-3 items-start">
                            <div class="size-2 rounded-full bg-amber-500 mt-1 shrink-0 animate-pulse"></div>
                            <p class="text-[10px] font-bold leading-snug text-slate-600 dark:text-slate-300">
                                Perlu Verifikasi Akurasi:
                                <span class="text-emerald-600 dark:text-emerald-400 uppercase font-black block">KDSPT29 - ARTALEGA</span>
                            </p>
                        </div>
                        <div class="flex gap-3 items-start">
                            <div class="size-2 rounded-full bg-emerald-500 mt-1 shrink-0"></div>
                            <p class="text-[10px] font-bold leading-snug text-slate-600 dark:text-slate-300">
                                Laporan Siap Cetak:
                                <span class="text-emerald-600 dark:text-emerald-400 uppercase font-black block">KDSPT28 - MARK DYNAMICS</span>
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Tambahan animasi halus jika diperlukan */
.transition-all {
    transition-duration: 300ms;
}
</style>
