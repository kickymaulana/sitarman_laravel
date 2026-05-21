<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2, IconTrash, IconDotsVertical } from "@tabler/icons-vue";
import { ref, computed, watch, onMounted } from "vue";
import { onClickOutside } from "@vueuse/core";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    density: { id: number };
    produkdensity: any;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    ovens: Array<{ id: number; oven: string }>;
    jamkeluarovens: Array<{ id: number; jam_keluar_oven: string }>;
}>();

const form = useForm({
    no: props.produkdensity?.no || 1,
    tgl_produksi: props.produkdensity?.tgl_produksi || "",
    customer_id: props.produkdensity?.customer_id || "",
    modelsize_id: props.produkdensity?.modelsize_id || "",
    oven_id: props.produkdensity?.oven_id || "",
    jam_keluar_oven_id: props.produkdensity?.jam_keluar_oven_id || "",
    ketebalan: parseFloat(props.produkdensity?.ketebalan) || 0,
    berat_awal: parseFloat(props.produkdensity?.berat_awal) || 0,
    berat_akhir: parseFloat(props.produkdensity?.berat_akhir) || 0,
    volume: parseFloat(props.produkdensity?.volume) || 0,
});

const calculatedDensity = computed(() => {
    const berat = form.berat_akhir;
    const vol = form.volume;
    if (!vol) return 0;
    return parseFloat((berat / vol).toFixed(2));
});

const searchCust = ref(""); const showCustDrop = ref(false); const custRef = ref(null);
const searchModel = ref(""); const showModelDrop = ref(false); const modelRef = ref(null);
const searchOven = ref(""); const showOvenDrop = ref(false); const ovenRef = ref(null);
const searchJamOven = ref(""); const showJamOvenDrop = ref(false); const jamOvenRef = ref(null);

onMounted(() => {
    if (form.customer_id) searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "";
    if (form.modelsize_id) searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "";
    if (form.oven_id) searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "";
    if (form.jam_keluar_oven_id) searchJamOven.value = props.jamkeluarovens.find(j => j.id === form.jam_keluar_oven_id)?.jam_keluar_oven || "";
});

onClickOutside(custRef, () => { showCustDrop.value = false; searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "" });
onClickOutside(modelRef, () => { showModelDrop.value = false; searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "" });
onClickOutside(ovenRef, () => { showOvenDrop.value = false; searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "" });
onClickOutside(jamOvenRef, () => { showJamOvenDrop.value = false; searchJamOven.value = props.jamkeluarovens.find(j => j.id === form.jam_keluar_oven_id)?.jam_keluar_oven || "" });

const filteredCusts = computed(() => props.customers.filter(c => c.customer.toLowerCase().includes(searchCust.value.toLowerCase())));
const filteredModels = computed(() => {
    const subset = form.customer_id ? props.modelsizes.filter(m => m.customer_id === form.customer_id) : props.modelsizes;
    return subset.filter(m => m.modelsize.toLowerCase().includes(searchModel.value.toLowerCase()));
});
const filteredOvens = computed(() => props.ovens.filter(o => o.oven.toLowerCase().includes(searchOven.value.toLowerCase())));
const filteredJamOvens = computed(() => props.jamkeluarovens.filter(j => j.jam_keluar_oven.toLowerCase().includes(searchJamOven.value.toLowerCase())));
</script>

<template>
    <Head title="Edit Item Density"/>
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('produkdensity.index', props.density.id)"><IconArrowLeft class="size-4"/></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Item Density</h2>
            </div>

            <!-- Delete Action -->
            <AlertDialog>
                <DropdownMenu>
                    <DropdownMenuTrigger as-child><Button variant="ghost" size="icon"><IconDotsVertical class="size-4"/></Button></DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <AlertDialogTrigger as-child><DropdownMenuItem class="text-destructive"><IconTrash class="mr-2 size-4"/>Hapus Item</DropdownMenuItem></AlertDialogTrigger>
                    </DropdownMenuContent>
                </DropdownMenu>
                <AlertDialogContent>
                    <AlertDialogHeader>
                        <AlertDialogTitle>Konfirmasi Hapus Data?</AlertDialogTitle>
                        <AlertDialogDescription>Data pengetesan density nomor urut <strong>{{ props.produkdensity.no }}</strong> akan dihapus permanen.</AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Batal</AlertDialogCancel>
                        <AlertDialogAction @click="router.delete(route('produkdensity.destroy', [props.density.id, props.produkdensity.id]))" class="bg-destructive text-white">Ya, Hapus</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardContent class="p-6">
                    <form @submit.prevent="form.put(route('produkdensity.update', [props.density.id, props.produkdensity.id]))" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2"><Label>No</Label><Input type="number" v-model="form.no"/></div>
                            <div class="grid gap-2"><Label>Tanggal Produksi</Label><Input type="date" v-model="form.tgl_produksi"/></div>
                        </div>

                        <!-- Dropdowns Area -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2 relative" ref="custRef">
                                <Label>Customer</Label>
                                <Input v-model="searchCust" @focus="showCustDrop = true"/>
                                <div v-if="showCustDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="c in filteredCusts" :key="c.id" @click="form.customer_id = c.id; searchCust = c.customer; showCustDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ c.customer }}</div>
                                </div>
                            </div>
                            <div class="grid gap-2 relative" ref="modelRef">
                                <Label>Model Size</Label>
                                <Input v-model="searchModel" @focus="showModelDrop = true"/>
                                <div v-if="showModelDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="m in filteredModels" :key="m.id" @click="form.modelsize_id = m.id; searchModel = m.modelsize; showModelDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ m.modelsize }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2 relative" ref="ovenRef">
                                <Label>Oven</Label>
                                <Input v-model="searchOven" @focus="showOvenDrop = true"/>
                                <div v-if="showOvenDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="o in filteredOvens" :key="o.id" @click="form.oven_id = o.id; searchOven = o.oven; showOvenDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ o.oven }}</div>
                                </div>
                            </div>
                            <div class="grid gap-2 relative" ref="jamOvenRef">
                                <Label>Jam Keluar Oven</Label>
                                <Input v-model="searchJamOven" @focus="showJamOvenDrop = true"/>
                                <div v-if="showJamOvenDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="j in filteredJamOvens" :key="j.id" @click="form.jam_keluar_oven_id = j.id; searchJamOven = j.jam_keluar_oven; showJamOvenDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ j.jam_keluar_oven }}</div>
                                </div>
                            </div>
                        </div>

                        <hr />

                        <!-- Parameters Input Area -->
                        <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900 border space-y-4">
                            <span class="text-xs font-bold uppercase text-zinc-400">Parameter Ukur & Input</span>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="grid gap-1.5"><Label>Ketebalan (mm)</Label><Input type="number" step="0.01" v-model="form.ketebalan"/></div>
                                <div class="grid gap-1.5"><Label>Berat Awal (g)</Label><Input type="number" step="0.01" v-model="form.berat_awal"/></div>
                                <div class="grid gap-1.5"><Label>Berat Akhir (g)</Label><Input type="number" step="0.01" v-model="form.berat_akhir"/></div>
                                <div class="grid gap-1.5"><Label>Volume ($cm^3$)</Label><Input type="number" step="0.01" v-model="form.volume"/></div>
                            </div>

                            <div class="pt-2 border-t text-base font-bold flex justify-between items-center">
                                <span>Hasil Nilai Density:</span>
                                <span class="text-xl text-blue-600 underline">{{ calculatedDensity }} g/$\text{cm}^3$</span>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full mt-4 shadow-md bg-primary hover:bg-primary/90">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin"/>
                            <IconDeviceFloppy v-else class="mr-2"/> Perbarui Data Density
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
