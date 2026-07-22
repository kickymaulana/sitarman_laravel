<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2, IconChecklist, IconFlame } from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshocks: Array<{
        id: number;
        posisi_former: number;
        hari_tgl: string;
        customer: { customer: string; model: string; spesifikasi: string; size: string } | null;

        // Existing values
        suhu_awal_180: number;
        suhu_display_180: number;
        suhu_actual_180: number;
        suhu_air_180: string;
        jam_awal_proses_180: string;
        jam_capai_suhu_180: string;
        jam_mulai_tembak_180: string;
        jam_selesai_tembak_180: string;

        suhu_awal_200: number;
        suhu_display_200: number;
        suhu_actual_200: number;
        suhu_air_200: string;
        jam_awal_proses_200: string;
        jam_capai_suhu_200: string;
        jam_mulai_tembak_200: string;
        jam_selesai_tembak_200: string;

        hasil_test_180: string;
        hasil_180: number | null;
        hasil_test_200: string;
        hasil_200: number | null;
        keterangan: string;
    }>;
    selectedIds: Array<string>;
}>();

// Ambil nilai default dari baris pertama sebagai nilai awal header form
const firstRow = props.thermalshocks[0] || {};

const form = useForm({
    // ===== MASTER PARAMETER HEADER 180°C =====
    suhu_awal_180: firstRow.suhu_awal_180 ?? 0,
    suhu_display_180: firstRow.suhu_display_180 ?? 0,
    suhu_actual_180: firstRow.suhu_actual_180 ?? 0,
    suhu_air_180: firstRow.suhu_air_180 ?? "-",
    jam_awal_proses_180: firstRow.jam_awal_proses_180 ? firstRow.jam_awal_proses_180.substring(0, 5) : "",
    jam_capai_suhu_180: firstRow.jam_capai_suhu_180 ? firstRow.jam_capai_suhu_180.substring(0, 5) : "",
    jam_mulai_tembak_180: firstRow.jam_mulai_tembak_180 ? firstRow.jam_mulai_tembak_180.substring(0, 5) : "",
    jam_selesai_tembak_180: firstRow.jam_selesai_tembak_180 ? firstRow.jam_selesai_tembak_180.substring(0, 5) : "",

    // ===== MASTER PARAMETER HEADER 200°C =====
    suhu_awal_200: firstRow.suhu_awal_200 ?? 0,
    suhu_display_200: firstRow.suhu_display_200 ?? 0,
    suhu_actual_200: firstRow.suhu_actual_200 ?? 0,
    suhu_air_200: firstRow.suhu_air_200 ?? "-",
    jam_awal_proses_200: firstRow.jam_awal_proses_200 ? firstRow.jam_awal_proses_200.substring(0, 5) : "",
    jam_capai_suhu_200: firstRow.jam_capai_suhu_200 ? firstRow.jam_capai_suhu_200.substring(0, 5) : "",
    jam_mulai_tembak_200: firstRow.jam_mulai_tembak_200 ? firstRow.jam_mulai_tembak_200.substring(0, 5) : "",
    jam_selesai_tembak_200: firstRow.jam_selesai_tembak_200 ? firstRow.jam_selesai_tembak_200.substring(0, 5) : "",

    // ===== LIST DETAIL HASIL TEST PER PRODUK =====
    records: props.thermalshocks.map(item => ({
        id: item.id,
        posisi_former: item.posisi_former,
        customer_name: item.customer?.customer ?? '-',
        modelsize_name: item.customer ? `${item.customer.model} (${item.customer.size})` : '-',
        hasil_test_180: item.hasil_test_180 || 'Belum Tes',
        hasil_180: item.hasil_180 ?? 0,
        hasil_test_200: item.hasil_test_200 || 'Belum Tes',
        hasil_200: item.hasil_200 ?? 0,
        keterangan: item.keterangan || '-'
    }))
});

const showHeaderForm = ref(true);

const formatTimeInput = (field: keyof typeof form, event: Event) => {
    const target = event.target as HTMLInputElement;
    let val = target.value.replace(/\D/g, '');
    if (val.length > 4) val = val.substring(0, 4);

    if (val.length > 2) {
        let hours = val.substring(0, 2);
        if (parseInt(hours) > 23) hours = '23';
        let minutes = val.substring(2);
        if (parseInt(minutes) > 59) minutes = '59';
        val = hours + ':' + minutes;
    } else if (val.length === 2 && parseInt(val) > 23) {
        val = '23';
    }
    // @ts-ignore
    form[field] = val;
};

const submit = () => {
    form.put(route('thermalshock.bulkUpdate'));
};

// LOGIKA AWAL ANDA (TETAP SAMA SEPERTI ASLI)
watch(
    () => form.records,
    (newRecords) => {
        newRecords.forEach((row) => {
            if (row.hasil_test_180 === 'NG') {
                row.hasil_test_200 = 'Pecah 180';
                row.hasil_200 = 0;
            } else if (row.hasil_test_200 === 'Pecah 180') {
                row.hasil_test_200 = 'Belum Tes';
            }
        });
    },
    { deep: true }
);
</script>

<template>
    <Head title="Bulk Edit Hasil Test & Parameter" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('thermalshock.index')">
                        <IconArrowLeft class="size-4" />
                    </Link>
                </Button>
                <h2 class="text-2xl md:text-3xl font-bold tracking-tight">Bulk Input Hasil & Parameter</h2>
            </div>

            <Button type="button" variant="outline" size="sm" @click="showHeaderForm = !showHeaderForm" class="text-xs">
                {{ showHeaderForm ? 'Sembunyikan Header Parameter' : 'Tampilkan Header Parameter' }}
            </Button>
        </div>

        <form @submit.prevent="submit" class="space-y-6 max-w-6xl mx-auto w-full">

            <!-- SECTION HEADER PARAMETER (SUHU 180 & 200) -->
            <div v-if="showHeaderForm" class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <!-- CARD PARAMETER 180°C -->
                <Card class="border border-blue-200 dark:border-blue-900 shadow-sm">
                    <CardHeader class="bg-blue-50/50 dark:bg-blue-950/20 py-3 px-4 border-b">
                        <CardTitle class="text-sm font-bold text-blue-700 dark:text-blue-400 flex items-center gap-1.5">
                            <IconFlame class="size-4" /> Parameter Massal Suhu 180°C
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="p-4 space-y-3">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                            <div>
                                <Label class="text-[11px]">Suhu Awal</Label>
                                <Input type="number" v-model.number="form.suhu_awal_180" class="h-8 text-xs" />
                            </div>
                            <div>
                                <Label class="text-[11px]">Suhu Display</Label>
                                <Input type="number" v-model.number="form.suhu_display_180" class="h-8 text-xs" />
                            </div>
                            <div>
                                <Label class="text-[11px]">Suhu Actual</Label>
                                <Input type="number" v-model.number="form.suhu_actual_180" class="h-8 text-xs" />
                            </div>
                            <div>
                                <Label class="text-[11px]">Suhu Air</Label>
                                <Input type="text" v-model="form.suhu_air_180" class="h-8 text-xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs pt-1 border-t">
                            <div>
                                <Label class="text-[10px] text-muted-foreground">Jam Awal</Label>
                                <Input type="text" v-model="form.jam_awal_proses_180" @input="formatTimeInput('jam_awal_proses_180', $event)" placeholder="00:00" class="h-8 text-xs text-center font-mono" />
                            </div>
                            <div>
                                <Label class="text-[10px] text-muted-foreground">Jam Capai</Label>
                                <Input type="text" v-model="form.jam_capai_suhu_180" @input="formatTimeInput('jam_capai_suhu_180', $event)" placeholder="00:00" class="h-8 text-xs text-center font-mono" />
                            </div>
                            <div>
                                <Label class="text-[10px] text-muted-foreground">Mulai Tembak</Label>
                                <Input type="text" v-model="form.jam_mulai_tembak_180" @input="formatTimeInput('jam_mulai_tembak_180', $event)" placeholder="00:00" class="h-8 text-xs text-center font-mono" />
                            </div>
                            <div>
                                <Label class="text-[10px] text-muted-foreground">Selesai Tembak</Label>
                                <Input type="text" v-model="form.jam_selesai_tembak_180" @input="formatTimeInput('jam_selesai_tembak_180', $event)" placeholder="00:00" class="h-8 text-xs text-center font-mono" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- CARD PARAMETER 200°C -->
                <Card class="border border-amber-200 dark:border-amber-900 shadow-sm">
                    <CardHeader class="bg-amber-50/50 dark:bg-amber-950/20 py-3 px-4 border-b">
                        <CardTitle class="text-sm font-bold text-amber-700 dark:text-amber-400 flex items-center gap-1.5">
                            <IconFlame class="size-4" /> Parameter Massal Suhu 200°C
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="p-4 space-y-3">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                            <div>
                                <Label class="text-[11px]">Suhu Awal</Label>
                                <Input type="number" v-model.number="form.suhu_awal_200" class="h-8 text-xs" />
                            </div>
                            <div>
                                <Label class="text-[11px]">Suhu Display</Label>
                                <Input type="number" v-model.number="form.suhu_display_200" class="h-8 text-xs" />
                            </div>
                            <div>
                                <Label class="text-[11px]">Suhu Actual</Label>
                                <Input type="number" v-model.number="form.suhu_actual_200" class="h-8 text-xs" />
                            </div>
                            <div>
                                <Label class="text-[11px]">Suhu Air</Label>
                                <Input type="text" v-model="form.suhu_air_200" class="h-8 text-xs" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs pt-1 border-t">
                            <div>
                                <Label class="text-[10px] text-muted-foreground">Jam Awal</Label>
                                <Input type="text" v-model="form.jam_awal_proses_200" @input="formatTimeInput('jam_awal_proses_200', $event)" placeholder="00:00" class="h-8 text-xs text-center font-mono" />
                            </div>
                            <div>
                                <Label class="text-[10px] text-muted-foreground">Jam Capai</Label>
                                <Input type="text" v-model="form.jam_capai_suhu_200" @input="formatTimeInput('jam_capai_suhu_200', $event)" placeholder="00:00" class="h-8 text-xs text-center font-mono" />
                            </div>
                            <div>
                                <Label class="text-[10px] text-muted-foreground">Mulai Tembak</Label>
                                <Input type="text" v-model="form.jam_mulai_tembak_200" @input="formatTimeInput('jam_mulai_tembak_200', $event)" placeholder="00:00" class="h-8 text-xs text-center font-mono" />
                            </div>
                            <div>
                                <Label class="text-[10px] text-muted-foreground">Selesai Tembak</Label>
                                <Input type="text" v-model="form.jam_selesai_tembak_200" @input="formatTimeInput('jam_selesai_tembak_200', $event)" placeholder="00:00" class="h-8 text-xs text-center font-mono" />
                            </div>
                        </div>
                    </CardContent>
                </Card>

            </div>

            <!-- TABEL HASIL TEST (BOTTOM SECTION) -->
            <Card class="border-none shadow-md">
                <CardHeader class="border-b bg-zinc-50/50 dark:bg-zinc-900/50 py-3">
                    <CardTitle class="text-emerald-600 flex items-center gap-2 text-base">
                        <IconChecklist class="size-5" /> Mengisi {{ form.records.length }} Data Produk (Berurutan Posisi Former)
                    </CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <Table class="w-full text-xs">
                            <TableHeader>
                                <TableRow class="bg-muted/50 whitespace-nowrap">
                                    <TableHead class="w-16 text-center font-bold" rowspan="2">Posisi</TableHead>
                                    <TableHead rowspan="2">Customer / Model</TableHead>
                                    <TableHead class="text-center bg-blue-50/40 dark:bg-blue-950/20" colspan="2">Pengujian 180°C</TableHead>
                                    <TableHead class="text-center bg-amber-50/40 dark:bg-amber-950/20" colspan="2">Pengujian 200°C</TableHead>
                                    <TableHead class="min-w-[180px]" rowspan="2">Keterangan / Defect</TableHead>
                                </TableRow>
                                <TableRow class="bg-muted/30 whitespace-nowrap text-[11px]">
                                    <TableHead class="w-36 bg-blue-50/20 dark:bg-blue-950/10">Status 180</TableHead>
                                    <TableHead class="w-24 bg-blue-50/20 dark:bg-blue-950/10 text-center">Hasil 180</TableHead>
                                    <TableHead class="w-36 bg-amber-50/20 dark:bg-amber-950/10">Status 200</TableHead>
                                    <TableHead class="w-24 bg-amber-50/20 dark:bg-amber-950/10 text-center">Hasil 200</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(row, index) in form.records" :key="row.id" class="hover:bg-muted/20 whitespace-nowrap">
                                    <TableCell class="text-center font-bold text-base text-primary align-middle">
                                        {{ row.posisi_former }}
                                    </TableCell>

                                    <TableCell class="align-middle">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ row.customer_name }}</span>
                                            <span class="text-[11px] text-muted-foreground">{{ row.modelsize_name }}</span>
                                        </div>
                                    </TableCell>

                                    <!-- Status & Hasil 180 dengan Hiasan Warna -->
                                    <TableCell
                                        class="align-middle transition-colors duration-200"
                                        :class="{
                                            'bg-rose-50/50 dark:bg-rose-950/20': row.hasil_test_180 === 'NG',
                                            'bg-emerald-50/50 dark:bg-emerald-950/20': row.hasil_test_180 === 'OK',
                                            'bg-blue-50/5 dark:bg-blue-950/5': row.hasil_test_180 === 'Belum Tes'
                                        }"
                                    >
                                        <Select v-model="form.records[index].hasil_test_180">
                                            <SelectTrigger
                                                class="h-8 text-xs font-semibold transition-all duration-200"
                                                :class="{
                                                    'border-rose-500 bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 ring-2 ring-rose-200 dark:ring-rose-900': row.hasil_test_180 === 'NG',
                                                    'border-emerald-500 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300': row.hasil_test_180 === 'OK'
                                                }"
                                            >
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Belum Tes">Belum Tes</SelectItem>
                                                <SelectItem value="OK" class="text-emerald-600 font-semibold">OK</SelectItem>
                                                <SelectItem value="NG" class="text-rose-600 font-semibold">NG</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </TableCell>

                                    <TableCell class="align-middle bg-blue-50/5 dark:bg-blue-950/5">
                                        <Input
                                            type="number"
                                            v-model.number="form.records[index].hasil_180"
                                            class="h-8 text-xs text-center font-mono"
                                            min="0"
                                            @focus="$event.target.select()"
                                        />
                                    </TableCell>

                                    <!-- Status & Hasil 200 dengan Hiasan Warna Merah untuk (NG & Pecah 180) -->
                                    <TableCell
                                        class="align-middle transition-colors duration-200"
                                        :class="{
                                            'bg-rose-50/50 dark:bg-rose-950/20': row.hasil_test_200 === 'NG' || row.hasil_test_200 === 'Pecah 180',
                                            'bg-emerald-50/50 dark:bg-emerald-950/20': row.hasil_test_200 === 'OK',
                                            'bg-amber-50/5 dark:bg-amber-950/5': row.hasil_test_200 === 'Belum Tes'
                                        }"
                                    >
                                        <Select v-model="form.records[index].hasil_test_200">
                                            <SelectTrigger
                                                class="h-8 text-xs font-semibold transition-all duration-200"
                                                :class="{
                                                    'border-rose-500 bg-rose-50 text-rose-700 dark:bg-rose-950 dark:text-rose-300 ring-2 ring-rose-200 dark:ring-rose-900': row.hasil_test_200 === 'NG' || row.hasil_test_200 === 'Pecah 180',
                                                    'border-emerald-500 bg-emerald-50 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300': row.hasil_test_200 === 'OK'
                                                }"
                                            >
                                                <SelectValue />
                                            </SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Belum Tes">Belum Tes</SelectItem>
                                                <SelectItem value="OK" class="text-emerald-600 font-semibold">OK</SelectItem>
                                                <SelectItem value="NG" class="text-rose-600 font-semibold">NG</SelectItem>
                                                <SelectItem value="Pecah 180" class="text-rose-600 font-semibold">Pecah 180</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </TableCell>

                                    <TableCell class="align-middle bg-amber-50/5 dark:bg-amber-950/5">
                                        <Input
                                            type="number"
                                            v-model.number="form.records[index].hasil_200"
                                            class="h-8 text-xs text-center font-mono"
                                            min="0"
                                            @focus="$event.target.select()"
                                        />
                                    </TableCell>

                                    <!-- Keterangan -->
                                    <TableCell class="pr-4 align-middle">
                                        <Input
                                            type="text"
                                            v-model="form.records[index].keterangan"
                                            class="h-8 text-xs"
                                            placeholder="Catatan defect..."
                                        />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <Button type="submit" :disabled="form.processing" class="w-full bg-emerald-600 hover:bg-emerald-700 shadow-md h-11 text-sm font-medium text-white">
                <IconLoader2 v-if="form.processing" class="mr-2 animate-spin size-5" />
                <IconDeviceFloppy v-else class="mr-2 size-5" /> Simpan Perubahan Parameter & Hasil Test Massal
            </Button>
        </form>
    </div>
</template>
