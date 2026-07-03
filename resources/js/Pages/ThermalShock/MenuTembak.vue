<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { IconFlame, IconArrowRight, IconChecklist } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

defineProps<{
    antreanPintu: Array<{
        id: number;
        thermal_pintu: string;
        total_antrean: number;
    }>;
}>();
</script>

<template>
    <Head title="Menu Tembak - Antrean Lab" />

    <div class="flex flex-col gap-6 p-4 md:p-8">
        <div>
            <h2 class="text-3xl font-bold tracking-tight flex items-center gap-2">
                <IconFlame class="size-8 text-orange-600 animate-pulse" />
                Menu Tembak (Monitoring Pengujian)
            </h2>
            <p class="text-sm text-muted-foreground mt-1">
                Daftar ringkasan jumlah sampel cetakan yang sedang mengantre / belum selesai di-test pada suhu 180°C & 200°C.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card v-for="pintu in antreanPintu" :key="pintu.id" class="border-none shadow-md transition-all hover:scale-[1.02]">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 border-b bg-zinc-50/50 dark:bg-zinc-900/50">
                    <CardTitle class="text-sm font-bold tracking-wide text-zinc-700 dark:text-zinc-300">
                        {{ pintu.thermal_pintu }}
                    </CardTitle>
                    <IconFlame class="size-4 text-orange-500" />
                </CardHeader>
                <CardContent class="pt-4 flex flex-col gap-4">
                    <div>
                        <div class="text-3xl font-black tracking-tight" :class="pintu.total_antrean > 0 ? 'text-orange-600' : 'text-emerald-600'">
                            {{ pintu.total_antrean }} <span class="text-xs font-medium text-muted-foreground">Unit Cetakan</span>
                        </div>
                        <p class="text-[11px] text-muted-foreground mt-0.5">Belum di-test / butuh kelanjutan uji</p>
                    </div>

                    <Button as-child class="w-full text-xs h-9" :variant="pintu.total_antrean > 0 ? 'default' : 'outline'" :disabled="pintu.total_antrean === 0">
                        <Link :href="pintu.total_antrean > 0 ? route('thermalshock.pintuAntrean', pintu.id) : '#'">
                            <IconChecklist class="mr-1.5 size-4" />
                            {{ pintu.total_antrean > 0 ? 'Mulai Input Hasil' : 'Selesai Diuji' }}
                            <IconArrowRight class="ml-auto size-3.5" />
                        </Link>
                    </Button>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
