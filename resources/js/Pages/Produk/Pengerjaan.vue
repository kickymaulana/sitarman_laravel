<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";
import { ref, onMounted } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: { id: number };
    produk: any;
    ovens: Array<{ id: number; oven: string }>;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    spesifikasis: Array<{ id: number; spesifikasi: string }>;
    tinggiformers: Array<{ id: number; tinggi_former: number }>;
    jamkeluarovens: Array<{ id: number; jam_keluar_oven: string }>;
}>();

// Form hanya mengirim data yang akan di-update oleh user pengerjaan
const form = useForm({
    suhu_actual: props.produk.suhu_actual ?? "",
    hasil_test: props.produk.hasil_test === "Belum Tes" ? "Belum Tes" : (props.produk.hasil_test ?? "OK"),
    keterangan: props.produk.keterangan ?? ""
});

// State Text untuk Detail Ringkas (Read-only)
const searchOven = ref("");
const searchCust = ref("");
const searchModel = ref("");
const searchSpec = ref("");
const searchTinggi = ref("");
const searchJam = ref("");

onMounted(() => {
    searchOven.value = props.ovens.find(o => o.id === props.produk.oven_id)?.oven || "-";
    searchCust.value = props.customers.find(c => c.id === props.produk.customer_id)?.customer || "-";
    searchModel.value = props.modelsizes.find(m => m.id === props.produk.modelsize_id)?.modelsize || "-";
    searchSpec.value = props.spesifikasis.find(s => s.id === props.produk.spesifikasi_id)?.spesifikasi || "-";

    // Sinkronisasi data tinggi former
    searchTinggi.value = props.tinggiformers.find(t => t.id === props.produk.tinggi_former_id)?.tinggi_former
        ? props.tinggiformers.find(t => t.id === props.produk.tinggi_former_id)?.tinggi_former + " mm"
        : "-";

    // Sinkronisasi data jam keluar oven
    const jm = props.jamkeluarovens.find(j => j.id === props.produk.jam_keluar_oven_id)?.jam_keluar_oven;
    searchJam.value = jm ? jm.substring(0, 5) + " WIB" : "-";
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
                    <form @submit.prevent="form.post(route('produk.pengerjaan.store', [props.thermalshock.id, props.produk.id]))" class="space-y-6">

                        <!-- ================= SECTION 1: DATA READ-ONLY (3 KOLOM SEIMBANG) ================= -->
                        <div class="bg-zinc-50 dark:bg-zinc-900/40 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800">
                            <p class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4">
                                Informasi Identitas & Parameter Produk
                            </p>

                            <!-- Menggunakan Grid 3 Kolom di Desktop -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                <!-- KOLOM 1: IDENTITAS DASAR -->
                                <div class="space-y-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs font-medium text-muted-foreground">Kode Tanah</span>
                                        <span class="text-base font-semibold text-foreground">{{ props.produk.kode_tanah }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                        <span class="text-xs font-medium text-muted-foreground">Kode Bakar</span>
                                        <span class="text-base font-semibold text-amber-600 dark:text-amber-400">{{ props.produk.kode_bakar }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                        <span class="text-xs font-medium text-muted-foreground">Sampel</span>
                                        <span class="text-base font-semibold text-foreground">{{ props.produk.sampel || '-' }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                        <span class="text-xs font-medium text-muted-foreground">Customer</span>
                                        <span class="text-base font-semibold text-foreground">{{ searchCust }}</span>
                                    </div>
                                </div>

                                <!-- KOLOM 2: SPESIFIKASI PRODUK -->
                                <div class="space-y-4 border-t pt-4 md:border-t-0 md:border-l md:pl-6 border-zinc-200 dark:border-zinc-800">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs font-medium text-muted-foreground">Model Size</span>
                                        <span class="text-base font-semibold text-foreground">{{ searchModel }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                        <span class="text-xs font-medium text-muted-foreground">Spesifikasi</span>
                                        <span class="text-base font-semibold text-foreground">{{ searchSpec }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                        <span class="text-xs font-medium text-muted-foreground">Tinggi Former</span>
                                        <span class="text-base font-semibold text-indigo-600 dark:text-indigo-400">{{ searchTinggi }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                        <span class="text-xs font-medium text-muted-foreground">Berat Former</span>
                                        <span class="text-base font-semibold text-foreground">{{ props.produk.berat_former }} gr</span>
                                    </div>
                                </div>

                                <!-- KOLOM 3: LOG OVEN & WAKTU -->
                                <div class="space-y-4 border-t pt-4 md:border-t-0 md:border-l md:pl-6 border-zinc-200 dark:border-zinc-800">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-xs font-medium text-muted-foreground">Oven</span>
                                        <span class="text-base font-semibold text-foreground">{{ searchOven }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                        <span class="text-xs font-medium text-muted-foreground">Tanggal Keluar Oven</span>
                                        <span class="text-base font-semibold text-foreground">{{ props.produk.tanggal_keluar_oven }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                        <span class="text-xs font-medium text-muted-foreground">Jam Keluar Oven</span>
                                        <span class="text-base font-semibold text-foreground">{{ searchJam }}</span>
                                    </div>
                                    <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                        <span class="text-xs font-medium text-muted-foreground">Tanggal Produksi</span>
                                        <span class="text-base font-semibold text-foreground">{{ props.produk.tgl_produksi }}</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ================= SECTION 2: INPUT UTAMA PENGUJIAN ================= -->
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
                                        <option value="Belum Tes">Belum Tes</option>
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

                        <!-- Tombol Aksi Simpan -->
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
