<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";
import { ref, computed, onMounted } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: { id: number };
    produk: any;
    ovens: Array<{ id: number; oven: string }>;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    spesifikasis: Array<{ id: number; spesifikasi: string }>;
}>();

// Form hanya mengirim data yang akan di-update oleh user pengerjaan
const form = useForm({
    suhu_actual: props.produk.suhu_actual ?? "",
    hasil_test: props.produk.hasil_test ?? "OK",
    keterangan: props.produk.keterangan ?? ""
});

// State Text untuk Dropdown (Hanya Read-only)
const searchOven = ref("");
const searchCust = ref("");
const searchModel = ref("");
const searchSpec = ref("");

onMounted(() => {
    searchOven.value = props.ovens.find(o => o.id === props.produk.oven_id)?.oven || "";
    searchCust.value = props.customers.find(c => c.id === props.produk.customer_id)?.customer || "";
    searchModel.value = props.modelsizes.find(m => m.id === props.produk.modelsize_id)?.modelsize || "";
    searchSpec.value = props.spesifikasis.find(s => s.id === props.produk.spesifikasi_id)?.spesifikasi || "";
});
</script>

<template>
    <Head title="Pengerjaan Item Produk" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('produk.index', props.thermalshock.id)"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Pengerjaan Log Produk</h2>
            </div>
            <!-- Badge Indikator Posisi Aktif -->
            <div class="bg-primary/10 text-primary font-semibold px-4 py-2 rounded-full border border-primary/20">
                Posisi Former Saat Ini: <span class="text-xl font-bold underline">{{ props.produk.posisi_former }}</span>
            </div>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="border-b pb-4 mb-4">
                    <CardTitle class="text-zinc-500 text-sm font-medium">
                        ID Produk: {{ props.produk.id }} | Kode Tanah: {{ props.produk.kode_tanah }}
                    </CardTitle>
                </CardHeader>

                <CardContent>
                    <!-- Arahkan submit ke route post pengerjaan -->
                    <form @submit.prevent="form.post(route('produk.pengerjaan.store', [props.thermalshock.id, props.produk.id]))" class="space-y-6">

                        <!-- ================= SECTION 1: DATA READ-ONLY (DISABLED) ================= -->
                        <div class="bg-zinc-50 dark:bg-zinc-900/50 p-4 rounded-xl space-y-4 border border-dashed">
                            <p class="text-xs font-bold uppercase tracking-wider text-muted-foreground">Informasi Identitas Produk (Read-Only)</p>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label>Kode Tanah</Label>
                                    <Input :value="props.produk.kode_tanah" class="bg-zinc-100 dark:bg-zinc-800" />
                                </div>
                                <div class="grid gap-2">
                                    <Label>Sampel</Label>
                                    <Input :value="props.produk.sampel" class="bg-zinc-100 dark:bg-zinc-800" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label>Oven</Label>
                                    <Input :value="searchOven" disabled class="bg-zinc-100 dark:bg-zinc-800" />
                                </div>
                                <div class="grid gap-2">
                                    <Label>Customer</Label>
                                    <Input :value="searchCust" disabled class="bg-zinc-100 dark:bg-zinc-800" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="grid gap-2">
                                    <Label>Model Size</Label>
                                    <Input :value="searchModel" disabled class="bg-zinc-100 dark:bg-zinc-800" />
                                </div>
                                <div class="grid gap-2">
                                    <Label>Spesifikasi</Label>
                                    <Input :value="searchSpec" disabled class="bg-zinc-100 dark:bg-zinc-800" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="grid gap-2">
                                    <Label>Berat Former</Label>
                                    <Input :value="props.produk.berat_former" class="bg-zinc-100 dark:bg-zinc-800" />
                                </div>
                                <div class="grid gap-2">
                                    <Label>Tanggal Produksi</Label>
                                    <Input :value="props.produk.tgl_produksi" class="bg-zinc-100 dark:bg-zinc-800" />
                                </div>
                                <div class="grid gap-2">
                                    <Label>Tanggal Keluar Oven</Label>
                                    <Input :value="props.produk.tanggal_keluar_oven" class="bg-zinc-100 dark:bg-zinc-800" />
                                </div>
                            </div>
                        </div>

                        <!-- ================= SECTION 2: INPUT UTAMA (YANG BOLEH DIEDIT) ================= -->
                        <div class="space-y-4 pt-2">
                            <p class="text-xs font-bold uppercase tracking-wider text-primary">Input Hasil Pengujian Kerja</p>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="grid gap-2">
                                    <Label for="suhu_actual" class="text-base font-medium">Suhu Actual (°C)</Label>
                                    <Input type="number" id="suhu_actual" v-model="form.suhu_actual" class="h-12 text-lg border-primary focus-visible:ring-primary" placeholder="Input suhu..." autofocus />
                                    <span v-if="form.errors.suhu_actual" class="text-xs text-destructive">{{ form.errors.suhu_actual }}</span>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="hasil_test" class="text-base font-medium">Hasil Test</Label>
                                    <select id="hasil_test" v-model="form.hasil_test" class="flex h-12 w-full rounded-md border border-primary bg-background px-3 py-2 text-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary">
                                        <option value="OK">OK</option>
                                        <option value="NG">NG</option>
                                    </select>
                                    <span v-if="form.errors.hasil_test" class="text-xs text-destructive">{{ form.errors.hasil_test }}</span>
                                </div>

                                <div class="grid gap-2">
                                    <Label for="keterangan" class="text-base font-medium">Keterangan</Label>
                                    <Input id="keterangan" v-model="form.keterangan" class="h-12 text-base border-primary" placeholder="Tulis catatan jika ada" />
                                    <span v-if="form.errors.keterangan" class="text-xs text-destructive">{{ form.errors.keterangan }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol aksi dinamis -->
                        <Button type="submit" :disabled="form.processing" class="w-full h-12 text-base shadow-md bg-emerald-600 hover:bg-emerald-500 text-white">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan & Lanjut Posisi Berikutnya
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
