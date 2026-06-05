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
    chemical: { id: number };
    lastProduk: any;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    ovens: Array<{ id: number; oven: string }>;
    jamkeluarovens: Array<{ id: number; jam_keluar_oven: string }>;
}>();

// 1. Definisikan semua field sesuai struktur database tabel produk_chemical
const form = useForm({
    tgl_produksi: props.lastProduk?.tgl_produksi || "",
    customer_id: props.lastProduk?.customer_id || "",
    modelsize_id: props.lastProduk?.modelsize_id || "",
    oven_id: props.lastProduk?.oven_id || "",
    tanggal_keluar_oven: props.lastProduk?.tanggal_keluar_oven || "",
    jam_keluar_oven_id: props.lastProduk?.jam_keluar_oven_id || "",
    sample: props.lastProduk?.sample || "-",

    // Input User
    ketebalan_mm: 0,
    berat_awal: 0,
    berat_akhir: 0,
    density: 0,

    // Hasil Kalkulasi Otomatis (Akan diisi lewat watch computed)
    berat_hilang: 0,
    metode_biasa: 0,
    volume: 0,
    ketebalan_dm: 0,
    luas_permukaan: 0,
    hasil_akhir: 0,
    gambar: null as File | null,
});

// 2. Rumus Kalkulasi Real-Time (Computed)
const hitungLab = computed(() => {
    const bAwal = Number(form.berat_awal) || 0;
    const bAkhir = Number(form.berat_akhir) || 0;
    const tmm = Number(form.ketebalan_mm) || 0;
    const dens = Number(form.density) || 0;

    // berat_hilang = berat_awal - berat_akhir
    const berat_hilang = bAwal - bAkhir;

    // metode_biasa = (berat_hilang / berat_awal) * 100
    const metode_biasa = bAwal > 0 ? (berat_hilang / bAwal) * 100 : 0;

    // volume = (berat_awal / density) / 1000
    const volume = dens > 0 ? (bAwal / dens) / 1000 : 0;

    // ketebalan_dm = ketebalan_mm / 100
    const ketebalan_dm = tmm / 100;

    // luas_permukaan = volume / ketebalan_dm
    const luas_permukaan = ketebalan_dm > 0 ? volume / ketebalan_dm : 0;

    // hasil akhir = berat_hilang / luas_permukaan
    const hasil_akhir = luas_permukaan > 0 ? berat_hilang / luas_permukaan : 0;

    return {
        berat_hilang: Number(berat_hilang.toFixed(3)),
        metode_biasa: Number(metode_biasa.toFixed(2)),
        volume: Number(volume.toFixed(6)),
        ketebalan_dm: Number(ketebalan_dm.toFixed(4)),
        luas_permukaan: Number(luas_permukaan.toFixed(2)),
        hasil_akhir: Number(hasil_akhir.toFixed(2))
    };
});

// 3. Watcher untuk memasukkan hasil computed ke dalam objek form sebelum submit
watch(hitungLab, (newVal) => {
    form.berat_hilang = newVal.berat_hilang;
    form.metode_biasa = newVal.metode_biasa;
    form.volume = newVal.volume;
    form.ketebalan_dm = newVal.ketebalan_dm;
    form.luas_permukaan = newVal.luas_permukaan;
    form.hasil_akhir = newVal.hasil_akhir;
}, { immediate: true, deep: true });


// State Dropdown Search (Tetap sama seperti kode Anda)
const searchCust = ref(""); const showCustDrop = ref(false); const custRef = ref(null);
const searchModel = ref(""); const showModelDrop = ref(false); const modelRef = ref(null);
const searchOven = ref(""); const showOvenDrop = ref(false); const ovenRef = ref(null);
const searchJam = ref(""); const showJamDrop = ref(false); const jamRef = ref(null);

onMounted(() => {
    if (form.customer_id) searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "";
    if (form.modelsize_id) searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "";
    if (form.oven_id) searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "";
    if (form.jam_keluar_oven_id) {
        const jm = props.jamkeluarovens.find(j => j.id === form.jam_keluar_oven_id)?.jam_keluar_oven;
        searchJam.value = jm ? jm.substring(0, 5) : "";
    }
});

onClickOutside(custRef, () => { showCustDrop.value = false; searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "" });
onClickOutside(modelRef, () => { showModelDrop.value = false; searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "" });
onClickOutside(ovenRef, () => { showOvenDrop.value = false; searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "" });
onClickOutside(jamRef, () => { showJamDrop.value = false; if(!form.jam_keluar_oven_id) searchJam.value = ""; else { const jm = props.jamkeluarovens.find(j => j.id === form.jam_keluar_oven_id)?.jam_keluar_oven; searchJam.value = jm ? jm.substring(0, 5) : "" } });

const filteredCusts = computed(() => props.customers.filter(c => c.customer.toLowerCase().includes(searchCust.value.toLowerCase())));
const filteredModels = computed(() => {
    const subset = form.customer_id ? props.modelsizes.filter(m => m.customer_id === form.customer_id) : props.modelsizes;
    return subset.filter(m => m.modelsize.toLowerCase().includes(searchModel.value.toLowerCase()));
});
const filteredOvens = computed(() => props.ovens.filter(o => o.oven.toLowerCase().includes(searchOven.value.toLowerCase())));
const filteredJams = computed(() => props.jamkeluarovens.filter(j => j.jam_keluar_oven.toLowerCase().includes(searchJam.value.toLowerCase())));

watch(() => form.customer_id, () => { form.modelsize_id = ""; searchModel.value = ""; });

// State untuk melihat preview lokal sebelum di-upload
const imagePreview = ref<string | null>(null);

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        form.gambar = file;
        imagePreview.value = URL.createObjectURL(file);
    }
};
</script>

<template>
    <Head title="Tambah Item Chemical" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('produkchemical.index', props.chemical.id)"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Tambah Item Sampel Chemical</h2>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardContent class="p-6">
                    <form @submit.prevent="form.post(route('produkchemical.store', props.chemical.id))" class="space-y-6">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2">
                                <Label>Tanggal Produksi</Label>
                                <Input type="date" v-model="form.tgl_produksi" />
                                <p v-if="form.errors.tgl_produksi" class="text-xs text-destructive">{{ form.errors.tgl_produksi }}</p>
                            </div>
                            <div class="grid gap-2 relative" ref="custRef">
                                <Label>Customer</Label>
                                <Input v-model="searchCust" @focus="showCustDrop = true" placeholder="Pilih Customer..." />
                                <div v-if="showCustDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="c in filteredCusts" :key="c.id" @click="form.customer_id = c.id; searchCust = c.customer; showCustDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ c.customer }}</div>
                                </div>
                                <p v-if="form.errors.customer_id" class="text-xs text-destructive">{{ form.errors.customer_id }}</p>
                            </div>
                            <div class="grid gap-2 relative" ref="modelRef">
                                <Label>Model Size</Label>
                                <Input v-model="searchModel" @focus="showModelDrop = true" :disabled="!form.customer_id" placeholder="Pilih Model Size..." />
                                <div v-if="showModelDrop && form.customer_id" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="m in filteredModels" :key="m.id" @click="form.modelsize_id = m.id; searchModel = m.modelsize; showModelDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ m.modelsize }}</div>
                                </div>
                                <p v-if="form.errors.modelsize_id" class="text-xs text-destructive">{{ form.errors.modelsize_id }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="grid gap-2 relative" ref="ovenRef">
                                <Label>Oven</Label>
                                <Input v-model="searchOven" @focus="showOvenDrop = true" placeholder="Pilih Oven..." />
                                <div v-if="showOvenDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="o in filteredOvens" :key="o.id" @click="form.oven_id = o.id; searchOven = o.oven; showOvenDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ o.oven }}</div>
                                </div>
                                <p v-if="form.errors.oven_id" class="text-xs text-destructive">{{ form.errors.oven_id }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label>Tanggal Keluar Oven</Label>
                                <Input type="date" v-model="form.tanggal_keluar_oven" />
                                <p v-if="form.errors.tanggal_keluar_oven" class="text-xs text-destructive">{{ form.errors.tanggal_keluar_oven }}</p>
                            </div>
                            <div class="grid gap-2 relative" ref="jamRef">
                                <Label>Jam Keluar Oven</Label>
                                <Input v-model="searchJam" @focus="showJamDrop = true" placeholder="Pilih Jam..." />
                                <div v-if="showJamDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="j in filteredJams" :key="j.id" @click="form.jam_keluar_oven_id = j.id; searchJam = j.jam_keluar_oven.substring(0, 5); showJamDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ j.jam_keluar_oven.substring(0, 5) }} WIB</div>
                                </div>
                                <p v-if="form.errors.jam_keluar_oven_id" class="text-xs text-destructive">{{ form.errors.jam_keluar_oven_id }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label>Sample</Label>
                                <Input type="text" v-model="form.sample" />
                                <p v-if="form.errors.sample" class="text-xs text-destructive">{{ form.errors.sample }}</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 space-y-4">
    <span class="text-xs font-bold uppercase text-zinc-600 dark:text-zinc-400">Dokumentasi Uji (Gambar)</span>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
        <div class="grid gap-1.5">
            <Label for="gambar_input">Pilih File Gambar</Label>
            <Input id="gambar_input" type="file" accept="image/*" @change="handleFileChange" class="cursor-pointer" />
            <p v-if="form.errors.gambar" class="text-xs text-destructive">{{ form.errors.gambar }}</p>
        </div>
        <div class="flex justify-center md:justify-start">
            <div v-if="imagePreview" class="relative size-24 rounded-lg border overflow-hidden bg-muted">
                <img :src="imagePreview" class="size-full object-cover" alt="Preview" />
            </div>
            <div v-else class="size-24 rounded-lg border border-dashed flex items-center justify-center text-xs text-muted-foreground bg-muted/40">
                Belum ada foto
            </div>
        </div>
    </div>
</div>

                        <div class="p-4 rounded-xl bg-purple-50/40 dark:bg-zinc-900 border border-purple-100 dark:border-zinc-800 space-y-4">
                            <span class="text-xs font-bold uppercase text-purple-600 dark:text-zinc-400">Parameter Fisik Lab (Input)</span>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div class="grid gap-1.5">
                                    <Label>Ketebalan (mm)</Label>
                                    <Input type="number" step="0.01" v-model.number="form.ketebalan_mm" />
                                    <p v-if="form.errors.ketebalan_mm" class="text-xs text-destructive">{{ form.errors.ketebalan_mm }}</p>
                                </div>
                                <div class="grid gap-1.5">
                                    <Label>Berat Awal (gr)</Label>
                                    <Input type="number" step="0.001" v-model.number="form.berat_awal" />
                                    <p v-if="form.errors.berat_awal" class="text-xs text-destructive">{{ form.errors.berat_awal }}</p>
                                </div>
                                <div class="grid gap-1.5">
                                    <Label>Berat Akhir (gr)</Label>
                                    <Input type="number" step="0.001" v-model.number="form.berat_akhir" />
                                    <p v-if="form.errors.berat_akhir" class="text-xs text-destructive">{{ form.errors.berat_akhir }}</p>
                                </div>
                                <div class="grid gap-1.5">
                                    <Label>Density</Label>
                                    <Input type="number" step="0.01" v-model.number="form.density" />
                                    <p v-if="form.errors.density" class="text-xs text-destructive">{{ form.errors.density }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-emerald-50/40 dark:bg-zinc-900 border border-emerald-100 dark:border-zinc-800 space-y-4">
                            <span class="text-xs font-bold uppercase text-emerald-600 dark:text-zinc-400">Hasil Kalkulasi Otomatis (Live Preview)</span>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                                <div class="p-2 rounded bg-white dark:bg-zinc-800 border">
                                    <span class="text-xs text-muted-foreground block">Berat Hilang:</span>
                                    <strong class="font-mono text-base text-emerald-600">{{ hitungLab.berat_hilang }} gr</strong>
                                </div>
                                <div class="p-2 rounded bg-white dark:bg-zinc-800 border">
                                    <span class="text-xs text-muted-foreground block">Metode Biasa:</span>
                                    <strong class="font-mono text-base text-emerald-600">{{ hitungLab.metode_biasa }} %</strong>
                                </div>
                                <div class="p-2 rounded bg-white dark:bg-zinc-800 border">
                                    <span class="text-xs text-muted-foreground block">Volume:</span>
                                    <strong class="font-mono text-base text-emerald-600">{{ hitungLab.volume }}</strong>
                                </div>
                                <div class="p-2 rounded bg-white dark:bg-zinc-800 border">
                                    <span class="text-xs text-muted-foreground block">Ketebalan (dm):</span>
                                    <strong class="font-mono text-base text-emerald-600">{{ hitungLab.ketebalan_dm }} dm</strong>
                                </div>
                                <div class="p-2 rounded bg-white dark:bg-zinc-800 border">
                                    <span class="text-xs text-muted-foreground block">Luas Permukaan:</span>
                                    <strong class="font-mono text-base text-emerald-600">{{ hitungLab.luas_permukaan }}</strong>
                                </div>
                                <div class="p-2 rounded bg-white dark:bg-zinc-800 border">
                                    <span class="text-xs text-muted-foreground block">Hasil Akhir:</span>
                                    <strong class="font-mono text-lg text-primary block mt-0.5">{{ hitungLab.hasil_akhir }}</strong>
                                </div>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-semibold shadow-md">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan Data Sampel
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
