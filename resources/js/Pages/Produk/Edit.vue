<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";
import { ref, computed, watch, onMounted } from "vue";
import { onClickOutside } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: { id: number };
    produk: any; // Data produk yang akan diedit
    ovens: Array<{ id: number; oven: string }>;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    spesifikasis: Array<{ id: number; spesifikasi: string }>;
    tinggiformers: Array<{ id: number; tinggi_former: number }>;
    jamkeluarovens: Array<{ id: number; jam_keluar_oven: string }>;
}>();

// Set default value form dari data produk yang di-edit
const form = useForm({
    kode_tanah: props.produk?.kode_tanah || "",
    oven_id: props.produk?.oven_id || "",
    customer_id: props.produk?.customer_id || "",
    modelsize_id: props.produk?.modelsize_id || "",
    spesifikasi_id: props.produk?.spesifikasi_id || "",
    tinggi_former_id: props.produk?.tinggi_former_id || "",
    jam_keluar_oven_id: props.produk?.jam_keluar_oven_id || "",
    sampel: props.produk?.sampel || "",
    berat_former: props.produk?.berat_former || "",
    tanggal_keluar_oven: props.produk?.tanggal_keluar_oven || "",
    tgl_produksi: props.produk?.tgl_produksi || "",
    posisi_former: props.produk?.posisi_former || "",
    hasil_test: props.produk?.hasil_test || "Belum Tes",
    suhu_actual: props.produk?.suhu_actual || "",
    keterangan: props.produk?.keterangan || ""
});

// Search Dropdown State Managers
const searchOven = ref(""); const showOvenDrop = ref(false); const ovenRef = ref(null);
const searchCust = ref(""); const showCustDrop = ref(false); const custRef = ref(null);
const searchModel = ref(""); const showModelDrop = ref(false); const modelRef = ref(null);
const searchSpec = ref(""); const showSpecDrop = ref(false); const specRef = ref(null);
const searchTinggi = ref(""); const showTinggiDrop = ref(false); const tinggiRef = ref(null);
const searchJam = ref(""); const showJamDrop = ref(false); const jamRef = ref(null);

// Sinkronisasi teks pencarian dropdown di awal (On Mounted) agar tulisan ID berubah jadi Nama Master
onMounted(() => {
    if (form.oven_id) searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "";
    if (form.customer_id) searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "";
    if (form.modelsize_id) searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "";
    if (form.spesifikasi_id) searchSpec.value = props.spesifikasis.find(s => s.id === form.spesifikasi_id)?.spesifikasi || "";
    if (form.tinggi_former_id) searchTinggi.value = props.tinggiformers.find(t => t.id === form.tinggi_former_id)?.tinggi_former?.toString() || "";
    if (form.jam_keluar_oven_id) {
        const jm = props.jamkeluarovens.find(j => j.id === form.jam_keluar_oven_id)?.jam_keluar_oven;
        searchJam.value = jm ? jm.substring(0, 5) : "";
    }
});

onClickOutside(ovenRef, () => { showOvenDrop.value = false; if(!form.oven_id) searchOven.value = ""; else searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "" });
onClickOutside(custRef, () => { showCustDrop.value = false; if(!form.customer_id) searchCust.value = ""; else searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "" });
onClickOutside(modelRef, () => { showModelDrop.value = false; if(!form.modelsize_id) searchModel.value = ""; else searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "" });
onClickOutside(specRef, () => { showSpecDrop.value = false; if(!form.spesifikasi_id) searchSpec.value = ""; else searchSpec.value = props.spesifikasis.find(s => s.id === form.spesifikasi_id)?.spesifikasi || "" });
onClickOutside(tinggiRef, () => { showTinggiDrop.value = false; if(!form.tinggi_former_id) searchTinggi.value = ""; else searchTinggi.value = props.tinggiformers.find(t => t.id === form.tinggi_former_id)?.tinggi_former?.toString() || "" });
onClickOutside(jamRef, () => { showJamDrop.value = false; if(!form.jam_keluar_oven_id) searchJam.value = ""; else { const jm = props.jamkeluarovens.find(j => j.id === form.jam_keluar_oven_id)?.jam_keluar_oven; searchJam.value = jm ? jm.substring(0, 5) : "" } });

// Computed Filter List
const filteredOvens = computed(() => props.ovens.filter(o => o.oven.toLowerCase().includes(searchOven.value.toLowerCase())));
const filteredCusts = computed(() => props.customers.filter(c => c.customer.toLowerCase().includes(searchCust.value.toLowerCase())));
const filteredModels = computed(() => {
    const subset = form.customer_id ? props.modelsizes.filter(m => m.customer_id === form.customer_id) : props.modelsizes;
    return subset.filter(m => m.modelsize.toLowerCase().includes(searchModel.value.toLowerCase()));
});
const filteredSpecs = computed(() => props.spesifikasis.filter(s => s.spesifikasi.toLowerCase().includes(searchSpec.value.toLowerCase())));
const filteredTinggis = computed(() => props.tinggiformers.filter(t => t.tinggi_former.toString().includes(searchTinggi.value)));
const filteredJams = computed(() => props.jamkeluarovens.filter(j => j.jam_keluar_oven.toLowerCase().includes(searchJam.value.toLowerCase())));

// Reset model size HANYA jika user mengubah customer secara manual setelah page load
watch(() => form.customer_id, (newVal, oldVal) => {
    if (oldVal !== undefined && oldVal !== "") {
        form.modelsize_id = "";
        searchModel.value = "";
    }
});
</script>

<template>
    <Head title="Edit Produk Thermal" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('produk.index', props.thermalshock.id)"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Edit Item Produk</h2>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardHeader><CardTitle class="text-primary">Form Edit Data Log Produk</CardTitle></CardHeader>
                <CardContent>
                    <!-- Menggunakan form.put untuk update data -->
                    <form @submit.prevent="form.put(route('produk.update', [props.thermalshock.id, props.produk.id]))" class="space-y-6">

                        <!-- Row 1: Informasi Dasar -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2">
                                <Label for="kode_tanah">Kode Tanah</Label>
                                <Input id="kode_tanah" v-model="form.kode_tanah" placeholder="Masukkan kode tanah" />
                                <p v-if="form.errors.kode_tanah" class="text-xs text-destructive">{{ form.errors.kode_tanah }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="sampel">Sampel</Label>
                                <Input id="sampel" v-model="form.sampel" placeholder="Contoh: -" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="hasil_test">Hasil Test</Label>
                                <select id="hasil_test" v-model="form.hasil_test" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm">
                                    <option value="Belum Tes">Belum Tes</option>
                                    <option value="OK">OK</option>
                                    <option value="NG">NG</option>
                                </select>
                            </div>
                        </div>

                        <!-- Row 2: Searchable Dropdown Oven & Customer -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Oven -->
                            <div class="grid gap-2 relative" ref="ovenRef">
                                <Label>Oven</Label>
                                <Input v-model="searchOven" @focus="showOvenDrop = true" placeholder="Pilih Oven..." />
                                <div v-if="showOvenDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="o in filteredOvens" :key="o.id" @click="form.oven_id = o.id; searchOven = o.oven; showOvenDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">
                                        {{ o.oven }}
                                    </div>
                                </div>
                                <p v-if="form.errors.oven_id" class="text-xs text-destructive">{{ form.errors.oven_id }}</p>
                            </div>

                            <!-- Customer -->
                            <div class="grid gap-2 relative" ref="custRef">
                                <Label>Customer</Label>
                                <Input v-model="searchCust" @focus="showCustDrop = true" placeholder="Pilih Customer..." />
                                <div v-if="showCustDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="c in filteredCusts" :key="c.id" @click="form.customer_id = c.id; searchCust = c.customer; showCustDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">
                                        {{ c.customer }}
                                    </div>
                                </div>
                                <p v-if="form.errors.customer_id" class="text-xs text-destructive">{{ form.errors.customer_id }}</p>
                            </div>
                        </div>

                        <!-- Row 3: Model Size & Spesifikasi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Model Size -->
                            <div class="grid gap-2 relative" ref="modelRef">
                                <Label>Model Size <span class="text-[10px] text-muted-foreground italic">(Pilih customer dulu)</span></Label>
                                <Input v-model="searchModel" @focus="showModelDrop = true" :disabled="!form.customer_id" placeholder="Pilih Model Size..." />
                                <div v-if="showModelDrop && form.customer_id" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="m in filteredModels" :key="m.id" @click="form.modelsize_id = m.id; searchModel = m.modelsize; showModelDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">
                                        {{ m.modelsize }}
                                    </div>
                                </div>
                                <p v-if="form.errors.modelsize_id" class="text-xs text-destructive">{{ form.errors.modelsize_id }}</p>
                            </div>

                            <!-- Spesifikasi -->
                            <div class="grid gap-2 relative" ref="specRef">
                                <Label>Spesifikasi</Label>
                                <Input v-model="searchSpec" @focus="showSpecDrop = true" placeholder="Pilih Spesifikasi..." />
                                <div v-if="showSpecDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="s in filteredSpecs" :key="s.id" @click="form.spesifikasi_id = s.id; searchSpec = s.spesifikasi; showSpecDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">
                                        {{ s.spesifikasi }}
                                    </div>
                                </div>
                                <p v-if="form.errors.spesifikasi_id" class="text-xs text-destructive">{{ form.errors.spesifikasi_id }}</p>
                            </div>
                        </div>

                        <!-- Row 4: Relasi Baru (Tinggi Former & Jam Keluar Oven) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Tinggi Former -->
                            <div class="grid gap-2 relative" ref="tinggiRef">
                                <Label>Tinggi Former (mm)</Label>
                                <Input v-model="searchTinggi" @focus="showTinggiDrop = true" placeholder="Pilih Tinggi Former..." />
                                <div v-if="showTinggiDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="t in filteredTinggis" :key="t.id" @click="form.tinggi_former_id = t.id; searchTinggi = t.tinggi_former.toString(); showTinggiDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">
                                        {{ t.tinggi_former }} mm
                                    </div>
                                </div>
                                <p v-if="form.errors.tinggi_former_id" class="text-xs text-destructive">{{ form.errors.tinggi_former_id }}</p>
                            </div>

                            <!-- Jam Keluar Oven -->
                            <div class="grid gap-2 relative" ref="jamRef">
                                <Label>Jam Keluar Oven</Label>
                                <Input v-model="searchJam" @focus="showJamDrop = true" placeholder="Pilih Jam Keluar..." />
                                <div v-if="showJamDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="j in filteredJams" :key="j.id" @click="form.jam_keluar_oven_id = j.id; searchJam = j.jam_keluar_oven.substring(0, 5); showJamDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">
                                        {{ j.jam_keluar_oven.substring(0, 5) }} WIB
                                    </div>
                                </div>
                                <p v-if="form.errors.jam_keluar_oven_id" class="text-xs text-destructive">{{ form.errors.jam_keluar_oven_id }}</p>
                            </div>
                        </div>

                        <!-- Row 5: Parameter Numerik & Tanggal -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="grid gap-2">
                                <Label for="berat_former">Berat Former (gr)</Label>
                                <Input type="number" id="berat_former" v-model="form.berat_former" />
                                <p v-if="form.errors.berat_former" class="text-xs text-destructive">{{ form.errors.berat_former }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="posisi_former">Posisi Former</Label>
                                <Input type="number" id="posisi_former" v-model="form.posisi_former" />
                                <p v-if="form.errors.posisi_former" class="text-xs text-destructive">{{ form.errors.posisi_former }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="suhu_actual">Suhu Aktual (°C)</Label>
                                <Input type="number" id="suhu_actual" v-model="form.suhu_actual" />
                                <p v-if="form.errors.suhu_actual" class="text-xs text-destructive">{{ form.errors.suhu_actual }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="tgl_produksi">Tanggal Produksi</Label>
                                <Input type="date" id="tgl_produksi" v-model="form.tgl_produksi" />
                                <p v-if="form.errors.tgl_produksi" class="text-xs text-destructive">{{ form.errors.tgl_produksi }}</p>
                            </div>
                        </div>

                        <!-- Row 6: Tanggal Keluar Oven & Keterangan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="tanggal_keluar_oven">Tanggal Keluar Oven</Label>
                                <Input type="date" id="tanggal_keluar_oven" v-model="form.tanggal_keluar_oven" />
                                <p v-if="form.errors.tanggal_keluar_oven" class="text-xs text-destructive">{{ form.errors.tanggal_keluar_oven }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="keterangan">Keterangan</Label>
                                <Input id="keterangan" v-model="form.keterangan" placeholder="Opsional" />
                                <p v-if="form.errors.keterangan" class="text-xs text-destructive">{{ form.errors.keterangan }}</p>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <Button type="submit" :disabled="form.processing" class="w-full mt-4 shadow-md bg-primary hover:bg-primary/90">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Perbarui Produk
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
