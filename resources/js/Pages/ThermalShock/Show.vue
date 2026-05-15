<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { IconArrowLeft, IconPrinter, IconFlask } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: any;
}>();

// Fungsi format jam (H:i:s -> H:i)
const formatTime = (time: string) => {
    return time ? time.substring(0, 5) : "-";
};

// Fungsi format tanggal Indonesia
const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
    });
};
</script>

<template>
    <Head title="Detail Thermal Shock" />
    <div class="p-4 md:p-8 pt-2">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('thermalshock.index')"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Detail Test Thermal Shock</h2>
                    <p class="text-sm text-muted-foreground italic">ID Pengetesan: #TS-{{ thermalshock.id }}</p>
                </div>
            </div>
            <Button @click="window.print()" variant="secondary">
                <IconPrinter class="mr-2 size-4" /> Cetak Laporan
            </Button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <Card class="lg:col-span-1 border-none shadow-md h-fit">
                <CardHeader class="bg-primary/5 border-b">
                    <CardTitle class="text-sm flex items-center gap-2">
                        <IconFlask class="size-4 text-primary" /> Informasi Header
                    </CardTitle>
                </CardHeader>
                <CardContent class="pt-6 space-y-4">
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-sm text-muted-foreground">Hari / Tanggal</span>
                        <span class="text-sm font-bold">{{ formatDate(thermalshock.hari_tgl) }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-sm text-muted-foreground">Suhu Testing</span>
                        <span class="text-sm font-bold text-orange-600">{{ thermalshock.suhu_testing }}°C</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-sm text-muted-foreground">Suhu Motor</span>
                        <span class="text-sm font-bold">{{ thermalshock.suhu_motor || '-' }}</span>
                    </div>
                    <div class="flex justify-between border-b pb-2">
                        <span class="text-sm text-muted-foreground">Suhu Air</span>
                        <span class="text-sm font-bold">{{ thermalshock.suhu_air }}</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 pt-2">
                        <div>
                            <p class="text-[10px] uppercase text-muted-foreground">Jam Proses</p>
                            <p class="text-sm font-bold">{{ formatTime(thermalshock.jam_awal_proses) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-muted-foreground">Capai Suhu</p>
                            <p class="text-sm font-bold">{{ formatTime(thermalshock.jam_capai_suhu) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-muted-foreground">Mulai Tembak</p>
                            <p class="text-sm font-bold">{{ formatTime(thermalshock.jam_mulai_tembak) }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase text-muted-foreground">Selesai Tembak</p>
                            <p class="text-sm font-bold">{{ formatTime(thermalshock.jam_selesai_tembak) }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="lg:col-span-2 border-none shadow-md overflow-hidden">
                <CardHeader class="border-b">
                    <CardTitle class="text-sm">Isi Oven / Daftar Item ({{ thermalshock.details.length }} Item)</CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/30">
                                <TableHead>Oven/Kode</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Model / Size</TableHead>
                                <TableHead class="text-center">Pos</TableHead>
                                <TableHead class="text-center">Hasil</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in thermalshock.details" :key="item.id">
                                <TableCell class="font-medium">{{ item.oven_kode_tanah }}</TableCell>
                                <TableCell>{{ item.customer?.customer }}</TableCell>
                                <TableCell>
                                    <p class="text-sm font-medium">{{ item.model_size?.modelsize }}</p>
                                    <p class="text-[10px] text-muted-foreground italic">{{ item.spesifikasi }}</p>
                                </TableCell>
                                <TableCell class="text-center">{{ item.posisi_former }}</TableCell>
                                <TableCell class="text-center">
                                    <span :class="['px-2 py-1 rounded text-[10px] font-bold', item.hasil_test === 'OK' ? 'bg-green-100 text-green-700' : 'bg-destructive/10 text-destructive']">
                                        {{ item.hasil_test }}
                                    </span>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </CardContent>
            </Card>
        </div>

        <Card v-if="thermalshock.keterangan" class="mt-6 border-none shadow-md border-l-4 border-l-primary">
            <CardHeader class="pb-2"><CardTitle class="text-sm">Keterangan / Catatan QC</CardTitle></CardHeader>
            <CardContent>
                <p class="text-sm text-muted-foreground italic">"{{ thermalshock.keterangan }}"</p>
            </CardContent>
        </Card>
    </div>
</template>

<style scoped>
@media print {
    /* Sembunyikan sidebar dan tombol saat print */
    .no-print, Button, Link {
        display: none !important;
    }
}
</style>
