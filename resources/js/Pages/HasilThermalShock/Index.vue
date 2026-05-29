<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { IconPlus, IconPencil, IconSearch, IconX, IconFlame } from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    hasil: {
        data: Array<{
            id: number;
            tanggal_keluar_oven: string | null;
            kode_tanah: string;
            suhu_180: string;
            suhu_200: string;
            suhu: number;
            berat_former: number;
            thickness: string;
            chemical: string;
            wa_palm: string;
            wa_mc: string;
            wa_sli: string;
            density: string;
            luas_area: string;
            visual: number;
            oven: { oven: string } | null;
            jam_keluar_oven: { jam_keluar_oven: string } | null;
            customer: { customer: string } | null;
            model_size: { modelsize: string } | null;
            spesifikasi: { spesifikasi: string } | null;
        }>;
        links: any[]; from: number; to: number; total: number;
    };
    filters: { search: string };
}>();

const search = ref(props.filters.search || "");
let timeout: any;

// Fitur Pencarian Real-time Otomatis
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route("hasilthermalshock.index"), { search: value }, { preserveState: true, replace: true });
    }, 500);
});

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};

// Fungsi pembantu warna badge status hasil uji
const getStatusClass = (status: string) => {
    if (status === "OK") return "bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 font-bold";
    if (status === "NG") return "bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-400 font-bold";
    return "bg-zinc-100 text-zinc-800 dark:bg-zinc-800 dark:text-zinc-400";
};
</script>

<template>
    <Head title="Rekap Hasil Thermal Shock" />
    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <div>
            <h2 class="text-3xl font-bold tracking-tight">Rekapitulasi Hasil Thermal Shock</h2>
            <p class="text-sm text-muted-foreground">Muara data pengujian ketahanan fisik dan temperatur produk cetakan.</p>
        </div>

        <Card class="border-none shadow-sm mt-2">
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconFlame class="size-6 text-red-500" /> Riwayat Log Hasil Pengujian
                </CardTitle>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari Kode Tanah / Customer..." class="pl-10 pr-10" />
                        <button v-if="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"><IconX class="size-4" /></button>
                    </div>
                    <Button as-child class="bg-blue-600 hover:bg-blue-500 shadow-md text-white font-semibold">
                        <Link :href="route('hasilthermalshock.create')">
                            <IconPlus class="mr-2 size-4" /> Tambah Rekap
                        </Link>
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50 text-xs">
                                <TableHead class="text-center">Tgl Keluar</TableHead>
                                <TableHead class="text-center">Model Size</TableHead>
                                <TableHead class="text-center">Oven</TableHead>
                                <TableHead class="text-center">Kode Tanah</TableHead>
                                <TableHead class="text-center">180°C</TableHead>
                                <TableHead class="text-center">200°C</TableHead>
                                <TableHead class="text-center">Suhu</TableHead>
                                <TableHead class="text-center">Berat</TableHead>
                                <TableHead class="text-center">Ketebalan</TableHead>
                                <TableHead class="text-center">Density</TableHead>
                                <TableHead class="text-center">Wtr Palm</TableHead>
                                <TableHead class="text-center">Wtr MC</TableHead>
                                <TableHead class="text-center">Wtr Sli</TableHead>
                                <TableHead class="text-center">Hasil Akhir</TableHead>
                                <TableHead class="text-center">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="props.hasil.data.length === 0">
                                <TableCell colspan="14" class="h-24 text-center text-muted-foreground italic">Belum ada data rekapitulasi terdaftar.</TableCell>
                            </TableRow>
                            <TableRow v-for="item in props.hasil.data" :key="item.id" class="hover:bg-muted/30 text-xs">
                                <TableCell class="text-center whitespace-nowrap font-medium">
                                    {{ item.tanggal_keluar_oven ? new Date(item.tanggal_keluar_oven).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) : '-' }}
                                </TableCell>
                                <TableCell class="text-left max-w-[180px] truncate">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ item.customer?.customer ?? 'Manual Input' }}</div>
                                    <div class="text-[10px] text-muted-foreground">{{ item.model_size?.modelsize ?? '-' }}</div>
                                </TableCell>
                                <TableCell class="text-center whitespace-nowrap">
                                    {{ item.oven?.oven ?? '-' }} <span class="text-[10px] text-muted-foreground block">{{ item.jam_keluar_oven?.jam_keluar_oven ?? '' }}</span>
                                </TableCell>
                                <TableCell class="text-center font-mono font-medium text-blue-600">{{ item.kode_tanah }}</TableCell>
                                <TableCell class="text-center">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px]" :class="getStatusClass(item.suhu_180)">{{ item.suhu_180 }}</span>
                                </TableCell>
                                <TableCell class="text-center">
                                    <span class="inline-flex items-center rounded-md px-2 py-0.5 text-[10px]" :class="getStatusClass(item.suhu_200)">{{ item.suhu_200 }}</span>
                                </TableCell>
                                <TableCell class="text-center">{{ item.suhu }}°C</TableCell>
                                <TableCell class="text-center font-mono">{{ item.berat_former }}g</TableCell>
                                <TableCell class="text-center font-mono">{{ parseFloat(item.ketebalan).toFixed(2) }}</TableCell>
                                <TableCell class="text-center font-mono font-medium text-purple-600">{{ parseFloat(item.density).toFixed(2) }}</TableCell>
                                <TableCell class="text-center font-mono text-emerald-600">{{ parseFloat(item.wa_palm).toFixed(3) }}%</TableCell>
                                <TableCell class="text-center font-mono text-emerald-600">{{ parseFloat(item.wa_mc).toFixed(3) }}%</TableCell>
                                <TableCell class="text-center font-mono text-emerald-600">{{ parseFloat(item.wa_sli).toFixed(3) }}%</TableCell>
                                <TableCell class="text-center font-mono font-medium text-purple-600">{{ parseFloat(item.hasil_akhir).toFixed(2) }}</TableCell>
                                <TableCell class="text-center whitespace-nowrap">
                                    <Button variant="ghost" size="icon" class="size-7 hover:text-blue-600 rounded-full" as-child>
                                        <Link :href="route('hasilthermalshock.show.edit', item.id)"><IconPencil class="size-4" /></Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Pagination Footer -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground italic">Menampilkan {{ props.hasil.from ?? 0 }} - {{ props.hasil.to ?? 0 }} dari {{ props.hasil.total }} rekap data</p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in props.hasil.links" :key="k">
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
