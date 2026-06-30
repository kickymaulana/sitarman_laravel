<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { IconPlus, IconEye, IconSearch, IconX, IconFlame, IconHammer, IconFileSpreadsheet, IconCopy, IconEdit, IconLoader2 } from "@tabler/icons-vue";

import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger
} from "@/components/ui/alert-dialog";
import { ref, watch, computed } from "vue";
import axios from "axios";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshocks: {
        data: Array<{
            id: number;
            hari_tgl: string;
            suhu_testing: string;
            suhu_display: number;
            suhu_actual: number;
            jam_awal_proses: string;
            jam_capai_suhu: string;
            suhu_awal: number;
            suhu_air: string;
            jam_mulai_tembak: string;
            jam_selesai_tembak: string | null;
            thermal_oven: { thermal_oven: string } | null;
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
            hasil_test_180: string;
            hasil_test_200: string;
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

const isCopyModalOpen = ref(false);
const targetSuhu = ref("200");

const handleBulkCopy = () => {
    if (selectedIds.value.length === 0) return;

    router.post(route('thermalshock.bulkReplicate'), {
        ids: selectedIds.value,
        target_suhu: targetSuhu.value
    }, {
        onSuccess: () => {
            selectedIds.value = [];
            isCopyModalOpen.value = false;
        },
        preserveScroll: true
    });
};

const handleBulkEdit = () => {
    if (selectedIds.value.length === 0) return;
    router.get(route('thermalshock.bulkEdit'), {
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
            "ID", "Tanggal Proses", "Suhu Testing", "Suhu Display", "Suhu Actual",
            "Jam Awal", "Capai Suhu", "Suhu Awal", "Suhu Air", "Mulai Tembak", "Selesai Tembak",
            "Thermal Oven", "Thermal Pintu", "Operator", "Kode Bakar", "Kode Tanah",
            "Oven Produksi", "Customer", "Model", "Size", "Spesifikasi", "Tinggi Former",
            "Jam Keluar Oven", "Sampel", "Berat Former", "Tgl Keluar Oven", "Tgl Produksi",
            "Posisi Former", "Hasil 180", "Hasil 200", "Keterangan"
        ];

        const rows = records.map((item: any) => {
            const tfObj = item.tinggi_former || item.tinggiFormer;
            const jkObj = item.jam_keluar_oven || item.jamKeluarOven;
            return [
                item.id,
                item.hari_tgl,
                `"${item.suhu_testing}°C"`,
                item.suhu_display,
                item.suhu_actual,
                item.jam_awal_proses ? item.jam_awal_proses.substring(0, 5) : '-',
                item.jam_capai_suhu ? item.jam_capai_suhu.substring(0, 5) : '-',
                item.suhu_awal,
                `"${item.suhu_air}"`,
                item.jam_mulai_tembak ? item.jam_mulai_tembak.substring(0, 5) : '-',
                item.jam_selesai_tembak ? item.jam_selesai_tembak.substring(0, 5) : '-',
                `"${item.thermal_oven?.thermal_oven ?? '-'}"`,
                `"${item.thermal_pintu?.thermal_pintu ?? '-'}"`,
                `"${item.user?.name ?? '-'}"`,
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
                `"${item.hasil_test_180}"`,
                `"${item.hasil_test_200}"`,
                `"${item.keterangan ? item.keterangan.replace(/"/g, '""') : '-'}"`
            ];
        });

        const csvContent = [headers.join(";"), ...rows.map((e: any) => e.join(";"))].join("\n");
        const BOM = "\uFEFF";
        const blob = new Blob([BOM + csvContent], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);

        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", `Rekap_ThermalShock_${startDate.value}_s.d_${endDate.value}.csv`);
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
                    Daftar Lengkap Thermal Shock
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari data..." class="pl-10 pr-10" />
                        <button v-if="search" @click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <Button
                        v-if="selectedIds.length > 0"
                        @click="handleBulkEdit"
                        variant="default"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white shadow-md"
                    >
                        <IconEdit class="mr-2 size-4" /> Input Hasil ({{ selectedIds.length }})
                    </Button>

                    <AlertDialog :open="isCopyModalOpen" @update:open="isCopyModalOpen = $event">
                        <AlertDialogTrigger as-child>
                            <Button
                                v-if="selectedIds.length > 0"
                                variant="default"
                                class="bg-amber-600 hover:bg-amber-700 text-white shadow-md"
                            >
                                <IconCopy class="mr-2 size-4" /> Copy Terpilih ({{ selectedIds.length }})
                            </Button>
                        </AlertDialogTrigger>

                        <AlertDialogContent class="max-w-md bg-white dark:bg-zinc-900">
                            <AlertDialogHeader>
                                <AlertDialogTitle class="flex items-center gap-2">
                                    <IconCopy class="size-5 text-amber-600" /> Bulk Copy Record Data
                                </AlertDialogTitle>
                                <AlertDialogDescription class="pt-2 text-zinc-600 dark:text-zinc-400">
                                    Anda memilih <span class="font-bold text-foreground">{{ selectedIds.length }} data</span> untuk di-duplikasi. Tentukan target temperatur pengujian baru:
                                </AlertDialogDescription>
                            </AlertDialogHeader>

                            <div class="py-4 flex flex-col gap-3">
                                <Label class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Target Suhu Testing Baru</Label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center justify-between rounded-lg border p-3 cursor-pointer" :class="{ 'border-amber-600 bg-amber-50/40': targetSuhu === '180' }">
                                        <span class="text-sm font-medium">Suhu 180 °C</span>
                                        <input type="radio" value="180" v-model="targetSuhu" class="text-amber-600 focus:ring-amber-600 size-4" />
                                    </label>
                                    <label class="flex items-center justify-between rounded-lg border p-3 cursor-pointer" :class="{ 'border-amber-600 bg-amber-50/40': targetSuhu === '200' }">
                                        <span class="text-sm font-medium">Suhu 200 °C</span>
                                        <input type="radio" value="200" v-model="targetSuhu" class="text-amber-600 focus:ring-amber-600 size-4" />
                                    </label>
                                </div>
                            </div>

                            <AlertDialogFooter>
                                <AlertDialogCancel @click="isCopyModalOpen = false">Batal</AlertDialogCancel>
                                <AlertDialogAction @click="handleBulkCopy" class="bg-amber-600 text-white hover:bg-amber-700 shadow-md">Proses Copy Data</AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>

                    <div v-if="selectedIds.length === 0" class="flex flex-wrap items-center gap-2 border rounded-lg p-1.5 bg-zinc-50/50 dark:bg-zinc-900/50 w-full md:w-auto">
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
                            <IconFileSpreadsheet v-else class="mr-1 size-3.5" /> Export Periode
                        </Button>
                    </div>

                    <Button as-child class="bg-primary hover:bg-primary/90 shadow-md">
                        <Link :href="route('thermalshock.create')">
                            <IconPlus class="mr-2 size-4" /> Tambah Data
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-x-auto">
                    <Table class="w-full">
                        <TableHeader>
                            <TableRow class="bg-muted/50 whitespace-nowrap">
                                <TableHead class="w-12 text-center">
                                    <input type="checkbox" :checked="isAllSelected" @change="toggleSelectAll" class="rounded border-zinc-300 text-primary focus:ring-primary size-4 cursor-pointer" />
                                </TableHead>
                                <TableHead class="text-center">Aksi</TableHead>
                                <TableHead>Tanggal Proses</TableHead>
                                <TableHead class="text-center">Suhu Testing</TableHead>
                                <TableHead class="text-center">Suhu Display</TableHead>
                                <TableHead class="text-center">Suhu Actual</TableHead>
                                <TableHead class="text-center">Jam Awal</TableHead>
                                <TableHead class="text-center">Capai Suhu</TableHead>
                                <TableHead class="text-center">Suhu Awal</TableHead>
                                <TableHead class="text-center">Suhu Air</TableHead>
                                <TableHead class="text-center">Mulai Tembak</TableHead>
                                <TableHead class="text-center">Selesai Tembak</TableHead>
                                <TableHead>Thermal Oven</TableHead>
                                <TableHead>Thermal Pintu</TableHead>
                                <TableHead>Operator</TableHead>
                                <TableHead class="text-center">Kode Bakar</TableHead>
                                <TableHead>Kode Tanah</TableHead>
                                <TableHead>Oven</TableHead>
                                <TableHead>Customer</TableHead>
                                <TableHead>Model</TableHead>
                                <TableHead>Size</TableHead>
                                <TableHead>Spesifikasi</TableHead>
                                <TableHead>Tinggi Former</TableHead>
                                <TableHead>Jam Keluar Oven</TableHead>
                                <TableHead>Sampel</TableHead>
                                <TableHead class="text-center">Berat Former</TableHead>
                                <TableHead>Tgl Keluar Oven</TableHead>
                                <TableHead>Tgl Produksi</TableHead>
                                <TableHead class="text-center">Posisi Former</TableHead>
                                <TableHead class="text-center">Hasil 180</TableHead>
                                <TableHead class="text-center">Hasil 200</TableHead>
                                <TableHead>Keterangan</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="thermalshocks.data.length === 0">
                                <TableCell colspan="32" class="h-24 text-center text-muted-foreground italic">Data tidak ditemukan.</TableCell>
                            </TableRow>

                            <TableRow v-for="item in thermalshocks.data" :key="item.id" class="hover:bg-muted/30 transition-colors whitespace-nowrap">
                                <TableCell class="text-center">
                                    <input type="checkbox" :value="item.id" v-model="selectedIds" class="rounded border-zinc-300 text-primary focus:ring-primary size-4 cursor-pointer" />
                                </TableCell>
                                <TableCell class="text-center sticky left-0 bg-background shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] z-10">
                                    <Button variant="ghost" size="icon" class="size-8 hover:text-primary" as-child title="Lihat Detail">
                                        <Link :href="route('thermalshock.edit', item.id)"><IconEye class="size-4" /></Link>
                                    </Button>
                                </TableCell>
                                <TableCell class="font-medium">
                                    {{ new Date(item.hari_tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) }}
                                </TableCell>
                                <TableCell class="text-center">{{ item.suhu_testing }}°C</TableCell>
                                <TableCell class="text-center">{{ item.suhu_display }}°C</TableCell>
                                <TableCell class="text-center">{{ item.suhu_actual }}°C</TableCell>
                                <TableCell class="text-center">{{ item.jam_awal_proses ? item.jam_awal_proses.substring(0, 5) : '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.jam_capai_suhu ? item.jam_capai_suhu.substring(0, 5) : '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.suhu_awal }}°C</TableCell>
                                <TableCell class="text-center">{{ item.suhu_air }}</TableCell>
                                <TableCell class="text-center">{{ item.jam_mulai_tembak ? item.jam_mulai_tembak.substring(0, 5) : '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.jam_selesai_tembak ? item.jam_selesai_tembak.substring(0, 5) : '-' }}</TableCell>
                                <TableCell><span class="font-semibold text-primary">{{ item.thermal_oven?.thermal_oven ?? '-' }}</span></TableCell>
                                <TableCell>{{ item.thermal_pintu?.thermal_pintu ?? '-' }}</TableCell>
                                <TableCell class="text-muted-foreground text-sm">{{ item.user?.name ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.kode_bakar }}</TableCell>
                                <TableCell>{{ item.kode_tanah }}</TableCell>
                                <TableCell>{{ item.oven?.oven ?? '-' }}</TableCell>
                                <TableCell>{{ item.customer?.customer ?? '-' }}</TableCell>
                                <TableCell>{{ item.customer?.model ?? '-' }}</TableCell>
                                <TableCell>{{ item.customer?.size ?? '-' }}</TableCell>
                                <TableCell>{{ item.customer?.spesifikasi ?? '-' }}</TableCell>

                                <TableCell class="text-center font-medium text-amber-600 dark:text-amber-500">
                                    {{ (item.tinggi_former || item.tinggiFormer) ? `${(item.tinggi_former || item.tinggiFormer).tinggi_former} mm` : '-' }}
                                </TableCell>
                                <TableCell class="text-center text-zinc-700 dark:text-zinc-300">
                                    {{ (item.jam_keluar_oven || item.jamKeluarOven) ? (item.jam_keluar_oven || item.jamKeluarOven).jam_keluar_oven.substring(0, 5) : '-' }}
                                </TableCell>

                                <TableCell>{{ item.sampel }}</TableCell>
                                <TableCell class="text-center">{{ item.berat_former }} g</TableCell>
                                <TableCell>
                                    {{ item.tanggal_keluar_oven ? new Date(item.tanggal_keluar_oven).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) : '-' }}
                                </TableCell>
                                <TableCell>
                                    {{ item.tgl_produksi ? new Date(item.tgl_produksi).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) : '-' }}
                                </TableCell>
                                <TableCell class="text-center">{{ item.posisi_former }}</TableCell>
                                <TableCell class="text-center">
                                    <span :class="{ 'text-emerald-600 font-bold': item.hasil_test_180 === 'OK', 'text-rose-600 font-bold': item.hasil_test_180 === 'NG' }">{{ item.hasil_test_180 }}</span>
                                </TableCell>
                                <TableCell class="text-center">
                                    <span :class="{ 'text-emerald-600 font-bold': item.hasil_test_200 === 'OK', 'text-rose-600 font-bold': item.hasil_test_200 === 'NG' }">{{ item.hasil_test_200 }}</span>
                                </TableCell>
                                <TableCell class="max-w-xs truncate" :title="item.keterangan">{{ item.keterangan }}</TableCell>
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
