<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { IconPlus, IconPencil, IconSearch, IconX, IconBox, IconArrowLeft, IconSend, IconFileSpreadsheet } from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: { id: number; hari_tgl: string };
    produk: {
        data: Array<{
            id: number;
            kode_tanah: string;
            sampel: string;
            hasil_test: 'OK' | 'NG';
            suhu_actual: number;
            oven: { oven: string } | null;
            customer: { customer: string } | null;
            model_size: { modelsize: string } | null;
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
            route("produk.index", props.thermalshock.id),
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
    <Head title="Produk Thermal Shock" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('thermalshock.show', props.thermalshock.id)">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Item Produk Teruji</h2>
                <p class="text-sm text-muted-foreground">Log Thermal Shock Tanggal: {{ new Date(props.thermalshock.hari_tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) }}</p>
            </div>
        </div>

        <Card class="border-none shadow-sm mt-2">
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconBox class="size-6 text-primary" />
                    Daftar Produk
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari Kode Tanah / Customer..." class="pl-10 pr-10" />
                        <button v-if="search" @click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <!-- TOMBOL MASSAL: MASUKKAN SEMUA KE HASIL THERMAL SHOCK -->
                    <Button
                        v-if="props.produk.data.length > 0"
                        @click="router.post(route('produk.kirimkehasilthermalshock', props.thermalshock.id))"
                        class="bg-emerald-600 hover:bg-emerald-500 text-white font-semibold shadow-md"
                    >
                        <IconSend class="mr-2 size-4" /> Kirim Semua ke Rekap
                    </Button>

                    <Button
                        v-if="props.produk.data.length > 0"
                        variant="outline"
                        class="border-emerald-600 text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 font-semibold shadow-sm"
                        as-child
                    >
                        <a :href="route('produk.exportExcel', props.thermalshock.id)">
                            <IconFileSpreadsheet class="mr-2 size-4" /> Export Excel
                        </a>
                    </Button>

                    <Button as-child class="bg-primary hover:bg-primary/90 shadow-md">
                        <Link :href="route('produk.create', props.thermalshock.id)">
                            <IconPlus class="mr-2 size-4" /> Tambah Produk
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead>Tgl Keluar Oven</TableHead>
                                <TableHead>Model Size</TableHead>
                                <TableHead>Oven</TableHead>
                                <TableHead>Kode Tanah</TableHead>
                                <TableHead>Kode Bakar</TableHead>
                                <TableHead>Spesifikasi</TableHead>
                                <TableHead>Sampel</TableHead>
                                <TableHead>Tgl Produksi</TableHead>
                                <TableHead>Posisi Former</TableHead>
                                <TableHead class="text-center">Suhu Aktual</TableHead>
                                <TableHead class="text-center">Hasil Test</TableHead>
                                <TableHead class="text-center">Keterangan</TableHead>
                                <TableHead class="text-right">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="produk.data.length === 0">
                                <TableCell colspan="7" class="h-24 text-center text-muted-foreground italic">
                                    Belum ada item produk terdaftar pada sesi pengujian ini.
                                </TableCell>
                            </TableRow>

                            <TableRow v-for="item in produk.data" :key="item.id" class="hover:bg-muted/30">
                                <TableCell>
                                    {{ item.tanggal_keluar_oven ? new Date(item.tanggal_keluar_oven).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) : '-' }}
                                </TableCell>
                                <TableCell class="text-left max-w-[180px] truncate">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ item.customer?.customer ?? 'Manual Input' }}</div>
                                    <div class="text-[10px] text-muted-foreground">{{ item.model_size?.modelsize ?? '-' }}</div>
                                </TableCell>
                                <TableCell class="text-center whitespace-nowrap">
                                    {{ item.oven?.oven ?? '-' }} <span class="text-[10px] text-muted-foreground block">{{ item.jam_keluar_oven?.jam_keluar_oven ?? '' }}</span>
                                </TableCell>
                                <TableCell class="font-medium text-primary">{{ item.kode_tanah }}</TableCell>
                                <TableCell>{{ item.kode_bakar ?? '-' }}</TableCell>
                                <TableCell>{{ item.spesifikasi?.spesifikasi ?? '-' }}</TableCell>
                                <TableCell>{{ item.sampel ?? '-' }}</TableCell>
                                <TableCell>{{ item.tgl_produksi ?? '-' }}</TableCell>
                                <TableCell>{{ item.posisi_former ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.suhu_actual }}°C</TableCell>
                                <TableCell class="text-center">
                                    <span :class="item.hasil_test === 'OK' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400'" class="px-2.5 py-0.5 rounded text-xs font-bold">
                                        {{ item.hasil_test }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-center">{{ item.keterangan }}</TableCell>
                                <TableCell class="text-right">
                                    <Button variant="ghost" size="icon" class="size-8 hover:text-primary" as-child>
                                        <Link :href="route('produk.edit', [props.thermalshock.id, item.id])">
                                            <IconPencil class="size-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Pagination -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground italic font-medium">
                        Menampilkan {{ produk.from ?? 0 }} - {{ produk.to ?? 0 }} dari {{ produk.total }} data
                    </p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in produk.links" :key="k">
                            <Button v-if="link.url === null" variant="outline" size="sm" disabled class="opacity-50 text-xs px-3 h-8" v-html="cleanLabel(link.label)" />
                            <Button v-else as-child variant="outline" size="sm" class="text-xs px-3 h-8" :class="{ 'bg-primary text-primary-foreground hover:bg-primary/90': link.active }">
                                <Link :href="link.url" v-html="cleanLabel(link.label)" />
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
