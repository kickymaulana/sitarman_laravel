<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2, IconChecklist } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshocks: Array<{
        id: number;
        posisi_former: number;
        hari_tgl: string;
        customer: { customer: string; model: string; spesifikasi: string; size: string } | null;
        hasil_test_180: string;
        hasil_180: number | null;
        hasil_test_200: string;
        hasil_200: number | null;
        keterangan: string;
    }>;
    selectedIds: Array<string>;
}>();

const form = useForm({
    records: props.thermalshocks.map(item => ({
        id: item.id,
        posisi_former: item.posisi_former,
        customer_name: item.customer?.customer ?? '-',
        modelsize_name: item.customer ? `${item.customer.model} (Size: ${item.customer.size})` : '-',
        hasil_test_180: item.hasil_test_180 || 'Belum Tes',
        hasil_180: item.hasil_180 ?? 0, // Mapping baru
        hasil_test_200: item.hasil_test_200 || 'Belum Tes',
        hasil_200: item.hasil_200 ?? 0, // Mapping baru
        keterangan: item.keterangan || '-'
    }))
});

const submit = () => {
    form.put(route('thermalshock.bulkUpdate'));
};
</script>

<template>
    <Head title="Bulk Input Hasil Test" />

    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('thermalshock.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Bulk Input Hasil Pengujian</h2>
        </div>

        <form @submit.prevent="submit" class="space-y-6 max-w-6xl mx-auto w-full">
            <Card class="border-none shadow-md">
                <CardHeader class="border-b bg-zinc-50/50 dark:bg-zinc-900/50">
                    <CardTitle class="text-emerald-600 flex items-center gap-2 text-lg">
                        <IconChecklist class="size-5" /> Mengisi {{ form.records.length }} Data (Berurutan Berdasarkan Posisi Former)
                    </CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <Table class="w-full text-xs">
                            <TableHeader>
                                <TableRow class="bg-muted/50 whitespace-nowrap">
                                    <TableHead class="w-16 text-center font-bold" rowspan="2">Posisi</TableHead>
                                    <TableHead rowspan="2">Customer / Model</TableHead>
                                    <TableHead class="text-center bg-blue-50/40 dark:bg-blue-950/20" colspan="2">Pengujian 180°C</TableHead>
                                    <TableHead class="text-center bg-amber-50/40 dark:bg-amber-950/20" colspan="2">Pengujian 200°C</TableHead>
                                    <TableHead class="min-w-[180px]" rowspan="2">Keterangan / Defect</TableHead>
                                </TableRow>
                                <TableRow class="bg-muted/30 whitespace-nowrap text-[11px]">
                                    <TableHead class="w-36 bg-blue-50/20 dark:bg-blue-950/10">Status 180</TableHead>
                                    <TableHead class="w-24 bg-blue-50/20 dark:bg-blue-950/10 text-center">Qty (Pcs)</TableHead>
                                    <TableHead class="w-36 bg-amber-50/20 dark:bg-amber-950/10">Status 200</TableHead>
                                    <TableHead class="w-24 bg-amber-50/20 dark:bg-amber-950/10 text-center">Qty (Pcs)</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(row, index) in form.records" :key="row.id" class="hover:bg-muted/20 whitespace-nowrap">
                                    <TableCell class="text-center font-bold text-base text-primary align-middle">
                                        {{ row.posisi_former }}
                                    </TableCell>

                                    <TableCell class="align-middle">
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ row.customer_name }}</span>
                                            <span class="text-[11px] text-muted-foreground">{{ row.modelsize_name }}</span>
                                        </div>
                                    </TableCell>

                                    <TableCell class="align-middle bg-blue-50/5 dark:bg-blue-950/5">
                                        <Select v-model="form.records[index].hasil_test_180">
                                            <SelectTrigger class="h-8 text-xs"><SelectValue /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Belum Tes">Belum Tes</SelectItem>
                                                <SelectItem value="OK">OK</SelectItem>
                                                <SelectItem value="NG">NG</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </TableCell>

                                    <TableCell class="align-middle bg-blue-50/5 dark:bg-blue-950/5">
                                        <Input
                                            type="number"
                                            v-model.number="form.records[index].hasil_180"
                                            class="h-8 text-xs text-center font-mono"
                                            min="0"
                                            :disabled="form.records[index].hasil_test_180 === 'Belum Tes'"
                                        />
                                    </TableCell>

                                    <TableCell class="align-middle bg-amber-50/5 dark:bg-amber-950/5">
                                        <Select v-model="form.records[index].hasil_test_200">
                                            <SelectTrigger class="h-8 text-xs"><SelectValue /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Belum Tes">Belum Tes</SelectItem>
                                                <SelectItem value="OK">OK</SelectItem>
                                                <SelectItem value="NG">NG</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </TableCell>

                                    <TableCell class="align-middle bg-amber-50/5 dark:bg-amber-950/5">
                                        <Input
                                            type="number"
                                            v-model.number="form.records[index].hasil_200"
                                            class="h-8 text-xs text-center font-mono"
                                            min="0"
                                            :disabled="form.records[index].hasil_test_200 === 'Belum Tes'"
                                        />
                                    </TableCell>

                                    <TableCell class="pr-4 align-middle">
                                        <Input
                                            type="text"
                                            v-model="form.records[index].keterangan"
                                            class="h-8 text-xs"
                                            placeholder="Catatan defect..."
                                        />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <Button type="submit" :disabled="form.processing" class="w-full bg-emerald-600 hover:bg-emerald-700 shadow-md h-11 text-sm font-medium text-white">
                <IconLoader2 v-if="form.processing" class="mr-2 animate-spin size-5" />
                <IconDeviceFloppy v-else class="mr-2 size-5" /> Simpan Semua Hasil Test Masal
            </Button>
        </form>
    </div>
</template>
