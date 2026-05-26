<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { IconPlus, IconPencil, IconSearch, IconX, IconArrowLeft, IconFlask } from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    chemical: { id: number; tgl_test: string };
    produkchemical: {
        data: Array<{
            id: number;
            tgl_production: string;
            tanggal_keluar_oven: string;
            sample: string;
            ketebalan: string;
            berat_awal: string;
            berat_akhir: string;
            customer: { customer: string } | null;
            model_size: { modelsize: string } | null;
            oven: { oven: string } | null;
            jam_keluar_oven: { jam_keluar_oven: string } | null;
        }>;
        links: any[]; from: number; to: number; total: number;
    };
    filters: { search: string };
}>();

const search = ref(props.filters.search || "");
let timeout: any;
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route("produkchemical.index", props.chemical.id), { search: value }, { preserveState: true, replace: true });
    }, 500);
});

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Item Produk Chemical" />
    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('chemical.index')"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Item Pengujian Chemical</h2>
                <p class="text-sm text-muted-foreground">
                    Sesi Log Master: {{ new Date(props.chemical.tgl_test).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) }}
                </p>
            </div>
        </div>

        <Card class="border-none shadow-sm mt-2">
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconFlask class="size-6 text-purple-500" /> Daftar Sampel Produk Chemical
                </CardTitle>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari Customer / Sampel..." class="pl-10 pr-10" />
                        <button v-if="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"><IconX class="size-4" /></button>
                    </div>
                    <Button as-child class="bg-primary hover:bg-primary/90 shadow-md">
                        <Link :href="route('produkchemical.create', props.chemical.id)">
                            <IconPlus class="mr-2 size-4" /> Tambah Item
                        </Link>
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableRow class="bg-muted/50 text-xs">
                                <TableHead class="text-center">ID</TableHead>
                                <TableHead>Customer & Model</TableHead>
                                <TableHead class="text-center">Tgl Produksi</TableHead>
                                <TableHead class="text-center">Oven Keluar</TableHead>
                                <TableHead class="text-center">Sampel</TableHead>
                                <TableHead class="text-center">Tebal (mm)</TableHead>
                                <TableHead class="text-center">B. Awal (gr)</TableHead>
                                <TableHead class="text-center">B. Akhir (gr)</TableHead>
                                <TableHead class="text-right w-10"></TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="produkchemical.data.length === 0">
                                <TableCell colspan="9" class="h-24 text-center text-muted-foreground italic">Belum ada data sampel terdaftar.</TableCell>
                            </TableRow>
                            <TableRow v-for="item in produkchemical.data" :key="item.id" class="hover:bg-muted/30 text-xs">
                                <TableCell class="text-center font-medium">{{ item.id }}</TableCell>
                                <TableCell class="text-left max-w-[200px] truncate">
                                    <div class="font-semibold text-zinc-900 dark:text-zinc-100">{{ item.customer?.customer ?? '-' }}</div>
                                    <div class="text-[10px] text-muted-foreground">{{ item.model_size?.modelsize ?? '-' }}</div>
                                </TableCell>
                                <TableCell class="text-center whitespace-nowrap">
                                    {{ new Date(item.tgl_production).toLocaleDateString("id-ID", { day: "2-digit", month: "short", year: "numeric" }) }}
                                </TableCell>
                                <TableCell class="text-center whitespace-nowrap">
                                    {{ item.oven?.oven ?? '-' }}
                                    <span class="text-[10px] text-muted-foreground block">
                                        {{ item.tanggal_keluar_oven }} / {{ item.jam_keluar_oven?.jam_keluar_oven.substring(0,5) ?? '' }}
                                    </span>
                                </TableCell>
                                <TableCell class="text-center font-mono font-medium">{{ item.sample }}</TableCell>
                                <TableCell class="text-center font-mono">{{ item.ketebalan }}</TableCell>
                                <TableCell class="text-center font-mono">{{ item.berat_awal }}</TableCell>
                                <TableCell class="text-center font-mono">{{ item.berat_akhir }}</TableCell>
                                <TableCell class="text-right">
                                    <Button variant="ghost" size="icon" class="size-7 hover:text-purple-600" as-child>
                                        <Link :href="route('produkchemical.edit', [props.chemical.id, item.id])"><IconPencil class="size-4" /></Link>
                                    </Button>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground italic">Menampilkan {{ produkchemical.from ?? 0 }} - {{ produkchemical.to ?? 0 }} dari {{ produkchemical.total }} data</p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in produkchemical.links" :key="k">
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
