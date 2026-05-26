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
    chemical: { id: number };
    produkchemical: any;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    ovens: Array<{ id: number; oven: string }>;
    jamkeluarovens: Array<{ id: number; jam_keluar_oven: string }>;
}>();

const form = useForm({
    tgl_produksi: props.produkchemical?.tgl_produksi || "",
    customer_id: props.produkchemical?.customer_id || "",
    modelsize_id: props.produkchemical?.modelsize_id || "",
    oven_id: props.produkchemical?.oven_id || "",
    tanggal_keluar_oven: props.produkchemical?.tanggal_keluar_oven || "",
    jam_keluar_oven_id: props.produkchemical?.jam_keluar_oven_id || "",
    sample: props.produkchemical?.sample || "-",
    ketebalan: parseFloat(props.produkchemical?.ketebalan) || 0,
    berat_awal: parseFloat(props.produkchemical?.berat_awal) || 0,
    berat_akhir: parseFloat(props.produkchemical?.berat_akhir) || 0,
});

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
</script>

<template>
    <Head title="Edit Item Chemical" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('produkchemical.index', props.chemical.id)"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Item Chemical</h2>
            </div>

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
                        <AlertDialogDescription>Data pengetesan item ID: <strong>{{ props.produkchemical.id }}</strong> akan dihapus permanen.</AlertDialogDescription>
                    </AlertDialogHeader>
                    <AlertDialogFooter>
                        <AlertDialogCancel>Batal</AlertDialogCancel>
                        <AlertDialogAction @click="router.delete(route('produkchemical.destroy', [props.chemical.id, props.produkchemical.id]))" class="bg-destructive text-white">Ya, Hapus</AlertDialogAction>
                    </AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardContent class="p-6">
                    <form @submit.prevent="form.put(route('produkchemical.update', [props.chemical.id, props.produkchemical.id]))" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2"><Label>Tanggal Produksi</Label><Input type="date" v-model="form.tgl_produksi" /></div>
                            <div class="grid gap-2 relative" ref="custRef">
                                <Label>Customer</Label>
                                <Input v-model="searchCust" @focus="showCustDrop = true" />
                                <div v-if="showCustDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="c in filteredCusts" :key="c.id" @click="form.customer_id = c.id; searchCust = c.customer; showCustDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ c.customer }}</div>
                                </div>
                            </div>
                            <div class="grid gap-2 relative" ref="modelRef">
                                <Label>Model Size</Label>
                                <Input v-model="searchModel" @focus="showModelDrop = true" :disabled="!form.customer_id" />
                                <div v-if="showModelDrop && form.customer_id" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="m in filteredModels" :key="m.id" @click="form.modelsize_id = m.id; searchModel = m.modelsize; showModelDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ m.modelsize }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="grid gap-2 relative" ref="ovenRef">
                                <Label>Oven</Label>
                                <Input v-model="searchOven" @focus="showOvenDrop = true" />
                                <div v-if="showOvenDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="o in filteredOvens" :key="o.id" @click="form.oven_id = o.id; searchOven = o.oven; showOvenDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ o.oven }}</div>
                                </div>
                            </div>
                            <div class="grid gap-2"><Label>Tanggal Keluar Oven</Label><Input type="date" v-model="form.tanggal_keluar_oven" /></div>
                            <div class="grid gap-2 relative" ref="jamRef">
                                <Label>Jam Keluar Oven</Label>
                                <Input v-model="searchJam" @focus="showJamDrop = true" />
                                <div v-if="showJamDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 shadow-md dark:bg-zinc-900">
                                    <div v-for="j in filteredJams" :key="j.id" @click="form.jam_keluar_oven_id = j.id; searchJam = j.jam_keluar_oven.substring(0, 5); showJamDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ j.jam_keluar_oven.substring(0, 5) }} WIB</div>
                                </div>
                            </div>
                            <div class="grid gap-2"><Label>Sample</Label><Input type="text" v-model="form.sample" /></div>
                        </div>

                        <div class="p-4 rounded-xl bg-purple-50/40 dark:bg-zinc-900 border border-purple-100 dark:border-zinc-800 space-y-4">
                            <span class="text-xs font-bold uppercase text-purple-600 dark:text-zinc-400">Parameter Fisik Lab</span>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="grid gap-1.5"><Label>Ketebalan (mm)</Label><Input type="number" step="0.01" v-model.number="form.ketebalan" /></div>
                                <div class="grid gap-1.5"><Label>Berat Awal (gr)</Label><Input type="number" step="0.001" v-model.number="form.berat_awal" /></div>
                                <div class="grid gap-1.5"><Label>Berat Akhir (gr)</Label><Input type="number" step="0.001" v-model.number="form.berat_akhir" /></div>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-purple-600 hover:bg-purple-500 text-white font-semibold shadow-md">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Perbarui Data Sampel
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
