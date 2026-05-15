<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { IconArrowLeft, IconPrinter, IconFlask, IconClipboardList } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: any;
}>();

// Helper format jam
const formatTime = (time: string) => time ? time.substring(0, 5) : "-";

// Helper format tanggal Indonesia
const formatDate = (date: string) => {
    if (!date) return "-";
    return new Date(date).toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    });
};
</script>

<template>
    <Head title="Detail Thermal Shock" />
    <div class="p-4 md:p-8 pt-2">
        <div class="flex items-center justify-between mb-6 no-print">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('thermalshock.index')"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <div>
                    <h2 class="text-2xl font-bold tracking-tight">Laporan Test Thermal Shock</h2>
                    <p class="text-sm text-muted-foreground">No. Dokumen: MDIFM-QA14 (Rev. 07)</p>
                </div>
            </div>
            <Button @click="window.print()" variant="default" class="bg-primary shadow-lg">
                <IconPrinter class="mr-2 size-4" /> Cetak Laporan
            </Button>
        </div>

        <div class="space-y-6">
            <Card class="border-none shadow-md overflow-hidden">
                <CardHeader class="bg-muted/20 border-b">
                    <CardTitle class="text-md flex items-center gap-2">
                        <IconFlask class="size-5 text-primary" /> Informasi Header Pengetesan
                    </CardTitle>
                </CardHeader>
                <CardContent class="pt-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-y-6 gap-x-4">
                        <div>
                            <Label class="text-muted-foreground text-xs uppercase">Tanggal Test</Label>
                            <p class="font-bold">{{ formatDate(thermalshock.hari_tgl) }}</p>
                        </div>
                        <div>
                            <Label class="text-muted-foreground text-xs uppercase">Suhu Testing</Label>
                            <p class="font-bold text-orange-600">{{ thermalshock.suhu_testing }}°C</p>
                        </div>
                        <div>
                            <Label class="text-muted-foreground text-xs uppercase">Suhu Motor</Label>
                            <p class="font-bold">{{ thermalshock.suhu_motor || '-' }}</p>
                        </div>
                        <div>
                            <Label class="text-muted-foreground text-xs uppercase">Suhu Air</Label>
                            <p class="font-bold">{{ thermalshock.suhu_air }}</p>
                        </div>

                        <div class="border-t pt-2">
                            <Label class="text-muted-foreground text-xs uppercase">Suhu Display</Label>
                            <p class="font-semibold">{{ thermalshock.suhu_display }}°C</p>
                        </div>
                        <div class="border-t pt-2">
                            <Label class="text-muted-foreground text-xs uppercase">Suhu Actual</Label>
                            <p class="font-semibold">{{ thermalshock.suhu_actual }}°C</p>
                        </div>
                        <div class="border-t pt-2">
                            <Label class="text-muted-foreground text-xs uppercase">Suhu Awal</Label>
                            <p class="font-semibold">{{ thermalshock.suhu_awal }}°C</p>
                        </div>
                        <div class="border-t pt-2 text-muted-foreground italic flex items-center text-xs">Informasi Suhu</div>

                        <div class="bg-muted/30 p-2 rounded">
                            <Label class="text-muted-foreground text-[10px] uppercase">Jam Awal</Label>
                            <p class="text-sm font-bold">{{ formatTime(thermalshock.jam_awal_proses) }}</p>
                        </div>
                        <div class="bg-muted/30 p-2 rounded">
                            <Label class="text-muted-foreground text-[10px] uppercase">Capai Suhu</Label>
                            <p class="text-sm font-bold">{{ formatTime(thermalshock.jam_capai_suhu) }}</p>
                        </div>
                        <div class="bg-muted/30 p-2 rounded">
                            <Label class="text-muted-foreground text-[10px] uppercase">Mulai Tembak</Label>
                            <p class="text-sm font-bold">{{ formatTime(thermalshock.jam_mulai_tembak) }}</p>
                        </div>
                        <div class="bg-muted/30 p-2 rounded">
                            <Label class="text-muted-foreground text-[10px] uppercase">Selesai Tembak</Label>
                            <p class="text-sm font-bold">{{ formatTime(thermalshock.jam_selesai_tembak) }}</p>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-md overflow-hidden">
                <CardHeader class="bg-muted/20 border-b flex flex-row items-center justify-between">
                    <CardTitle class="text-md flex items-center gap-2">
                        <IconClipboardList class="size-5 text-primary" /> Daftar Item Isi Oven
                    </CardTitle>
                    <span class="text-xs font-medium px-2 py-1 bg-white rounded-full shadow-sm">{{ thermalshock.details.length }} Item</span>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow class="bg-muted/50">
                                    <TableHead class="text-xs uppercase">Oven/Kode</TableHead>
                                    <TableHead class="text-xs uppercase">Customer</TableHead>
                                    <TableHead class="text-xs uppercase">Model/Size</TableHead>
                                    <TableHead class="text-xs uppercase">Spesifikasi</TableHead>
                                    <TableHead class="text-xs uppercase text-center">Berat (g)</TableHead>
                                    <TableHead class="text-xs uppercase">Keluar Oven</TableHead>
                                    <TableHead class="text-xs uppercase">Tgl Prod</TableHead>
                                    <TableHead class="text-xs uppercase text-center">Pos</TableHead>
                                    <TableHead class="text-xs uppercase text-center">Hasil</TableHead>
                                    <TableHead class="text-xs uppercase">Keterangan</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="item in thermalshock.details" :key="item.id" class="hover:bg-muted/20">
                                    <TableCell class="font-bold">{{ item.oven_kode_tanah }}</TableCell>
                                    <TableCell>{{ item.customer?.customer }}</TableCell>
                                    <TableCell class="font-medium text-primary">{{ item.model_size?.modelsize }}</TableCell>
                                    <TableCell>{{ item.spesifikasi }}</TableCell>
                                    <TableCell class="text-center font-mono">{{ item.berat_former }}</TableCell>
                                    <TableCell class="whitespace-nowrap">{{ formatDate(item.tanggal_keluar_oven) }}</TableCell>
                                    <TableCell class="whitespace-nowrap">{{ formatDate(item.tgl_produksi) }}</TableCell>
                                    <TableCell class="text-center font-bold text-muted-foreground">{{ item.posisi_former }}</TableCell>
                                    <TableCell class="text-center">
                                        <span :class="['px-2 py-1 rounded text-[10px] font-bold shadow-sm', item.hasil_test === 'OK' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-destructive/10 text-destructive border border-destructive/20']">
                                            {{ item.hasil_test }}
                                        </span>
                                    </TableCell>
                                    <TableCell class="text-xs italic text-muted-foreground">
                                        {{ item.keterangan || '-' }}
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>
        </div>

        <div class="hidden print:block mt-12">
            <div class="grid grid-cols-3 gap-4 text-center">
                <div class="space-y-12">
                    <p class="text-sm">Dibuat Oleh,</p>
                    <p class="text-sm font-bold border-t border-black pt-2 mx-8">Operator QC</p>
                </div>
                <div></div>
                <div class="space-y-12">
                    <p class="text-sm">Diketahui Oleh,</p>
                    <p class="text-sm font-bold border-t border-black pt-2 mx-8">QC Manager</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@media print {
    .p-4 { padding: 0 !important; }
    .shadow-md { shadow: none !important; border: 1px solid #ddd !important; }
    .no-print { display: none !important; }
    body { background: white !important; }
}
</style>
