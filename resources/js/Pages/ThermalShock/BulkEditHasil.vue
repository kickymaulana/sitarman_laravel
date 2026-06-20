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
        suhu_testing: string;
        customer: { customer: string } | null;
        model_size: { modelsize: string } | null;
        hasil_test_180: string;
        hasil_test_200: string;
        keterangan: string;
    }>;
    selectedIds: Array<string>;
}>();

// UBAH: Ganti key 'data' menjadi 'records' agar tidak conflict dengan internal helper Inertia
const form = useForm({
    records: props.thermalshocks.map(item => ({
        id: item.id,
        posisi_former: item.posisi_former,
        customer_name: item.customer?.customer ?? '-',
        modelsize_name: item.model_size?.modelsize ?? '-',
        suhu_testing: item.suhu_testing,
        hasil_test_180: item.hasil_test_180 || 'Belum Tes',
        hasil_test_200: item.hasil_test_200 || 'Belum Tes',
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

        <form @submit.prevent="submit" class="space-y-6 max-w-5xl">
            <Card class="border-none shadow-md">
                <CardHeader class="border-b bg-zinc-50/50 dark:bg-zinc-900/50">
                    <CardTitle class="text-emerald-600 flex items-center gap-2 text-lg">
                        <IconChecklist class="size-5" /> Mengisi {{ form.records.length }} Data (Berurutan Berdasarkan Posisi Former)
                    </CardTitle>
                </CardHeader>
                <CardContent class="p-0">
                    <div class="overflow-x-auto">
                        <Table>
                            <TableHeader>
                                <TableRow class="bg-muted/40">
                                    <TableHead class="w-20 text-center font-bold text-zinc-700 dark:text-zinc-300">Posisi</TableHead>
                                    <TableHead>Customer / Model</TableHead>
                                    <TableHead class="text-center">Suhu Test Master</TableHead>
                                    <TableHead class="w-44">Hasil Test 180°C</TableHead>
                                    <TableHead class="w-44">Hasil Test 200°C</TableHead>
                                    <TableHead class="min-w-[200px]">Keterangan</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(row, index) in form.records" :key="row.id" class="hover:bg-muted/20">
                                    <TableCell class="text-center font-bold text-base text-primary">
                                        {{ row.posisi_former }}
                                    </TableCell>

                                    <TableCell>
                                        <div class="flex flex-col">
                                            <span class="font-semibold text-sm">{{ row.customer_name }}</span>
                                            <span class="text-xs text-muted-foreground">{{ row.modelsize_name }}</span>
                                        </div>
                                    </TableCell>

                                    <TableCell class="text-center font-medium text-xs text-zinc-500">
                                        {{ row.suhu_testing }} °C
                                    </TableCell>

                                    <TableCell>
                                        <Select v-model="form.records[index].hasil_test_180">
                                            <SelectTrigger class="h-9"><SelectValue /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Belum Tes">Belum Tes</SelectItem>
                                                <SelectItem value="OK">OK</SelectItem>
                                                <SelectItem value="NG">NG</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </TableCell>

                                    <TableCell>
                                        <Select v-model="form.records[index].hasil_test_200">
                                            <SelectTrigger class="h-9"><SelectValue /></SelectTrigger>
                                            <SelectContent>
                                                <SelectItem value="Belum Tes">Belum Tes</SelectItem>
                                                <SelectItem value="OK">OK</SelectItem>
                                                <SelectItem value="NG">NG</SelectItem>
                                            </SelectContent>
                                        </Select>
                                    </TableCell>

                                    <TableCell class="pr-4">
                                        <Input
                                            type="text"
                                            v-model="form.records[index].keterangan"
                                            class="h-9"
                                            placeholder="Catatan defect..."
                                        />
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <Button type="submit" :disabled="form.processing" class="w-full bg-emerald-600 hover:bg-emerald-700 shadow-md h-11 text-white">
                <IconLoader2 v-if="form.processing" class="mr-2 animate-spin size-5" />
                <IconDeviceFloppy v-else class="mr-2 size-5" /> Simpan Semua Hasil Test Masal
            </Button>
        </form>
    </div>
</template>
