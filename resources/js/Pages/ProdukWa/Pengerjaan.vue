<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";
import { ref, onMounted, computed, watch } from "vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    waterabsorption: { id: number };
    produkwa: any;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    spesifikasis: Array<{ id: number; spesifikasi: string }>;
}>();

// Menginisialisasi form menggunakan data database awal
const form = useForm({
    temp: props.produkwa.temp ?? 0,
    palm_wo: parseFloat(props.produkwa.palm_wo) || 0,
    palm_wa: parseFloat(props.produkwa.palm_wa) || 0,
    mc_wo: parseFloat(props.produkwa.mc_wo) || 0,
    mc_wa: parseFloat(props.produkwa.mc_wa) || 0,
    sl_wo: parseFloat(props.produkwa.sl_wo) || 0,
    sl_wa: parseFloat(props.produkwa.sl_wa) || 0,
});

// Real-time Visual Calculation di Frontend
const palmWater = computed(() => parseFloat((form.palm_wa - form.palm_wo).toFixed(3)));
const mcWater = computed(() => parseFloat((form.mc_wa - form.mc_wo).toFixed(3)));
const slWater = computed(() => parseFloat((form.sl_wa - form.sl_wo).toFixed(3)));

// State Text untuk Detail Ringkas (Read-only)
const txtCust = ref("");
const txtModel = ref("");
const txtSpec = ref("");

const syncText = () => {
    txtCust.value = props.customers.find(c => c.id === props.produkwa.customer_id)?.customer || "-";
    txtModel.value = props.modelsizes.find(m => m.id === props.produkwa.modelsize_id)?.modelsize || "-";
    txtSpec.value = props.spesifikasis.find(s => s.id === props.produkwa.spesifikasi_id)?.spesifikasi || "-";
};

onMounted(() => syncText());

// KUNCI UTAMA: Memantau perubahan database model saat pindah sampel (Inertia Redirect)
watch(() => props.produkwa, (newP) => {
    // 1. Definisikan ulang nilai default form berdasarkan data fresh dari database (newP)
    form.defaults({
        temp: newP.temp ?? 0,
        palm_wo: parseFloat(newP.palm_wo) || 0,
        palm_wa: parseFloat(newP.palm_wa) || 0,
        mc_wo: parseFloat(newP.mc_wo) || 0,
        mc_wa: parseFloat(newP.mc_wa) || 0,
        sl_wo: parseFloat(newP.sl_wo) || 0,
        sl_wa: parseFloat(newP.sl_wa) || 0,
    });

    // 2. Reset form agar memaksa UI memuat data default baru di atas
    form.reset();

    // 3. Bersihkan sisa log error validasi dari sampel sebelumnya
    form.clearErrors();

    // 4. Perbarui data teks identitas produk
    syncText();
}, { deep: true });
</script>

<template>
    <Head title="Pengerjaan Lab Water Absorption" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('produkwa.index', props.waterabsorption.id)"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Input Cepat Nilai Lab</h2>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="border-b pb-4 mb-4">
                    <CardTitle class="text-zinc-500 text-sm font-medium">
                        ID Sampel: {{ props.produkwa.id }} | Nama Sampel: {{ props.produkwa.sampel }}
                    </CardTitle>
                </CardHeader>

                <CardContent class="p-6 space-y-6">
                    <!-- ================= SECTION 1: DATA READ-ONLY (3 KOLOM SEIMBANG) ================= -->
                    <div class="bg-zinc-50 dark:bg-zinc-900/40 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800">
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4">
                            Informasi Identitas & Master Data Produk
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- KOLOM 1: CUSTOMER & SAMPLE -->
                            <div class="space-y-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs font-medium text-muted-foreground">Customer</span>
                                    <span class="text-base font-semibold text-foreground">{{ txtCust }}</span>
                                </div>
                                <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                    <span class="text-xs font-medium text-muted-foreground">Nama Sampel</span>
                                    <span class="text-base font-semibold text-primary underline">{{ props.produkwa.sampel || '-' }}</span>
                                </div>
                            </div>

                            <!-- KOLOM 2: SPESIFIKASI TEKNIS -->
                            <div class="space-y-4 border-t pt-4 md:border-t-0 md:border-l md:pl-6 border-zinc-200 dark:border-zinc-800">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs font-medium text-muted-foreground">Model Size</span>
                                    <span class="text-base font-semibold text-foreground">{{ txtModel }}</span>
                                </div>
                                <div class="flex flex-col gap-1 border-t pt-3 border-zinc-200/60 dark:border-zinc-800/60">
                                    <span class="text-xs font-medium text-muted-foreground">Spesifikasi Target</span>
                                    <span class="text-base font-semibold text-blue-600 dark:text-blue-400">{{ txtSpec }}</span>
                                </div>
                            </div>

                            <!-- KOLOM 3: WAKTU PRODUKSI -->
                            <div class="space-y-4 border-t pt-4 md:border-t-0 md:border-l md:pl-6 border-zinc-200 dark:border-zinc-800">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs font-medium text-muted-foreground">Tanggal Produksi</span>
                                    <span class="text-base font-semibold text-foreground">
                                        {{ props.produkwa.tgl_produksi ? new Date(props.produkwa.tgl_produksi).toLocaleDateString("id-ID", { day: "2-digit", month: "long", year: "numeric" }) : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ================= SECTION 2: FORM INPUT UTAMA PENGUJIAN ================= -->
                    <form @submit.prevent="form.post(route('produkwa.pengerjaan.store', [props.waterabsorption.id, props.produkwa.id]))" class="space-y-6">

                        <!-- Input Suhu -->
                        <div class="w-full md:w-1/3 grid gap-2">
                            <Label for="temp" class="text-base font-medium">Suhu Temp (°C)</Label>
                            <Input type="number" id="temp" v-model="form.temp" class="h-12 text-lg focus-visible:ring-primary" placeholder="Suhu aktual..." />
                            <p v-if="form.errors.temp" class="text-xs text-destructive">{{ form.errors.temp }}</p>
                        </div>

                        <!-- Grid Parameter Matriks Lab -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Palm Matrix -->
                            <div class="p-4 rounded-xl border bg-zinc-50/50 dark:bg-zinc-900/50 space-y-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-zinc-400 block">PALM MATRICES</span>
                                <div class="grid gap-1.5"><Label>WO (gr)</Label><Input type="number" step="0.001" v-model="form.palm_wo" class="h-10" /></div>
                                <div class="grid gap-1.5"><Label>WA (gr)</Label><Input type="number" step="0.001" v-model="form.palm_wa" class="h-10" /></div>
                                <div class="pt-2 text-sm font-semibold flex justify-between border-t border-dashed">
                                    <span>Water Net:</span><span class="text-primary underline font-bold">{{ palmWater }}</span>
                                </div>
                            </div>

                            <!-- MC Matrix -->
                            <div class="p-4 rounded-xl border bg-blue-50/30 dark:bg-blue-950/10 border-blue-100 dark:border-blue-900 space-y-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-blue-500 block">MC MATRICES</span>
                                <div class="grid gap-1.5"><Label>WO (gr)</Label><Input type="number" step="0.001" v-model="form.mc_wo" class="h-10" /></div>
                                <div class="grid gap-1.5"><Label>WA (gr)</Label><Input type="number" step="0.001" v-model="form.mc_wa" class="h-10" /></div>
                                <div class="pt-2 text-sm font-semibold flex justify-between border-t border-dashed">
                                    <span>Water Net:</span><span class="text-blue-600 underline font-bold">{{ mcWater }}</span>
                                </div>
                            </div>

                            <!-- SL Matrix -->
                            <div class="p-4 rounded-xl border bg-amber-50/30 dark:bg-amber-950/10 border-amber-100 dark:border-amber-900 space-y-3">
                                <span class="text-xs font-bold uppercase tracking-wider text-amber-500 block">SL MATRICES</span>
                                <div class="grid gap-1.5"><Label>WO (gr)</Label><Input type="number" step="0.001" v-model="form.sl_wo" class="h-10" /></div>
                                <div class="grid gap-1.5"><Label>WA (gr)</Label><Input type="number" step="0.001" v-model="form.sl_wa" class="h-10" /></div>
                                <div class="pt-2 text-sm font-semibold flex justify-between border-t border-dashed">
                                    <span>Water Net:</span><span class="text-amber-600 underline font-bold">{{ slWater }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <Button type="submit" :disabled="form.processing" class="w-full h-12 text-base bg-emerald-600 hover:bg-emerald-500 text-white font-semibold shadow-md">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan & Lanjut Sampel Berikutnya
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
