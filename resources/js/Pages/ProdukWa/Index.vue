<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { IconPlus, IconPencil, IconSearch, IconX, IconDroplet, IconArrowLeft, IconFlask } from "@tabler/icons-vue";
import { ref, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    waterabsorption: { id: number; hari_tgl: string };
    produkwa: {
        data: Array<{
            id: number;
            tgl_produksi: string;
            sampel: string;
            temp: number;
            palm_wo: string; palm_wa: string; palm_water: string;
            mc_wo: string; mc_wa: string; mc_water: string;
            sl_wo: string; sl_wa: string; sl_water: string;
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
watch(search, (value) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => {
        router.get(route("produkwa.index", props.waterabsorption.id), { search: value }, { preserveState: true, replace: true });
    }, 500);
});

const cleanLabel = (label: string) => {
    if (label.includes("Previous")) return "Sebelumnya";
    if (label.includes("Next")) return "Selanjutnya";
    return label;
};
</script>

<template>
    <Head title="Produk Water Absorption" />
    <div class="flex flex-col gap-4 p-4 md:p-8 pt-4">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('waterabsorption.index')"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Item Pengujian Water Absorption</h2>
                <p class="text-sm text-muted-foreground">Sesi Log: {{ new Date(props.waterabsorption.hari_tgl).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) }}</p>
            </div>
        </div>

        <Card class="border-none shadow-sm mt-2">
            <CardHeader class="flex flex-col md:flex-row items-start md:items-center justify-between space-y-4 md:space-y-0 pb-6">
                <CardTitle class="text-xl font-bold flex items-center gap-2">
                    <IconDroplet class="size-6 text-blue-500" /> Daftar Sampel Uji
                </CardTitle>
                <div class="flex items-center gap-2 w-full md:w-auto">
                    <div class="relative w-full md:w-72">
                        <IconSearch class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-muted-foreground" />
                        <Input v-model="search" placeholder="Cari Sampel / Customer..." class="pl-10 pr-10" />
                        <button v-if="search" @click="search = ''" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground"><IconX class="size-4" /></button>
                    </div>
                    <Button as-child class="bg-blue-600 hover:bg-blue-500 text-white shadow-md">
                        <Link :href="route('produkwa.create', props.waterabsorption.id)">
                            <IconPlus class="mr-2 size-4" /> Tambah Item
                        </Link>
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <div class="rounded-lg border overflow-hidden">
                    <Table>
                        <TableHeader>
                            <TableHead class="text-center">Tanggal Prod</TableHead>
                            <TableHead class="text-center">Customer</TableHead>
                            <TableHead class="text-center">Model</TableHead>
                            <TableHead class="text-center">Spesifikasi</TableHead>
                            <TableHead class="text-center">Sampel</TableHead>
                            <TableHead class="text-center">Temp</TableHead>
                            <TableHead class="text-center">Palm Wo</TableHead>
                            <TableHead class="text-center">Palm Wa</TableHead>
                            <TableHead class="text-center">Palm Water</TableHead>
                            <TableHead class="text-center">Mc Wo</TableHead>
                            <TableHead class="text-center">Mc Wa</TableHead>
                            <TableHead class="text-center">Mc Water</TableHead>
                            <TableHead class="text-center">Sl Wo</TableHead>
                            <TableHead class="text-center">Sl Wa</TableHead>
                            <TableHead class="text-center">Sl Water</TableHead>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-if="produkwa.data.length === 0">
                                <TableCell colspan="15" class="h-24 text-center text-muted-foreground italic">Belum ada item produk terdaftar.</TableCell>
                            </TableRow>
                            <TableRow v-for="item in produkwa.data" :key="item.id" class="hover:bg-muted/30 text-xs">
                                <TableCell class="text-center">{{ item.tgl_produksi ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.customer?.customer ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.model_size?.modelsize ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.spesifikasi?.spesifikasi ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.sample ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.temp ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.palm_wo ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.palm_wa ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.palm_water ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.mc_wo ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.mc_wa ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.mc_water ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.sl_wo ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.sl_wa ?? '-' }}</TableCell>
                                <TableCell class="text-center">{{ item.sl_water ?? '-' }}</TableCell>
                                <TableCell class="text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <Button variant="ghost" size="icon" class="size-7 hover:text-blue-600" as-child>
                                            <Link :href="route('produkwa.edit', [props.waterabsorption.id, item.id])"><IconPencil class="size-4" /></Link>
                                        </Button>
                                    </div>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
                <!-- Pagination -->
                <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-6">
                    <p class="text-xs text-muted-foreground italic">Menampilkan {{ produkwa.from ?? 0 }} - {{ produkwa.to ?? 0 }} dari {{ produkwa.total }} data</p>
                    <nav class="flex items-center gap-1">
                        <template v-for="(link, k) in produkwa.links" :key="k">
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
