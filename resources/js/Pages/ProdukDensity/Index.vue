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
    density: { id: number; hari_tgl: string };
    produkdensity: {
        data: Array<{
            id: number;
            no: number;
            tgl_produksi: string;
            ketebalan: string;
            berat_awal: string;
            berat_akhir: string;
            volume: string;
            density: string;
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
        router.get(route("produkdensity.index", props.density.id), { search: value }, { preserveState: true, replace: true });
    }, 500);
});

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Produk Density"/>
    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <!-- Sesuaikan link ini ke route list master density kamu -->
                <Link :href="route('density.index')"><IconArrowLeft class="size-4"/></Link>
            </Button>
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Item Pengujian Density</h2>
                <p class="text-sm text-muted-foreground" v-if="props.density.hari_tgl">
                    Sesi Log: {{ new Date(props.density.hari_tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) }}
                </p>
            </div>
        </div>

        <Card class="border-none shadow-sm mt-2">
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconFlask class="size-6 text-blue-500"/> Daftar Sampel Density
                </CardTitle>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground"/>
                        <Input v-model="search" placeholder="Cari Customer..." class="pl-10 pr-10"/>
                        <button v-if="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"><IconX class="size-4"/></button>
                    </div>
                    <Button as-child class="bg-primary hover:bg-primary/90 shadow-md">
                        <Link :href="route('produkdensity.create', props.density.id)">
                            <IconPlus class="mr-2 size-4"/> Tambah Item
                        </Link>
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableHead class="text-center">No</TableHead>
                            <TableHead class="text-center">Tanggal Prod</TableHead>
                            <TableHead class="text-center">Customer</TableHead>
                            <TableHead class="text-center">Model Size</TableHead>
                            <TableHead class="text-center">Oven</TableHead>
                            <TableHead class="text-center">Jam Keluar</TableHead>
                            <TableHead class="text-center">Tebal (mm)</TableHead>
                            <TableHead class="text-center">Berat Awal (gr)</TableHead>
                            <TableHead class="text-center">Berat Akhir (gr)</TableHead>
                            <TableHead class="text-center">Volume (ml)</TableHead>
                            <TableHead class="text-center bg-blue-50 dark:bg-blue-950/20">Density (p=m/V)</TableHead>
                            <TableHead class="text-right"></TableHead>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="produkdensity.data.length === 0">
                                <TableCell colspan="12" class="h-24 text-center text-muted-foreground italic">Belum ada data sampel density terdaftar.</TableCell>
                            </TableRow>
                            <TableRow v-for="item in produkdensity.data" :key="item.id" class="hover:bg-muted/30 text-xs">
                                <TableCell class="text-center font-medium">{{ item.no ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.tgl_produksi ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.customer?.customer ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.model_size?.modelsize ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.oven?.oven ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.jam_keluar_oven?.jam_keluar_oven ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.ketebalan ?? '0.00' }}</TableCell>
                                <TableCell class="text-center">{{ item.berat_awal ?? '0.00' }}</TableCell>
                                <TableCell class="text-center">{{ item.berat_akhir ?? '0.00' }}</TableCell>
                                <TableCell class="text-center">{{ item.volume ?? '0.00' }}</TableCell>
                                <TableCell class="text-center font-bold bg-blue-50/50 dark:bg-blue-950/10 text-blue-600 dark:text-blue-400">
                                    {{ item.density ?? '0.00' }}
                                </TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon" class="size-7 hover:text-blue-600" as-child>
                                            <Link :href="route('produkdensity.edit', [props.density.id, item.id])"><IconPencil class="size-4"/></Link>
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <!-- Pagination Area -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground italic">Menampilkan {{ produkdensity.from ?? 0 }} - {{ produkdensity.to ?? 0 }} dari {{ produkdensity.total }} data</p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in produkdensity.links" :key="k">
                            <Button v-if="link.url === null" variant="outline" size="sm" disabled class="opacity-50 text-xs px-3 h-8" v-html="cleanLabel(link.label)"/>
                            <Button v-else as-child variant="outline" size="sm" class="text-xs px-3 h-8" :class="{ 'bg-primary text-primary-foreground': link.active }">
                                <Link :href="link.url" v-html="cleanLabel(link.label)"/>
                            </Button>
                        </template>
                    </nav>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
