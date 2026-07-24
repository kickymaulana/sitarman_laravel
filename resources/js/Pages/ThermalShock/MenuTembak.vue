<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { IconFlame, IconArrowRight, IconChecklist, IconDoor } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

interface SesiItem {
    sesi: string;
    total: number;
}

interface PintuItem {
    id: number;
    thermal_pintu: string;
    sesi_list: SesiItem[];
    total_antrean: number;
}

defineProps<{
    antreanPintu: PintuItem[];
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
                Pilih pintu dan sesi untuk mulai input hasil pengujian.
            </p>
        </div>

        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <Card v-for="pintu in antreanPintu" :key="pintu.id" class="border-none shadow-md">
                <CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2 border-b bg-zinc-50/50 dark:bg-zinc-900/50">
                    <CardTitle class="text-sm font-bold tracking-wide flex items-center gap-2">
                        <IconDoor class="size-4 text-zinc-500" />
                        {{ pintu.thermal_pintu }}
                    </CardTitle>
                    <IconFlame class="size-4 text-orange-500" />
                </CardHeader>
                <CardContent class="pt-4 flex flex-col gap-2">
                    <div class="text-xs text-muted-foreground mb-1 font-semibold">
                        Total {{ pintu.total_antrean }} unit antrean
                    </div>

                    <!-- Daftar Sesi -->
                    <div v-for="sesi in pintu.sesi_list" :key="sesi.sesi"
                        class="flex items-center justify-between p-2 rounded-lg border bg-white dark:bg-zinc-950">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold">{{ sesi.sesi }}</span>
                            <span class="text-xs text-muted-foreground">{{ sesi.total }} unit</span>
                        </div>
                        <Button as-child size="sm" class="h-8 text-xs">
                            <Link :href="route('thermalshock.pintuAntrean', [pintu.id, sesi.sesi])">
                                Kerja <IconArrowRight class="size-3 ml-1" />
                            </Link>
                        </Button>
                    </div>

                    <div v-if="pintu.sesi_list.length === 0" class="text-center py-4 text-xs text-muted-foreground">
                        <IconChecklist class="size-6 mx-auto mb-1 opacity-50" />
                        Selesai Diuji
                    </div>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
