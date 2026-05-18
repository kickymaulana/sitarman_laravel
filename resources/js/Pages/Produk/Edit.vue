<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { IconArrowLeft, IconDeviceFloppy, IconDotsVertical, IconTrash, IconLoader2 } from "@tabler/icons-vue";
import { ref, computed, onMounted } from "vue";
import { onClickOutside } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: { id: number };
    produk: any;
    ovens: Array<{ id: number; oven: string }>;
    customers: Array<{ id: number; customer: string }>;
    modelsizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    spesifikasis: Array<{ id: number; spesifikasi: string }>;
}>();

const form = useForm({
    kode_tanah: props.produk.kode_tanah,
    oven_id: props.produk.oven_id,
    customer_id: props.produk.customer_id,
    modelsize_id: props.produk.modelsize_id,
    spesifikasi_id: props.produk.spesifikasi_id,
    sampel: props.produk.sampel,
    berat_former: props.produk.berat_former,
    tanggal_keluar_oven: props.produk.tanggal_keluar_oven,
    tgl_produksi: props.produk.tgl_produksi,
    posisi_former: props.produk.posisi_former,
    hasil_test: props.produk.hasil_test,
    suhu_actual: props.produk.suhu_actual,
    keterangan: props.produk.keterangan
});

const searchOven = ref(""); const showOvenDrop = ref(false); const ovenRef = ref(null);
const searchCust = ref(""); const showCustDrop = ref(false); const custRef = ref(null);
const searchModel = ref(""); const showModelDrop = ref(false); const modelRef = ref(null);
const searchSpec = ref(""); const showSpecDrop = ref(false); const specRef = ref(null);

onClickOutside(ovenRef, () => { showOvenDrop.value = false; searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "" });
onClickOutside(custRef, () => { showCustDrop.value = false; searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "" });
onClickOutside(modelRef, () => { showModelDrop.value = false; searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "" });
onClickOutside(specRef, () => { showSpecDrop.value = false; searchSpec.value = props.spesifikasis.find(s => s.id === form.spesifikasi_id)?.spesifikasi || "" });

const filteredOvens = computed(() => props.ovens.filter(o => o.oven.toLowerCase().includes(searchOven.value.toLowerCase())));
const filteredCusts = computed(() => props.customers.filter(c => c.customer.toLowerCase().includes(searchCust.value.toLowerCase())));
const filteredModels = computed(() => props.modelsizes.filter(m => m.customer_id === form.customer_id).filter(m => m.modelsize.toLowerCase().includes(searchModel.value.toLowerCase())));
const filteredSpecs = computed(() => props.spesifikasis.filter(s => s.spesifikasi.toLowerCase().includes(searchSpec.value.toLowerCase())));

onMounted(() => {
    searchOven.value = props.ovens.find(o => o.id === form.oven_id)?.oven || "";
    searchCust.value = props.customers.find(c => c.id === form.customer_id)?.customer || "";
    searchModel.value = props.modelsizes.find(m => m.id === form.modelsize_id)?.modelsize || "";
    searchSpec.value = props.spesifikasis.find(s => s.id === form.spesifikasi_id)?.spesifikasi || "";
});
</script>

<template>
    <Head title="Edit Item Produk" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('produk.index', props.thermalshock.id)"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Item Produk</h2>
            </div>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="flex flex-row items-center justify-between border-b pb-4 mb-4">
                    <CardTitle class="text-primary text-lg">Update Data ID Produk: {{ props.produk.id }}</CardTitle>
                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon"><IconDotsVertical class="size-4" /></Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <AlertDialogTrigger as-child>
                                    <DropdownMenuItem class="text-destructive cursor-pointer"><IconTrash class="mr-2 size-4" />Hapus</DropdownMenuItem>
                                </AlertDialogTrigger>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>Hapus Data Produk?</AlertDialogTitle>
                                <AlertDialogDescription>Hapus log item produk kode tanah {{ props.produk.kode_tanah }} secara permanen?</AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction @click="router.delete(route('produk.destroy', [props.thermalshock.id, props.produk.id]))" class="bg-destructive text-white hover:bg-destructive/90">Ya, Hapus</AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="form.put(route('produk.update', [props.thermalshock.id, props.produk.id]))" class="space-y-6">
                        <!-- Gunakan Layout Grid Form yang sama seperti di Create.vue -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="grid gap-2">
                                <Label for="kode_tanah">Kode Tanah</Label>
                                <Input id="kode_tanah" v-model="form.kode_tanah" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="sampel">Sampel</Label>
                                <Input id="sampel" v-model="form.sampel" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="hasil_test">Hasil Test</Label>
                                <select id="hasil_test" v-model="form.hasil_test" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm shadow-sm">
                                    <option value="OK">OK</option>
                                    <option value="NG">NG</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2 relative" ref="ovenRef">
                                <Label>Oven</Label>
                                <Input v-model="searchOven" @focus="showOvenDrop = true" />
                                <div v-if="showOvenDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="o in filteredOvens" :key="o.id" @click="form.oven_id = o.id; searchOven = o.oven; showOvenDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ o.oven }}</div>
                                </div>
                            </div>
                            <div class="grid gap-2 relative" ref="custRef">
                                <Label>Customer</Label>
                                <Input v-model="searchCust" @focus="showCustDrop = true" />
                                <div v-if="showCustDrop" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white p-1 dark:bg-zinc-900 shadow-md">
                                    <div v-for="c in filteredCusts" :key="c.id" @click="form.customer_id = c.id; searchCust = c.customer; showCustDrop = false" class="cursor-pointer rounded p-2 text-sm hover:bg-accent">{{ c.customer }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="grid gap-2">
                                <Label for="berat_former">Berat Former</Label>
                                <Input type="number" id="berat_former" v-model="form.berat_former" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="posisi_former">Posisi Former</Label>
                                <Input type="number" id="posisi_former" v-model="form.posisi_former" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="suhu_actual">Suhu Actual</Label>
                                <Input type="number" id="suhu_actual" v-model="form.suhu_actual" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="tgl_produksi">Tanggal Produksi</Label>
                                <Input type="date" id="tgl_produksi" v-model="form.tgl_produksi" />
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="tanggal_keluar_oven">Tanggal Keluar Oven</Label>
                                <Input type="date" id="tanggal_keluar_oven" v-model="form.tanggal_keluar_oven" />
                            </div>
                            <div class="grid gap-2">
                                <Label for="keterangan">Keterangan</Label>
                                <Input id="keterangan" v-model="form.keterangan" />
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full shadow-md">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan Perubahan
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
