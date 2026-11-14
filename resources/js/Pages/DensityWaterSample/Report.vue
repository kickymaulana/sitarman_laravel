<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Head, Link, router } from "@inertiajs/vue3";
import { IconArrowLeft, IconDownload, IconFileSpreadsheet } from "@tabler/icons-vue";
import { ref } from "vue";

defineOptions({ layout: AuthenticatedLayout });

type User = { name: string; username: string };
type Sample = Record<string, string | number | null | User> & {
    id: number;
    tgl_test: string;
    urutan: number;
    kode_sample: string;
    no_open: string | null;
    status: string;
    creator: User;
    completer: User | null;
};

const props = defineProps<{
    samples: Sample[];
    filters: { tgl_test?: string };
}>();

const tglTest = ref(props.filters.tgl_test || "");

const search = () => {
    router.get(route("density-water-sample.report"), {
        tgl_test: tglTest.value || undefined,
    }, { preserveState: true, replace: true });
};

const exportCsv = () => {
    if (!tglTest.value) return;
    window.location.href = route("density-water-sample.export", { tgl_test: tglTest.value });
};

const display = (value: unknown) => value === null || value === "" ? "-" : String(value);
const userName = (user: User | null) => user?.name || "-";
</script>

<template>
    <Head title="Laporan Density Water Sample" />
    <div class="flex flex-col gap-4 p-4 pt-4 md:p-8">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('density-water-sample.index')"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <h2 class="text-2xl font-bold tracking-tight">Laporan Density Water Sample</h2>
        </div>

        <Card class="border-none shadow-sm">
            <CardHeader class="flex flex-col items-start justify-between gap-4 md:flex-row md:items-center">
                <CardTitle class="flex items-center gap-2 text-xl">
                    <IconFileSpreadsheet class="size-6 text-primary" />
                    Pilih Tanggal Test
                </CardTitle>
                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                    <Input v-model="tglTest" type="date" class="sm:w-52" @change="search" />
                    <Button :disabled="!tglTest" @click="exportCsv">
                        <IconDownload class="mr-2 size-4" /> Export CSV
                    </Button>
                </div>
            </CardHeader>
            <CardContent>
                <p v-if="!tglTest" class="py-8 text-center text-muted-foreground">Pilih tanggal test untuk menampilkan laporan.</p>
                <p v-else-if="samples.length === 0" class="py-8 text-center text-muted-foreground">Tidak ada data pada tanggal tersebut.</p>
                <div v-else class="w-full overflow-x-auto rounded-lg border">
                    <Table class="min-w-[2200px]">
                        <TableHeader>
                            <TableRow class="bg-muted/50">
                                <TableHead>Urutan</TableHead>
                                <TableHead>Tanggal Test</TableHead>
                                <TableHead>Kode Sample</TableHead>
                                <TableHead>No. Open</TableHead>
                                <TableHead>Ketebalan (mm)</TableHead>
                                <TableHead>Berat Awal (gr)</TableHead>
                                <TableHead>Berat Akhir (gr)</TableHead>
                                <TableHead>Volume (ml)</TableHead>
                                <TableHead>Density</TableHead>
                                <TableHead>Perlakuan</TableHead>
                                <TableHead>Palm WO</TableHead>
                                <TableHead>Palm WA</TableHead>
                                <TableHead>Palm Water</TableHead>
                                <TableHead>MC WO</TableHead>
                                <TableHead>MC WA</TableHead>
                                <TableHead>MC Water</TableHead>
                                <TableHead>SL WO</TableHead>
                                <TableHead>SL WA</TableHead>
                                <TableHead>SL Water</TableHead>
                                <TableHead>Spec</TableHead>
                                <TableHead>Mulai Proses</TableHead>
                                <TableHead>Selesai Proses</TableHead>
                                <TableHead>Temp Air (°C)</TableHead>
                                <TableHead>Status</TableHead>
                                <TableHead>Penginput 1</TableHead>
                                <TableHead>Penginput 2</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="item in samples" :key="item.id">
                                <TableCell>{{ item.urutan }}</TableCell>
                                <TableCell>{{ item.tgl_test }}</TableCell>
                                <TableCell>{{ item.kode_sample }}</TableCell>
                                <TableCell>{{ display(item.no_open) }}</TableCell>
                                <TableCell>{{ display(item.ketebalan) }}</TableCell>
                                <TableCell>{{ display(item.berat_awal) }}</TableCell>
                                <TableCell>{{ display(item.berat_akhir) }}</TableCell>
                                <TableCell>{{ display(item.volume) }}</TableCell>
                                <TableCell>{{ display(item.density) }}</TableCell>
                                <TableCell>{{ display(item.perlakuan) }}</TableCell>
                                <TableCell>{{ display(item.palm_wo) }}</TableCell>
                                <TableCell>{{ display(item.palm_wa) }}</TableCell>
                                <TableCell>{{ display(item.palm_water) }}</TableCell>
                                <TableCell>{{ display(item.mc_wo) }}</TableCell>
                                <TableCell>{{ display(item.mc_wa) }}</TableCell>
                                <TableCell>{{ display(item.mc_water) }}</TableCell>
                                <TableCell>{{ display(item.sl_wo) }}</TableCell>
                                <TableCell>{{ display(item.sl_wa) }}</TableCell>
                                <TableCell>{{ display(item.sl_water) }}</TableCell>
                                <TableCell>{{ display(item.spec) }}</TableCell>
                                <TableCell>{{ display(item.mulai_proses) }}</TableCell>
                                <TableCell>{{ display(item.selesai_proses) }}</TableCell>
                                <TableCell>{{ display(item.temp_air) }}</TableCell>
                                <TableCell>{{ item.status === "selesai" ? "Selesai" : "Belum Lengkap" }}</TableCell>
                                <TableCell>{{ userName(item.creator) }}</TableCell>
                                <TableCell>{{ userName(item.completer) }}</TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
