<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2, IconTrash, IconDotsVertical } from "@tabler/icons-vue";
import { ref, computed, watch, onMounted } from "vue";
import { onClickOutside } from "@vueuse/core";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    waterabsorption: { id: number };
    produkwa: any;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    spesifikasis: Array<{ id: number; spesifikasi: string }>;
}>();

const form = useForm({
    no: props.produkwa?.no || 1,
    tgl_produksi: props.produkwa?.tgl_produksi || "",
    customer_id: props.produkwa?.customer_id || "",
    modelsize_id: props.produkwa?.modelsize_id || "",
    spesifikasi_id: props.produkwa?.spesifikasi_id || "",
    sampel: props.produkwa?.sampel || "",
    temp: props.produkwa?.temp || 0,
    palm_wo: parseFloat(props.produkwa?.palm_wo) || 0,
    palm_wa: parseFloat(props.produkwa?.palm_wa) || 0,
    mc_wo: parseFloat(props.produkwa?.mc_wo) || 0,
    mc_wa: parseFloat(props.produkwa?.mc_wa) || 0,
    sl_wo: parseFloat(props.produkwa?.sl_wo) || 0,
    sl_wa: parseFloat(props.produkwa?.sl_wa) || 0,
});

const palmWater = computed(() => parseFloat((form.palm_wa - form.palm_wo).toFixed(3)));
const mcWater = computed(() => parseFloat((form.mc_wa - form.mc_wo).toFixed(3)));
const slWater = computed(() => parseFloat((form.sl_wa - form.sl_wo).toFixed(3)));

const searchCust = ref(""); const showCustDrop = ref(false); const custRef = ref(null);
const searchModel = ref(""); const showModelDrop = ref(false); const modelRef = ref(null);
const searchSpec = ref(""); const showSpecDrop = ref(false); const specRef = ref(null);

onMounted(() => {
    if (form.customer_id) searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "";
    if (form.modelsize_id) searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "";
    if (form.spesifikasi_id) searchSpec.value = props.spesifikasis.find(s => s.id === form.spesifikasi_id)?.spesifikasi || "";
});

onClickOutside(custRef, () => { showCustDrop.value = false; searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "" });
onClickOutside(modelRef, () => { showModelDrop.value = false; searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "" });
onClickOutside(specRef, () => { showSpecDrop.value = false; searchSpec.value = props.spesifikasis.find(s => s.id === form.spesifikasi_id)?.spesifikasi || "" });

const filteredCusts = computed(() => props.customers.filter(c => c.customer.toLowerCase().includes(searchCust.value.toLowerCase())));
const filteredModels = computed(() => {
    const subset = form.customer_id ? props.modelsizes.filter(m => m.customer_id === form.customer_id) : props.modelsizes;
    return subset.filter(m => m.modelsize.toLowerCase().includes(searchModel.value.toLowerCase()));
});
const filteredSpecs = computed(() => props.spesifikasis.filter(s => s.spesifikasi.toLowerCase().includes(searchSpec.value.toLowerCase())));
</script>

<template>
    <Head title="Edit Item Water Absorption" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('produkwa.index', props.waterabsorption.id)"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Item Pengujian</h2>
            </div>

            <!-- Danger Zone Management -->
            <AlertDialog>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child><Button variant="ghost" size="icon"><IconDotsVertical class="size-4" /></Button></DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <AlertDialogTrigger as-child><DropdownMenuItem class="text-destructive"><IconTrash class="mr-2 size-4" />Hapus Item</DropdownMenuItem></AlertDialogTrigger>
                    </DropdownMenuContent>
                </DropdownMenu>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Konfirmasi Hapus Data?</AlertDialogTitle>
                        <AlertDialogDescription>Data pengetesan sampel <strong>{{ props.produkwa.sampel }}</strong> akan dihapus permanen.</AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Batal</AlertDialogCancel>
                        <AlertDialogAction @click="router.delete(route('produkwa.destroy', [props.waterabsorption.id, props.produkwa.id]))" class="bg-destructive text-white">Ya, Hapus</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardContent class="p-6">
                    <form @submit.prevent="form.put(route('produkwa.update', [props.waterabsorption.id, props.produkwa.id]))" class="space-y-6">
                        <!-- Identity & Parameter Row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2"><Label>Tanggal Produksi</Label><Input type="date" v-model="form.tgl_produksi" /></div>
                            <div class="grid gap-2"><Label>Sampel Name</Label><Input v-model="form.sampel" /></div>
                            <div class="grid gap-2"><Label>Temp (°C)</Label><Input type="number" v-model="form.temp" /></div>
                        </div>

                        <!-- Dropdowns Area -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2 relative" ref="custRef">
                                <Label>Customer</Label>
                                <Input v-model="searchCust" @focus="showCustDrop = true" />
                                <div v-if="showCustDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="c in filteredCusts" :key="c.id" @click="form.customer_id = c.id; searchCust = c.customer; showCustDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ c.customer }}</div>
                                </div>
                            </div>
                            <div class="grid gap-2 relative" ref="modelRef">
                                <Label>Model Size</Label>
                                <Input v-model="searchModel" @focus="showModelDrop = true" />
                                <div v-if="showModelDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="m in filteredModels" :key="m.id" @click="form.modelsize_id = m.id; searchModel = m.modelsize; showModelDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ m.modelsize }}</div>
                                </div>
                            </div>
                            <div class="grid gap-2 relative" ref="specRef">
                                <Label>Spesifikasi</Label>
                                <Input v-model="searchSpec" @focus="showSpecDrop = true" />
                                <div v-if="showSpecDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="s in filteredSpecs" :key="s.id" @click="form.spesifikasi_id = s.id; searchSpec = s.spesifikasi; showSpecDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ s.spesifikasi }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2">
                                <Label>No</Label>
                                <Input type="number" v-model="form.no" />
                            </div>
                        </div>

                        <hr />

                        <!-- Dynamic Calculation Section -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Palm -->
                            <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900 border space-y-3">
                                <span class="text-xs font-bold uppercase text-zinc-400">Parameter Palm</span>
                                <div class="grid gap-1.5"><Label>Palm WO</Label><Input type="number" step="0.001" v-model="form.palm_wo" /></div>
                                <div class="grid gap-1.5"><Label>Palm WA</Label><Input type="number" step="0.001" v-model="form.palm_wa" /></div>
                                <div class="pt-2 text-sm font-semibold flex justify-between"><span>Palm Water:</span><span class="text-primary underline">{{ palmWater }}</span></div>
                            </div>
                            <!-- MC -->
                            <div class="p-4 rounded-xl bg-blue-50/40 dark:bg-blue-950/10 border border-blue-100 dark:border-blue-900 space-y-3">
                                <span class="text-xs font-bold uppercase text-blue-500">Parameter MC</span>
                                <div class="grid gap-1.5"><Label>MC WO</Label><Input type="number" step="0.001" v-model="form.mc_wo" /></div>
                                <div class="grid gap-1.5"><Label>MC WA</Label><Input type="number" step="0.001" v-model="form.mc_wa" /></div>
                                <div class="pt-2 text-sm font-semibold flex justify-between"><span>MC Water:</span><span class="text-blue-600 underline">{{ mcWater }}</span></div>
                            </div>
                            <!-- SL -->
                            <div class="p-4 rounded-xl bg-amber-50/40 dark:bg-amber-950/10 border border-amber-100 dark:border-amber-900 space-y-3">
                                <span class="text-xs font-bold uppercase text-amber-500">Parameter SL</span>
                                <div class="grid gap-1.5"><Label>SL WO</Label><Input type="number" step="0.001" v-model="form.sl_wo" /></div>
                                <div class="grid gap-1.5"><Label>SL WA</Label><Input type="number" step="0.001" v-model="form.sl_wa" /></div>
                                <div class="pt-2 text-sm font-semibold flex justify-between"><span>SL Water:</span><span class="text-amber-600 underline">{{ slWater }}</span></div>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full mt-4 shadow-md bg-primary hover:bg-primary/90">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Perbarui Data Pengujian
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
