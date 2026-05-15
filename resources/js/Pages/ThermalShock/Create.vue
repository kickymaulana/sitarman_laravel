<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { IconArrowLeft, IconDeviceFloppy, IconPlus, IconTrash, IconLoader2, IconAlertCircle } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; modelsize: string }>;
}>();

const form = useForm({
    // Header Data
    hari_tgl: new Date().toISOString().split('T')[0],
    suhu_testing: 200,
    suhu_motor: "",
    suhu_display: 0,
    suhu_actual: 0,
    jam_awal_proses: "",
    jam_capai_suhu: "",
    suhu_awal: 0,
    suhu_air: "",
    jam_mulai_tembak: "",
    jam_selesai_tembak: "",

    // Array Detail
    items: [
        {
            customer_id: "",
            modelsize_id: "",
            oven_kode_tanah: "",
            spesifikasi: "-",
            berat_former: 0,
            tanggal_keluar_oven: "",
            tgl_produksi: "",
            posisi_former: 1,
            hasil_test: "OK",
            keterangan: ""
        }
    ]
});

const addRow = () => {
    form.items.push({
        customer_id: "",
        modelsize_id: "",
        oven_kode_tanah: "",
        spesifikasi: "-",
        berat_former: 0,
        tanggal_keluar_oven: "",
        tgl_produksi: "",
        posisi_former: form.items.length + 1,
        hasil_test: "OK",
        keterangan: ""
    });
};

const removeRow = (index: number) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const submit = () => {
    form.post(route('thermalshock.store'));
};
</script>

<template>
    <Head title="Entry Thermal Shock" />
    <div class="p-4 md:p-8 pt-2">
        <div class="flex items-center gap-4 mb-6">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('thermalshock.index')"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Entry Test Thermal Shock</h2>
        </div>


        <div v-if="$page.props.errors.message" class="mb-6 p-4 bg-destructive text-white rounded-lg shadow-lg">
            {{ $page.props.errors.message }}
        </div>

        <div v-if="Object.keys(form.errors).length > 0" class="mb-6 p-4 bg-destructive/10 border border-destructive text-destructive rounded-lg flex items-center gap-3">
            <IconAlertCircle class="size-5" />
            <span class="text-sm font-medium">Ada kesalahan validasi. Silakan periksa kolom yang berwarna merah.</span>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <Card class="border-none shadow-md">
                <CardHeader class="border-b bg-muted/10"><CardTitle class="text-lg">Data Pengetesan (Header)</CardTitle></CardHeader>
                <CardContent class="grid grid-cols-1 md:grid-cols-4 gap-6 pt-6">
                    <div class="space-y-2">
                        <Label>Hari / Tanggal</Label>
                        <Input type="date" v-model="form.hari_tgl" :class="{'border-destructive': form.errors.hari_tgl}" />
                        <p v-if="form.errors.hari_tgl" class="text-xs text-destructive">{{ form.errors.hari_tgl }}</p>
                    </div>
                    <div class="space-y-2">
                        <Label>Suhu Testing (°C)</Label>
                        <Input type="number" v-model="form.suhu_testing" />
                    </div>
                    <div class="space-y-2">
                        <Label>Suhu Motor</Label>
                        <Input v-model="form.suhu_motor" placeholder="Contoh: 15Hz" />
                    </div>
                    <div class="space-y-2">
                        <Label>Suhu Air</Label>
                        <Input v-model="form.suhu_air" placeholder="Contoh: 31/32" />
                    </div>
                    <div class="space-y-2">
                        <Label>Suhu Display</Label>
                        <Input type="number" v-model="form.suhu_display" />
                    </div>
                    <div class="space-y-2">
                        <Label>Suhu Actual</Label>
                        <Input type="number" v-model="form.suhu_actual" />
                    </div>
                    <div class="space-y-2">
                        <Label>Suhu Awal</Label>
                        <Input type="number" v-model="form.suhu_awal" />
                    </div>
                    <div class="space-y-2 text-primary font-bold">---</div>
                    <div class="space-y-2">
                        <Label>Jam Awal Proses</Label>
                        <Input type="time" v-model="form.jam_awal_proses" />
                    </div>
                    <div class="space-y-2">
                        <Label>Jam Capai Suhu</Label>
                        <Input type="time" v-model="form.jam_capai_suhu" />
                    </div>
                    <div class="space-y-2">
                        <Label>Jam Mulai Tembak</Label>
                        <Input type="time" v-model="form.jam_mulai_tembak" />
                    </div>
                    <div class="space-y-2">
                        <Label>Jam Selesai Tembak</Label>
                        <Input type="time" v-model="form.jam_selesai_tembak" />
                    </div>
                </CardContent>
            </Card>


            <Card class="border-none shadow-md overflow-hidden">
                    <CardHeader class="flex flex-row items-center justify-between border-b bg-muted/20">
                        <CardTitle class="text-lg">Isi Oven / Daftar Item</CardTitle>
                        <Button type="button" @click="addRow" variant="outline" size="sm" class="bg-white hover:bg-primary">
                            <IconPlus class="mr-2 size-4" /> Tambah Item
                        </Button>
                    </CardHeader>
                    <CardContent class="p-0">
                        <div class="overflow-x-auto">
                            <Table>
                                <TableHeader>
                                    <TableRow class="bg-muted/30">
                                        <TableHead class="w-[120px]">Oven/Kode</TableHead>
                                        <TableHead class="w-[180px]">Customer</TableHead>
                                        <TableHead class="w-[180px]">Model/Size</TableHead>
                                        <TableHead class="w-[150px]">Spesifikasi</TableHead>
                                        <TableHead class="w-[120px]">Berat (g)</TableHead>
                                        <TableHead class="w-[140px]">Keluar Oven</TableHead>
                                        <TableHead class="w-[140px]">Tgl Prod</TableHead>
                                        <TableHead class="w-[80px]">Pos</TableHead>
                                        <TableHead class="w-[100px]">Hasil</TableHead>
                                        <TableHead class="w-[200px]">Keterangan</TableHead>
                                        <TableHead class="w-[50px]"></TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow v-for="(item, index) in form.items" :key="index">
                                        <TableCell>
                                            <Input v-model="item.oven_kode_tanah" placeholder="..." />
                                        </TableCell>
                                        <TableCell>
                                            <select v-model="item.customer_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                                <option value="">Pilih...</option>
                                                <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.customer }}</option>
                                            </select>
                                        </TableCell>
                                        <TableCell>
                                            <select v-model="item.modelsize_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm">
                                                <option value="">Pilih...</option>
                                                <option v-for="m in modelsizes" :key="m.id" :value="m.id">{{ m.modelsize }}</option>
                                            </select>
                                        </TableCell>
                                        <TableCell>
                                            <Input v-model="item.spesifikasi" placeholder="Contoh: Glazed" />
                                        </TableCell>
                                        <TableCell><Input type="number" v-model="item.berat_former" /></TableCell>
                                        <TableCell><Input type="date" v-model="item.tanggal_keluar_oven" /></TableCell>
                                        <TableCell><Input type="date" v-model="item.tgl_produksi" /></TableCell>
                                        <TableCell><Input type="number" v-model="item.posisi_former" /></TableCell>
                                        <TableCell>
                                            <select v-model="item.hasil_test" :class="['flex h-10 w-full rounded-md border border-input px-3 py-2 text-sm font-bold', item.hasil_test === 'NG' ? 'bg-destructive/10 text-destructive' : 'bg-green-50 text-green-700']">
                                                <option value="OK">OK</option>
                                                <option value="NG">NG</option>
                                            </select>
                                        </TableCell>

                                        <TableCell>
                                            <Input v-model="item.keterangan" placeholder="Catatan item..." :class="{'border-destructive': form.errors[`items.${index}.keterangan`]}" />
                                            <p v-if="form.errors[`items.${index}.keterangan`]" class="text-[10px] text-destructive">
                                                {{ form.errors[`items.${index}.keterangan`] }}
                                            </p>
                                        </TableCell>

                                        <TableCell>
                                            <Button type="button" variant="ghost" size="icon" @click="removeRow(index)" class="text-destructive">
                                                <IconTrash class="size-4" />
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </div>
                    </CardContent>
                </Card>

            <div class="flex items-center justify-between bg-muted/10 p-4 rounded-lg border border-dashed">
                <p class="text-sm text-muted-foreground italic">Pastikan semua data di atas sudah sesuai dengan formulir fisik sebelum menekan tombol simpan.</p>
                <Button type="submit" class="w-full md:w-64 py-6 text-lg shadow-lg" :disabled="form.processing">
                    <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                    <IconDeviceFloppy v-else class="mr-2" /> Simpan Laporan
                </Button>
            </div>
        </form>
    </div>
</template>
