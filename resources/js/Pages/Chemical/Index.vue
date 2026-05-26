<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { IconPlus, IconEye, IconSearch, IconX, IconFlask } from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    chemicals: {
        data: Array<{
            id: number;
            tgl_test: string;
            kode_alkali: string;
            alkali_jam_mulai: string;
            alkali_jam_selesai: string;
            kode_acid: string;
            acid_jam_mulai: string;
            acid_jam_selesai: string;
            produk_chemical: Array<{ id: number }>;
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
            route("chemical.index"),
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
    <Head title="Chemical Test Log" />

    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <Card class="border-none shadow-sm">
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconFlask class="size-6 text-primary" />
                    Daftar Pengujian Chemical
                </CardTitle>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari tanggal / kode..." class="pl-10 pr-10" />
                        <button v-if="search" @click="clearSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground">
                            <IconX class="size-4" />
                        </button>
                    </div>

                    <Button as-child class="bg-primary hover:bg-primary/90 shadow-md transition-all active:scale-95">
                        <Link :href="route('chemical.create')">
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
                                <TableHead class="w-12">ID</TableHead>
                                <TableHead>Tanggal Test</TableHead>
                                <TableHead>Kode Alkali</TableHead>
                                <TableHead>Waktu Alkali</TableHead>
                                <TableHead>Kode Acid</TableHead>
                                <TableHead>Waktu Acid</TableHead>
                                <TableHead class="text-center">Aksi</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="chemicals.data.length === 0">
                                <TableCell colspan="7" class="h-24 text-center text-muted-foreground italic">
                                    Data tidak ditemukan.
                                </TableCell>
                            </TableRow>

                            <TableRow v-for="item in chemicals.data" :key="item.id" class="hover:bg-muted/30 transition-colors">
                                <TableCell class="font-medium">{{ item.id }}</TableCell>
                                <TableCell class="font-medium whitespace-nowrap">
                                    {{ new Date(item.tgl_test).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) }}
                                </TableCell>
                                <TableCell>
                                    <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-950/40 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-400 border border-blue-200/60">
                                        {{ item.kode_alkali }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-sm text-muted-foreground whitespace-nowrap">
                                    {{ item.alkali_jam_mulai }} - {{ item.alkali_jam_selesai }}
                                </TableCell>
                                <TableCell>
                                    <span class="inline-flex items-center rounded-md bg-purple-50 dark:bg-purple-950/40 px-2 py-1 text-xs font-medium text-purple-700 dark:text-purple-400 border border-purple-200/60">
                                        {{ item.kode_acid }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-sm text-muted-foreground whitespace-nowrap">
                                    {{ item.acid_jam_mulai }} - {{ item.acid_jam_selesai }}
                                </TableCell>
                                <TableCell class="text-center whitespace-nowrap">
                                    <Button variant="ghost" size="icon" class="size-8 hover:text-primary transition-colors" as-child title="Lihat Detail">
                                        <Link :href="route('chemical.show', item.id)">
                                            <IconEye class="size-4" />
                                        </Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground italic font-medium">
                        Menampilkan {{ chemicals.from ?? 0 }} - {{ chemicals.to ?? 0 }} dari {{ chemicals.total }} data
                    </p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in chemicals.links" :key="k">
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
