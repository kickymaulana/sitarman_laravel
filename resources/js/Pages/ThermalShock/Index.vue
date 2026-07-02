<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { IconPlus, IconEye, IconSearch, IconX, IconFlame, IconHammer, IconFileSpreadsheet, IconEdit, IconLoader2 } from "@tabler/icons-vue";

import { ref, watch, computed } from "vue";
import axios from "axios";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshocks: {
        data: Array<{
            id: number;
            hari_tgl: string;

            // Parameter Suhu 180
            suhu_display_180: number;
            suhu_actual_180: number;
            suhu_awal_180: number;
            suhu_air_180: string;
            jam_awal_proses_180: string;
            jam_capai_suhu_180: string;
            jam_mulai_tembak_180: string;
            jam_selesai_tembak_180: string | null;
            hasil_test_180: string;

            // Parameter Suhu 200
            suhu_display_200: number;
            suhu_actual_200: number;
            suhu_awal_200: number;
            suhu_air_200: string;
            jam_awal_proses_200: string;
            jam_capai_suhu_200: string;
            jam_mulai_tembak_200: string;
            jam_selesai_tembak_200: string | null;
            hasil_test_200: string;

            thermal_pintu: { thermal_pintu: string } | null;
            user: { name: string } | null;
            kode_bakar: number;
            kode_tanah: string;
            oven: { oven: string } | null;
            customer: { customer: string; model: string; spesifikasi: string; size: string } | null;
            tinggi_former?: { tinggi_former: number } | null;
            tinggiFormer?: { tinggi_former: number } | null;
            jam_keluar_oven?: { jam_keluar_oven: string } | null;
            jamKeluarOven?: { jam_keluar_oven: string } | null;
            sampel: string;
            berat_former: number;
            tanggal_keluar_oven: string;
            tgl_produksi: string;
            posisi_former: number;
            keterangan: string;
        }>;
        links: any[];
        from: number;
        to: number;
        total: number;
    };
    filters: { search: string };
}>();

const search = ref(props.filters.search || "");
let timeout: any;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(
            route("thermalshock.index"),
            { search: value },
            { preserveState: true, replace: true },
        );
    }, 500);
});

const clearSearch = () => { search.value = ""; };
const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};

const selectedIds = ref<number[]>([]);

const isAllSelected = computed(() => {
    if (props.thermalshocks.data.length === 0) return false;
    return props.thermalshocks.data.every(item => selectedIds.value.includes(item.id));
});

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        const pageIds = props.thermalshocks.data.map(item => item.id);
        selectedIds.value = selectedIds.value.filter(id => !pageIds.includes(id));
    } else {
        props.thermalshocks.data.forEach(item => {
            if (!selectedIds.value.includes(item.id)) {
                selectedIds.value.push(item.id);
            }
        });
    }
};

const handleBulkEdit = () => {
    if (selectedIds.value.length === 0) return;
    router.get(route('thermalshock.bulkEdit'), {
        ids: selectedIds.value.join(',')
    });
};

const handleBulkEdit200 = () => {
    if (selectedIds.value.length === 0) return;
    router.get(route('thermalshock.bulkEdit200'), {
        ids: selectedIds.value.join(',')
    });
};

const startDate = ref("");
const endDate = ref("");
const isExporting = ref(false);

const handleExportCSVByDate = async () => {
    if (!startDate.value || !endDate.value) {
        alert("Silakan pilih Tanggal Mulai dan Tanggal Selesai terlebih dahulu!");
        return;
    }

    try {
        isExporting.value = true;
        const response = await axios.get(route('thermalshock.getExportData'), {
            params: { start_date: startDate.value, end_date: endDate.value }
        });

        const records = response.data;
        if (records.length === 0) {
            alert("Tidak ada data Thermal Shock ditemukan pada periode tanggal tersebut.");
            return;
        }

        const headers = [
            "ID", "Tanggal Proses", "Thermal Pintu", "Operator",
            "Hasil 180", "Suhu Awal 180", "Suhu Display 180", "Suhu Actual 180", "Suhu Air 180", "Jam Awal 180", "Capai Suhu 180", "Mulai Tembak 180", "Selesai Tembak 180",
            "Hasil 200", "Suhu Awal 200", "Suhu Display 200", "Suhu Actual 200", "Suhu Air 200", "Jam Awal 200", "Capai Suhu 200", "Mulai Tembak 200", "Selesai Tembak 200",
            "Kode Bakar", "Kode Tanah", "Oven Produksi", "Customer", "Model", "Size", "Spesifikasi",
            "Tinggi Former", "Jam Keluar Oven", "Sampel", "Berat Former", "Tgl Keluar Oven", "Tgl Produksi", "Posisi Former", "Keterangan"
        ];

        const rows = records.map((item: any) => {
            const tfObj = item.tinggi_former || item.tinggiFormer;
            const jkObj = item.jam_keluar_oven || item.jamKeluarOven;
            return [
                item.id,
                item.hari_tgl,
                `"${item.thermal_pintu?.thermal_pintu ?? '-'}"`,
                `"${item.user?.name ?? '-'}"`,

                `"${item.hasil_test_180}"`,
                item.suhu_awal_180, item.suhu_display_180, item.suhu_actual_180, `"${item.suhu_air_180}"`,
                item.jam_awal_proses_180 ? item.jam_awal_proses_180.substring(0, 5) : '-',
                item.jam_capai_suhu_180 ? item.jam_capai_suhu_180.substring(0, 5) : '-',
                item.jam_mulai_tembak_180 ? item.jam_mulai_tembak_180.substring(0, 5) : '-',
                item.jam_selesai_tembak_180 ? item.jam_selesai_tembak_180.substring(0, 5) : '-',

                `"${item.hasil_test_200}"`,
                item.suhu_awal_200, item.suhu_display_200, item.suhu_actual_200, `"${item.suhu_air_200}"`,
                item.jam_awal_proses_200 ? item.jam_awal_proses_200.substring(0, 5) : '-',
                item.jam_capai_suhu_200 ? item.jam_capai_suhu_200.substring(0, 5) : '-',
                item.jam_mulai_tembak_200 ? item.jam_mulai_tembak_200.substring(0, 5) : '-',
                item.jam_selesai_tembak_200 ? item.jam_selesai_tembak_200.substring(0, 5) : '-',

                item.kode_bakar ?? '-',
                `"${item.kode_tanah ?? '-'}"`,
                `"${item.oven?.oven ?? '-'}"`,
                `"${item.customer?.customer ?? '-'}"`,
                `"${item.customer?.model ?? '-'}"`,
                `"${item.customer?.size ?? '-'}"`,
                `"${item.customer?.spesifikasi ?? '-'}"`,
                tfObj?.tinggi_former ?? '-',
                jkObj?.jam_keluar_oven ? jkObj.jam_keluar_oven.substring(0, 5) : '-',
                `"${item.sampel ?? '-'}"`,
                item.berat_former,
                item.tanggal_keluar_oven ?? '-',
                item.tgl_produksi ?? '-',
                item.posisi_former,
                `"${item.keterangan ? item.keterangan.replace(/"/g, '""') : '-'}"`
            ];
        });

        const csvContent = [headers.join(";"), ...rows.map((e: any) => e.join(";"))].join("\n");
        const BOM = "\uFEFF";
        const blob = new Blob([BOM + csvContent], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);

        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", `Rekap_ThermalShock_Gabungan_${startDate.value}_to_${endDate.value}.csv`);
        link.style.visibility = "hidden";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    } catch (error) {
        console.error(error);
        alert("Terjadi kesalahan saat memproses data export.");
    } finally {
        isExporting.value = false;
    }
};
</script>

<template>
    <Head title="Thermal Shock" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconFlame class="size-6 text-primary" />
                    Daftar Lengkap Thermal Shock (Gabungan)
                </CardTitle>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-64">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari data..." class="pl-10 pr-10" />
                        <button v-if="search" @click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <div v-if="selectedIds.length > 0" class="flex flex-wrap items-center gap-2 w-full md:w-auto animation-fade-in">
                        <Button
                            @click="handleBulkEdit"
                            variant="default"
                            class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm text-xs h-9"
                        >
                            <IconEdit class="mr-1.5 size-4" /> Hasil Test ({{ selectedIds.length }})
                        </Button>

                        <Button
                            @click="handleBulkEdit200"
                            variant="default"
                            class="bg-amber-600 hover:bg-amber-700 text-white shadow-sm text-xs h-9"
                        >
                            <IconFlame class="mr-1.5 size-4" /> Set Suhu 200°C ({{ selectedIds.length }})
                        </Button>

                        <Button @click="selectedIds = []" variant="ghost" size="sm" class="text-xs h-9 text-muted-foreground">
                            Batal
                        </Button>
                    </div>

                    <div v-else class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                        <div class="flex flex-wrap items-center gap-2 border rounded-lg p-1 bg-zinc-50/50 dark:bg-zinc-900/50">
                            <div class="flex items-center gap-1">
                                <span class="text-xs font-medium text-muted-foreground px-1">Dari:</span>
                                <Input type="date" v-model="startDate" class="h-8 text-xs w-32 bg-background" />
                            </div>
                            <div class="flex items-center gap-1">
                                <span class="text-xs font-medium text-muted-foreground px-1">Sampai:</span>
                                <Input type="date" v-model="endDate" class="h-8 text-xs w-32 bg-background" />
                            </div>
                            <Button @click="handleExportCSVByDate" :disabled="isExporting" variant="outline" size="sm" class="h-8 border-emerald-600 text-emerald-600 hover:bg-emerald-50 text-xs font-semibold">
                                <IconLoader2 v-if="isExporting" class="mr-1 animate-spin size-3.5" />
                                <IconFileSpreadsheet v-else class="mr-1 size-3.5" /> Export
                            </Button>
                        </div>

                        <Button as-child class="bg-primary hover:bg-primary/90 shadow-md h-9 text-xs">
                            <Link :href="route('thermalshock.create')">
                                <IconPlus class="mr-1 size-4" /> Tambah Data
                            </Link>
                        </Button>
                    </div>
                </div>


            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-x-auto">

                    <Table class="w-full text-xs">
                        <TableHeader>
                            <TableRow class="bg-muted/50 whitespace-nowrap">
                                <TableHead class="w-12 text-center" rowspan="2">
                                    <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll" class="rounded border-zinc-300 text-primary focus:ring-primary size-4 cursor-pointer" />
                                </TableHead>
                                <TableHead class="text-center" rowspan="2">Aksi</TableHead>
                                <TableHead rowspan="2">Tanggal Proses</TableHead>
                                <TableHead rowspan="2">Thermal Pintu</TableHead>
                                <TableHead class="text-center bg-blue-50/50 dark:bg-blue-950/20" colspan="2">Hasil Pengujian</TableHead>
                                <TableHead class="text-center" colspan="6">Parameter Data Pengujian (Top: 180°C / Bottom: 200°C)</TableHead>
                                <TableHead rowspan="2">Operator</TableHead>
                                <TableHead class="text-center" colspan="10">Data Manufaktur Produk</TableHead>
                                <TableHead rowspan="2">Keterangan</TableHead>
                            </TableRow>
                            <TableRow class="bg-muted/30 whitespace-nowrap text-[11px]">
                                <TableHead class="text-center bg-blue-50/30 dark:bg-blue-950/10">Hasil 180</TableHead>
                                <TableHead class="text-center bg-blue-50/30 dark:bg-blue-950/10">Hasil 200</TableHead>
                                <TableHead class="text-center">Suhu Awal</TableHead>
                                <TableHead class="text-center">Suhu Display</TableHead>
                                <TableHead class="text-center">Suhu Actual</TableHead>
                                <TableHead class="text-center">Suhu Air</TableHead>
                                <TableHead class="text-center">Jam Awal / Capai</TableHead>
                                <TableHead class="text-center">Mulai / Selesai Tembak</TableHead>
                                <TableHead class="text-center">Kode Bakar / Tanah</TableHead>
                                <TableHead>Oven</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Model / Size</TableHead>
                                <TableHead>Spesifikasi</TableHead>
                                <TableHead class="text-center">Tinggi Former</TableHead>
                                <TableHead class="text-center">Jam Keluar Oven</TableHead>
                                <TableHead>Sampel</TableHead>
                                <TableHead class="text-center">Berat Former</TableHead>
                                <TableHead class="text-center">Posisi Former</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="thermalshocks.data.length === 0">
                                <TableCell colspan="25" class="h-24 text-center text-muted-foreground italic">Data tidak ditemukan.</TableCell>
                            </TableRow>

                            <TableRow v-for="item in thermalshocks.data" :key="item.id" class="hover:bg-muted/30 transition-colors whitespace-nowrap">
                                <TableCell class="text-center">
                                    <input type="checkbox" :value="item.id" v-model="selectedIds" class="rounded border-zinc-300 text-primary focus:ring-primary size-4 cursor-pointer" />
                                </TableCell>
                                <TableCell class="text-center">
                                    <Button variant="ghost" size="icon" class="size-7 hover:text-primary" as-child title="Lihat/Edit Detail">
                                        <Link :href="route('thermalshock.edit', item.id)"><IconEye class="size-4" /></Link>
                                    </Button>
                                </TableCell>
                                <TableCell class="font-medium">
                                    {{ new Date(item.hari_tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) }}
                                </TableCell>
                                <TableCell class="font-semibold text-zinc-700 dark:text-zinc-300">
                                    {{ item.thermal_pintu?.thermal_pintu ?? '-' }}
                                </TableCell>

                                <TableCell class="text-center bg-blue-50/10 dark:bg-blue-950/5">
                                    <span :class="{ 'text-emerald-600 font-bold': item.hasil_test_180 === 'OK', 'text-rose-600 font-bold': item.hasil_test_180 === 'NG', 'text-zinc-400': item.hasil_test_180 === 'Belum Tes' }">
                                        {{ item.hasil_test_180 }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-center bg-blue-50/10 dark:bg-blue-950/5">
                                    <span :class="{ 'text-emerald-600 font-bold': item.hasil_test_200 === 'OK', 'text-rose-600 font-bold': item.hasil_test_200 === 'NG', 'text-zinc-400': item.hasil_test_200 === 'Belum Tes' }">
                                        {{ item.hasil_test_200 }}
                                    </span>
                                </TableCell>

                                <TableCell class="text-center leading-tight">
                                    <div class="text-blue-600 font-medium">{{ item.suhu_awal_180 }}°C</div>
                                    <div class="text-amber-600 font-medium border-t border-zinc-100 dark:border-zinc-800 mt-0.5 pt-0.5">{{ item.suhu_awal_200 }}°C</div>
                                </TableCell>
                                <TableCell class="text-center leading-tight">
                                    <div class="text-blue-600">{{ item.suhu_display_180 }}°C</div>
                                    <div class="text-amber-600 border-t border-zinc-100 dark:border-zinc-800 mt-0.5 pt-0.5">{{ item.suhu_display_200 }}°C</div>
                                </TableCell>
                                <TableCell class="text-center leading-tight">
                                    <div class="text-blue-600 font-semibold">{{ item.suhu_actual_180 }}°C</div>
                                    <div class="text-amber-600 font-semibold border-t border-zinc-100 dark:border-zinc-800 mt-0.5 pt-0.5">{{ item.suhu_actual_200 }}°C</div>
                                </TableCell>
                                <TableCell class="text-center leading-tight">
                                    <div class="text-zinc-600 dark:text-zinc-400">{{ item.suhu_air_180 }}</div>
                                    <div class="text-zinc-600 dark:text-zinc-400 border-t border-zinc-100 dark:border-zinc-800 mt-0.5 pt-0.5">{{ item.suhu_air_200 }}</div>
                                </TableCell>
                                <TableCell class="text-center leading-tight text-[11px]">
                                    <div class="text-blue-600">{{ item.jam_awal_proses_180?.substring(0,5) }} → {{ item.jam_capai_suhu_180?.substring(0,5) }}</div>
                                    <div class="text-amber-600 border-t border-zinc-100 dark:border-zinc-800 mt-0.5 pt-0.5">{{ item.jam_awal_proses_200?.substring(0,5) }} → {{ item.jam_capai_suhu_200?.substring(0,5) }}</div>
                                </TableCell>
                                <TableCell class="text-center leading-tight text-[11px]">
                                    <div class="text-blue-600">{{ item.jam_mulai_tembak_180?.substring(0,5) }} - {{ item.jam_selesai_tembak_180?.substring(0,5) }}</div>
                                    <div class="text-amber-600 border-t border-zinc-100 dark:border-zinc-800 mt-0.5 pt-0.5">{{ item.jam_mulai_tembak_200?.substring(0,5) }} - {{ item.jam_selesai_tembak_200?.substring(0,5) }}</div>
                                </TableCell>

                                <TableCell class="text-muted-foreground align-middle">{{ item.user?.name ?? '-' }}</TableCell>

                                <TableCell class="text-center leading-tight">
                                    <div class="font-medium text-zinc-800 dark:text-zinc-200">{{ item.kode_bakar }}</div>
                                    <div class="text-muted-foreground text-[10px] border-t border-zinc-100 dark:border-zinc-800 mt-0.5 pt-0.5">{{ item.kode_tanah }}</div>
                                </TableCell>
                                <TableCell class="align-middle">{{ item.oven?.oven ?? '-' }}</TableCell>
                                <TableCell class="font-medium text-primary align-middle">{{ item.customer?.customer ?? '-' }}</TableCell>
                                <TableCell class="align-middle">
                                    <div>{{ item.customer?.model ?? '-' }}</div>
                                    <div class="text-muted-foreground text-[11px] font-mono">{{ item.customer?.size ?? '-' }}</div>
                                </TableCell>
                                <TableCell class="max-w-[150px] truncate align-middle" :title="item.customer?.spesifikasi">{{ item.customer?.spesifikasi ?? '-' }}</TableCell>
                                <TableCell class="text-center font-medium text-amber-600 dark:text-amber-400 align-middle">
                                    {{ (item.tinggi_former || item.tinggiFormer) ? `${(item.tinggi_former || item.tinggiFormer).tinggi_former} mm` : '-' }}
                                </TableCell>
                                <TableCell class="text-center align-middle">
                                    {{ (item.jam_keluar_oven || item.jamKeluarOven) ? (item.jam_keluar_oven || item.jamKeluarOven).jam_keluar_oven.substring(0, 5) : '-' }}
                                </TableCell>
                                <TableCell class="align-middle">{{ item.sampel }}</TableCell>
                                <TableCell class="text-center align-middle">{{ item.berat_former }} g</TableCell>
                                <TableCell class="text-center align-middle">{{ item.posisi_former }}</TableCell>

                                <TableCell class="max-w-xs truncate text-muted-foreground align-middle" :title="item.keterangan">{{ item.keterangan }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>

                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground italic font-medium">Menampilkan {{ thermalshocks.from ?? 0 }} - {{ thermalshocks.to ?? 0 }} dari {{ thermalshocks.total }} data</p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in thermalshocks.links" :key="k">
                            <Button v-if="link.url === null" variant="outline" size="sm" disabled class="opacity-50 text-xs px-3 h-8" v-html="cleanLabel(link.label)" />
                            <Button v-else as-child variant="outline" size="sm" class="text-xs px-3 h-8" :class="{ 'bg-primary text-primary-foreground': link.active }">
                                <Link :href="link.url" v-html="cleanLabel(link.label)" />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
