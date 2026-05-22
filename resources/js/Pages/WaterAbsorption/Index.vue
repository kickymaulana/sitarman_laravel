<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { IconPlus, IconEye, IconSearch, IconX, IconDroplet } from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

// Penyesuaian tipe data TypeScript sesuai skema tabel baru
const props = defineProps<{
    waterabsorptions: {
        data: Array<{
            id: number;
            tgl: string | null; // Kolom baru menggantikan tgl_test
            spec: string;
            mulai_proses: string;
            selesai_proses: string;
            temp_air: number;
            produk_dwa: Array<{ id: number }>; // Relasi baru
            density_user: { name: string } | null;
            water_absoription_user: { name: string } | null; // Relasi operator WA
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
            route("waterabsorption.index"),
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
    <Head title="Water Absorption" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconDroplet class="size-6 text-blue-500" />
                    Daftar Water Absorption
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari tanggal atau spec..." class="pl-10 pr-10" />
                        <button v-if="search" @click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <!-- Jika pengisian create dilakukan satu pintu di modul density, link ini bisa diarahkan ke density.create atau biarkan jika ditangani terpisah -->
                    <Button as-child class="bg-primary hover:bg-primary/90 shadow-md transition-all active:scale-95">
                        <Link :href="route('waterabsorption.create')">
                            <IconPlus class="mr-2 size-4" /> Tambah Data
                        </Link>
                    </Button>
                </div>
            </CardHeader>

            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead>ID</TableHead>
                                <TableHead>Tanggal Test</TableHead>
                                <TableHead>Spesifikasi</TableHead>
                                <TableHead class="text-center">Mulai Proses</TableHead>
                                <TableHead class="text-center">Selesai Proses</TableHead>
                                <TableHead class="text-center">Temp Air</TableHead>
                                <TableHead>Operator WA</TableHead>
                                <TableHead class="text-center">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="waterabsorptions.data.length === 0">
                                <TableCell colspan="8" class="h-24 text-center text-muted-foreground italic">
                                    Data tidak ditemukan.
                                </TableCell>
                            </TableRow>

                            <TableRow v-for="item in waterabsorptions.data" :key="item.id" class="hover:bg-muted/30 transition-colors">
                                <TableCell class="font-mono text-xs">#{{ item.id }}</TableCell>
                                <TableCell class="font-medium whitespace-nowrap">
                                    {{ item.tgl ? new Date(item.tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) : '-' }}
                                </TableCell>
                                <TableCell>
                                    <span class="inline-flex items-center rounded-md bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-800 dark:bg-zinc-800 dark:text-zinc-200">
                                        {{ item.spec }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-center">{{ item.mulai_proses ? item.mulai_proses.substring(0, 5) : '00:00' }}</TableCell>
                                <TableCell class="text-center">{{ item.selesai_proses ? item.selesai_proses.substring(0, 5) : '00:00' }}</TableCell>
                                <TableCell class="text-center font-medium">{{ item.temp_air }}°C</TableCell>
                                <TableCell class="whitespace-nowrap text-muted-foreground text-sm">
                                    {{ item.water_absoription_user?.name ?? '-' }}
                                </TableCell>
                                <TableCell class="text-center whitespace-nowrap">
                                    <Button variant="ghost" size="icon" class="size-8 hover:text-primary transition-colors" as-child title="Lihat Detail">
                                        <Link :href="route('waterabsorption.show', item.id)">
                                            <IconEye class="size-4" />
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
                        Menampilkan {{ waterabsorptions.from ?? 0 }} - {{ waterabsorptions.to ?? 0 }} dari {{ waterabsorptions.total }} data
                    </p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in waterabsorptions.links" :key="k">
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
