<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { IconPlus, IconEye, IconSearch, IconX, IconFlame, IconHammer, IconFileSpreadsheet } from "@tabler/icons-vue";
import { ref, watch } from "vue";

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

            // Kolom pindahan dari produk yang sekarang tampil semua
            kode_bakar: number;
            kode_tanah: string;
            oven: { oven: string } | null; // Pastikan relasi ini di-load jika ingin nama oven, atau gunakan ID
            customer: { name: string } | null;
            model_size: { name: string } | null;
            spesifikasi: { name: string } | null;
            tinggi_former: { name: string } | null;
            jam_keluar_oven: { name: string } | null;
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
                        variant="outline"
                        class="border-emerald-600 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 font-semibold shadow-sm"
                        as-child
                    >
                        <a href="#">
                            <IconFileSpreadsheet class="mr-2 size-4" /> Export All Rekap
                        </a>
                    </Button>

                    <Button as-child class="bg-primary hover:bg-primary/90 shadow-md transition-all active:scale-95">
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
                                <TableHead>Model Size</TableHead>
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
                                <TableCell colspan="30" class="h-24 text-center text-muted-foreground italic">
                                    Data tidak ditemukan.
                                </TableCell>
                            </TableRow>

                            <TableRow v-for="item in thermalshocks.data" :key="item.id" class="hover:bg-muted/30 transition-colors whitespace-nowrap">
                                <TableCell class="text-center sticky left-0 bg-background shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] z-10">
                                    <Button variant="ghost" size="icon" class="size-8 hover:text-primary transition-colors" as-child title="Lihat Detail">
                                        <Link :href="route('thermalshock.edit', item.id)">
                                            <IconEye class="size-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                                <TableCell class="font-medium">
                                    {{ new Date(item.hari_tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) }}
                                </TableCell>
                                <TableCell class="text-center">{{ item.suhu_testing }}°C</TableCell>
                                <TableCell class="text-center">{{ item.suhu_display }}°C</TableCell>
                                <TableCell class="text-center">{{ item.suhu_actual }}°C</TableCell>
                                <TableCell class="text-center">{{ item.jam_awal_proses.substring(0, 5) }}</TableCell>
                                <TableCell class="text-center">{{ item.jam_capai_suhu.substring(0, 5) }}</TableCell>
                                <TableCell class="text-center">{{ item.suhu_awal }}°C</TableCell>
                                <TableCell class="text-center">{{ item.suhu_air }}</TableCell>
                                <TableCell class="text-center">{{ item.jam_mulai_tembak?.substring(0, 5) }}</TableCell>
                                <TableCell class="text-center">{{ item.jam_selesai_tembak ? item.jam_selesai_tembak.substring(0, 5) : '-' }}</TableCell>
                                <TableCell><span class="font-semibold text-primary">{{ item.thermal_oven?.thermal_oven ?? '-' }}</span></TableCell>
                                <TableCell>{{ item.thermal_pintu?.thermal_pintu ?? '-' }}</TableCell>
                                <TableCell class="text-muted-foreground text-sm">{{ item.user?.name ?? '-' }}</TableCell>

                                <TableCell class="text-center">{{ item.kode_bakar }}</TableCell>
                                <TableCell>{{ item.kode_tanah }}</TableCell>
                                <TableCell>{{ item.oven?.oven ?? item.oven_id }}</TableCell>
                                <TableCell>{{ item.customer?.name ?? item.customer_id }}</TableCell>
                                <TableCell>{{ item.model_size?.name ?? item.modelsize_id }}</TableCell>
                                <TableCell>{{ item.spesifikasi?.name ?? item.spesifikasi_id }}</TableCell>
                                <TableCell>{{ item.tinggi_former?.name ?? item.tinggi_former_id }}</TableCell>
                                <TableCell>{{ item.jam_keluar_oven?.name ?? item.jam_keluar_oven_id }}</TableCell>
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
                                    <span :class="{ 'text-emerald-600 font-bold': item.hasil_test_180 === 'OK', 'text-rose-600 font-bold': item.hasil_test_180 === 'NG' }">
                                        {{ item.hasil_test_180 }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-center">
                                    <span :class="{ 'text-emerald-600 font-bold': item.hasil_test_200 === 'OK', 'text-rose-600 font-bold': item.hasil_test_200 === 'NG' }">
                                        {{ item.hasil_test_200 }}
                                    </span>
                                </TableCell>
                                <TableCell class="max-w-xs truncate" :title="item.keterangan">{{ item.keterangan }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground italic font-medium">
                        Menampilkan {{ thermalshocks.from ?? 0 }} - {{ thermalshocks.to ?? 0 }} dari {{ thermalshocks.total }} data
                    </p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in thermalshocks.links" :key="k">
                            <Button v-if="link.url === null" variant="outline" size="sm" disabled class="opacity-50 text-xs px-3 h-8" v-html="cleanLabel(link.label)" />
                            <Button v-else as-child variant="outline" size="sm" class="text-xs px-3 h-8 transition-all" :class="{ 'bg-primary text-primary-foreground hover:bg-primary/90': link.active }">
                                <Link :href="link.url" v-html="cleanLabel(link.label)" />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
