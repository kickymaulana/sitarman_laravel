<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";
import { ref, computed, watch, onMounted } from "vue";
import { onClickOutside } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    density: { id: number };
    lastProduk: any;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    spesifikasis: Array<{ id: number; spesifikasi: string }>; // Properti baru
    ovens: Array<{ id: number; oven: string }>;
    jamkeluarovens: Array<{ id: number; jam_keluar_oven: string }>;
}>();

const form = useForm({
    tgl_produksi: props.lastProduk?.tgl_produksi || "",
    sample : props.lastProduk?.sample || "-",
    customer_id: props.lastProduk?.customer_id || "",
    modelsize_id: props.lastProduk?.modelsize_id || "",
    spesifikasi_id: props.lastProduk?.spesifikasi_id || "", // Field spesifikasi baru
    oven_id: props.lastProduk?.oven_id || "",
    jam_keluar_oven_id: props.lastProduk?.jam_keluar_oven_id || "",
    no: props.lastProduk?.no ? props.lastProduk.no + 1 : 1,
    ketebalan: 0,
    berat_awal: 0,
    berat_akhir: 0,
    volume: 0,
    density: 0,
});

// 1. Kalkulasi Volume Otomatis (Berat Awal - Berat Akhir)
const calculatedVolume = computed(() => {
    const awal = form.berat_awal || 0;
    const akhir = form.berat_akhir || 0;
    const hasil = awal - akhir;
    return hasil > 0 ? parseFloat(hasil.toFixed(2)) : 0;
});

// 2. Kalkulasi Density Otomatis (Berat Awal / Volume Otomatis)
const calculatedDensity = computed(() => {
    const awal = form.berat_awal || 0;
    const vol = calculatedVolume.value;
    if (!vol) return 0;
    return parseFloat((awal / vol).toFixed(2));
});

// Masukkan hasil volume otomatis ke form objek
watch(calculatedVolume, (newVol) => {
    form.volume = newVol;
});

// Masukkan hasil density otomatis ke form objek
watch(calculatedDensity, (newDensity) => {
    form.density = newDensity;
});

// Dropdown State Managers
const searchCust = ref(""); const showCustDrop = ref(false); const custRef = ref(null);
const searchModel = ref(""); const showModelDrop = ref(false); const modelRef = ref(null);
const searchSpec = ref(""); const showSpecDrop = ref(false); const specRef = ref(null); // State spec baru
const searchOven = ref(""); const showOvenDrop = ref(false); const ovenRef = ref(null);
const searchJam = ref(""); const showJamDrop = ref(false); const jamRef = ref(null);

onMounted(() => {
    if (form.customer_id) searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "";
    if (form.modelsize_id) searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "";
    if (form.spesifikasi_id) searchSpec.value = props.spesifikasis.find(s => s.id === form.spesifikasi_id)?.spesifikasi || "";
    if (form.oven_id) searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "";
    if (form.jam_keluar_oven_id) {
        const jm = props.jamkeluarovens.find(j => j.id === form.jam_keluar_oven_id)?.jam_keluar_oven;
        searchJam.value = jm ? jm.substring(0, 5) : "";
    }
});

onClickOutside(custRef, () => { showCustDrop.value = false; searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "" });
onClickOutside(modelRef, () => { showModelDrop.value = false; searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "" });
onClickOutside(specRef, () => { showSpecDrop.value = false; searchSpec.value = props.spesifikasis.find(s => s.id === form.spesifikasi_id)?.spesifikasi || "" });
onClickOutside(ovenRef, () => { showOvenDrop.value = false; searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "" });
onClickOutside(jamRef, () => { showJamDrop.value = false; if(!form.jam_keluar_oven_id) searchJam.value = ""; else { const jm = props.jamkeluarovens.find(j => j.id === form.jam_keluar_oven_id)?.jam_keluar_oven; searchJam.value = jm ? jm.substring(0, 5) : "" } });

const filteredCusts = computed(() => props.customers.filter(c => c.customer.toLowerCase().includes(searchCust.value.toLowerCase())));
const filteredModels = computed(() => {
    const subset = form.customer_id ? props.modelsizes.filter(m => m.customer_id === form.customer_id) : props.modelsizes;
    return subset.filter(m => m.modelsize.toLowerCase().includes(searchModel.value.toLowerCase()));
});
const filteredSpecs = computed(() => props.spesifikasis.filter(s => s.spesifikasi.toLowerCase().includes(searchSpec.value.toLowerCase())));
const filteredOvens = computed(() => props.ovens.filter(o => o.oven.toLowerCase().includes(searchOven.value.toLowerCase())));
const filteredJams = computed(() => props.jamkeluarovens.filter(j => j.jam_keluar_oven.toLowerCase().includes(searchJam.value.toLowerCase())));

watch(() => form.customer_id, () => { form.modelsize_id = ""; searchModel.value = ""; });
</script>

<template>
    <Head title="Tambah Item Density"/>
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('produkdensity.index', props.density.id)"><IconArrowLeft class="size-4"/></Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Tambah Item Density</h2>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardContent class="p-6">
                    <form @submit.prevent="form.post(route('produkdensity.store', props.density.id))" class="space-y-6">

                        <!-- Row 1: Identitas Dasar -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2">
                                <Label>No</Label>
                                <Input type="number" v-model="form.no"/>
                                <p v-if="form.errors.no" class="text-xs text-destructive">{{ form.errors.no }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label>Tanggal Produksi</Label>
                                <Input type="date" v-model="form.tgl_produksi"/>
                                <p v-if="form.errors.tgl_produksi" class="text-xs text-destructive">{{ form.errors.tgl_produksi }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label>Sample</Label>
                                <Input type="text" v-model="form.sample"/>
                                <p v-if="form.errors.sample" class="text-xs text-destructive">{{ form.errors.sample }}</p>
                            </div>
                        </div>

                        <!-- Row 2: Relasi Dropdown Dinamis -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Customer -->
                            <div class="grid gap-2 relative" ref="custRef">
                                <Label>Customer</Label>
                                <Input v-model="searchCust" @focus="showCustDrop = true" placeholder="Pilih Customer..."/>
                                <div v-if="showCustDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="c in filteredCusts" :key="c.id" @click="form.customer_id = c.id; searchCust = c.customer; showCustDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ c.customer }}</div>
                                </div>
                                <p v-if="form.errors.customer_id" class="text-xs text-destructive">{{ form.errors.customer_id }}</p>
                            </div>

                            <!-- Model Size -->
                            <div class="grid gap-2 relative" ref="modelRef">
                                <Label>Model Size</Label>
                                <Input v-model="searchModel" @focus="showModelDrop = true" :disabled="!form.customer_id" placeholder="Pilih Model Size..."/>
                                <div v-if="showModelDrop && form.customer_id" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="m in filteredModels" :key="m.id" @click="form.modelsize_id = m.id; searchModel = m.modelsize; showModelDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ m.modelsize }}</div>
                                </div>
                                <p v-if="form.errors.modelsize_id" class="text-xs text-destructive">{{ form.errors.modelsize_id }}</p>
                            </div>

                            <!-- Spesifikasi (Tambahan Baru) -->
                            <div class="grid gap-2 relative" ref="specRef">
                                <Label>Spesifikasi</Label>
                                <Input v-model="searchSpec" @focus="showSpecDrop = true" placeholder="Pilih Spesifikasi..."/>
                                <div v-if="showSpecDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="s in filteredSpecs" :key="s.id" @click="form.spesifikasi_id = s.id; searchSpec = s.spesifikasi; showSpecDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ s.spesifikasi }}</div>
                                </div>
                                <p v-if="form.errors.spesifikasi_id" class="text-xs text-destructive">{{ form.errors.spesifikasi_id }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Oven -->
                            <div class="grid gap-2 relative" ref="ovenRef">
                                <Label>Oven</Label>
                                <Input v-model="searchOven" @focus="showOvenDrop = true" placeholder="Pilih Oven..."/>
                                <div v-if="showOvenDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="o in filteredOvens" :key="o.id" @click="form.oven_id = o.id; searchOven = o.oven; showOvenDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ o.oven }}</div>
                                </div>
                                <p v-if="form.errors.oven_id" class="text-xs text-destructive">{{ form.errors.oven_id }}</p>
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

                        <hr class="my-4" />

                        <!-- Section Parameter Density -->
                        <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900 border space-y-4">
                            <span class="text-xs font-bold uppercase text-zinc-400">Parameter Ukur & Input</span>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="grid gap-1.5">
                                    <Label>Ketebalan (mm)</Label>
                                    <Input type="number" step="0.01" v-model.number="form.ketebalan"/>
                                    <p v-if="form.errors.ketebalan" class="text-xs text-destructive">{{ form.errors.ketebalan }}</p>
                                </div>
                                <div class="grid gap-1.5">
                                    <Label>Berat Awal (gr)</Label>
                                    <Input type="number" step="0.01" v-model.number="form.berat_awal"/>
                                    <p v-if="form.errors.berat_awal" class="text-xs text-destructive">{{ form.errors.berat_awal }}</p>
                                </div>
                                <div class="grid gap-1.5">
                                    <Label>Berat Akhir (gr)</Label>
                                    <Input type="number" step="0.01" v-model.number="form.berat_akhir"/>
                                    <p v-if="form.errors.berat_akhir" class="text-xs text-destructive">{{ form.errors.berat_akhir }}</p>
                                </div>

                                <div class="grid gap-1.5">
                                    <Label>Volume (ml)</Label>
                                    <Input type="number" step="0.01" :value="calculatedVolume" readonly class="bg-zinc-100 dark:bg-zinc-800 cursor-not-allowed font-medium text-amber-600"/>
                                    <p v-if="form.errors.volume" class="text-xs text-destructive">{{ form.errors.volume }}</p>
                                </div>
                            </div>

                            <div class="pt-2 border-t text-base font-bold flex justify-between items-center">
                                <span>Hasil Nilai Density:</span>
                                <span class="text-xl text-blue-600 underline">{{ calculatedDensity }} p=m/V</span>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin"/>
                            <IconDeviceFloppy v-else class="mr-2"/> Simpan Data Density
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
