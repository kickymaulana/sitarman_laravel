<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { IconArrowLeft, IconDeviceFloppy, IconDotsVertical, IconTrash, IconLoader2, IconFlame, IconHammer } from "@tabler/icons-vue";
import { ref, computed, onMounted } from "vue";
import { onClickOutside } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: any;
    thermalPintus: Array<{ id: number; thermal_pintu: string }>;
    ovens: Array<{ id: number; oven: string }>;
    customers: Array<{ id: number; customer: string; model: string; spesifikasi: string; size: string }>;
    tinggiFormers: Array<{ id: number; tinggi_former: number }>;
    jamKeluarOvens: Array<{ id: number; jam_keluar_oven: string }>;
}>();

const form = useForm({
    // Metadata Utama
    thermal_pintu_id: props.thermalshock.thermal_pintu_id,
    hari_tgl: props.thermalshock.hari_tgl,

    // Parameter Pengujian 180°C
    suhu_awal_180: props.thermalshock.suhu_awal_180 ?? 0,
    suhu_display_180: props.thermalshock.suhu_display_180 ?? 0,
    suhu_actual_180: props.thermalshock.suhu_actual_180 ?? 0,
    suhu_air_180: props.thermalshock.suhu_air_180 ?? "-",
    jam_awal_proses_180: props.thermalshock.jam_awal_proses_180 ?? "",
    jam_capai_suhu_180: props.thermalshock.jam_capai_suhu_180 ?? "",
    jam_mulai_tembak_180: props.thermalshock.jam_mulai_tembak_180 ?? "",
    jam_selesai_tembak_180: props.thermalshock.jam_selesai_tembak_180 ?? "",

    // Parameter Pengujian 200°C
    suhu_awal_200: props.thermalshock.suhu_awal_200 ?? 0,
    suhu_display_200: props.thermalshock.suhu_display_200 ?? 0,
    suhu_actual_200: props.thermalshock.suhu_actual_200 ?? 0,
    suhu_air_200: props.thermalshock.suhu_air_200 ?? "-",
    jam_awal_proses_200: props.thermalshock.jam_awal_proses_200 ?? "",
    jam_capai_suhu_200: props.thermalshock.jam_capai_suhu_200 ?? "",
    jam_mulai_tembak_200: props.thermalshock.jam_mulai_tembak_200 ?? "",
    jam_selesai_tembak_200: props.thermalshock.jam_selesai_tembak_200 ?? "",

    // Data Manufaktur
    kode_bakar: props.thermalshock.kode_bakar,
    kode_tanah: props.thermalshock.kode_tanah,
    oven_id: props.thermalshock.oven_id,
    customer_id: props.thermalshock.customer_id,
    tinggi_former_id: props.thermalshock.tinggi_former_id,
    jam_keluar_oven_id: props.thermalshock.jam_keluar_oven_id,
    sampel: props.thermalshock.sampel,
    berat_former: props.thermalshock.berat_former,
    tanggal_keluar_oven: props.thermalshock.tanggal_keluar_oven,
    tgl_produksi: props.thermalshock.tgl_produksi,
    posisi_former: props.thermalshock.posisi_former,
    hasil_test_180: props.thermalshock.hasil_test_180 ?? "Belum Tes",
    hasil_test_200: props.thermalshock.hasil_test_200 ?? "Belum Tes",
    keterangan: props.thermalshock.keterangan
});

const selectedCustomer = computed(() => {
    return props.customers.find(item => item.id === form.customer_id) || null;
});

const useDropdown = (propsList: any[], keyName: string, formField: string, isCustomer: boolean = false) => {
    const search = ref("");
    const show = ref(false);
    const elementRef = ref(null);

    onClickOutside(elementRef, () => {
        show.value = false;
        // @ts-ignore
        if (!form[formField]) {
            search.value = "";
        } else {
            const selected = propsList.find(item => item.id === form[formField]);
            if (selected) search.value = String(selected[keyName]);
        }
    });

    const filtered = computed(() => {
        if (!search.value) return propsList;
        const query = search.value.toLowerCase();

        if (isCustomer) {
            return propsList.filter(item =>
                String(item.customer).toLowerCase().includes(query) ||
                String(item.model).toLowerCase().includes(query) ||
                String(item.size).toLowerCase().includes(query) ||
                String(item.spesifikasi).toLowerCase().includes(query)
            );
        }

        return propsList.filter(item =>
            String(item[keyName]).toLowerCase().includes(query)
        );
    });

    const select = (item: any) => {
        // @ts-ignore
        form[formField] = item.id;
        search.value = String(item[keyName]);
        show.value = false;
    };

    return { search, show, elementRef, filtered, select };
};

const tPintu = useDropdown(props.thermalPintus, 'thermal_pintu', 'thermal_pintu_id');
const prodOven = useDropdown(props.ovens, 'oven', 'oven_id');
const cust = useDropdown(props.customers, 'customer', 'customer_id', true);
const tFormer = useDropdown(props.tinggiFormers, 'tinggi_former', 'tinggi_former_id');
const jKeluar = useDropdown(props.jamKeluarOvens, 'jam_keluar_oven', 'jam_keluar_oven_id');

const formatTimeInput = (field: keyof typeof form, event: Event) => {
    const target = event.target as HTMLInputElement;
    let val = target.value.replace(/\D/g, '');
    if (val.length > 4) val = val.substring(0, 4);

    if (val.length > 2) {
        let hours = val.substring(0, 2);
        if (parseInt(hours) > 23) hours = '23';
        let minutes = val.substring(2);
        if (parseInt(minutes) > 59) minutes = '59';
        val = hours + ':' + minutes;
    } else if (val.length === 2 && parseInt(val) > 23) {
        val = '23';
    }
    // @ts-ignore
    form[field] = val;
};

onMounted(() => {
    const tp = props.thermalPintus.find(p => p.id === form.thermal_pintu_id);
    if (tp) tPintu.search.value = tp.thermal_pintu;

    const po = props.ovens.find(o => o.id === form.oven_id);
    if (po) prodOven.search.value = po.oven;

    const c = props.customers.find(item => item.id === form.customer_id);
    if (c) cust.search.value = c.customer;

    const tf = props.tinggiFormers.find(item => item.id === form.tinggi_former_id);
    if (tf) tFormer.search.value = String(tf.tinggi_former);

    const jk = props.jamKeluarOvens.find(item => item.id === form.jam_keluar_oven_id);
    if (jk) jKeluar.search.value = jk.jam_keluar_oven.substring(0, 5);

    // Truncate format detik dari backend agar menjadi HH:mm di frontend input
    const timeFields = [
        'jam_awal_proses_180', 'jam_capai_suhu_180', 'jam_mulai_tembak_180', 'jam_selesai_tembak_180',
        'jam_awal_proses_200', 'jam_capai_suhu_200', 'jam_mulai_tembak_200', 'jam_selesai_tembak_200'
    ];
    timeFields.forEach(field => {
        // @ts-ignore
        if (form[field]) form[field] = form[field].substring(0, 5);
    });
});
</script>

<template>
    <Head title="Edit Thermal Shock" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full">
                    <Link :href="route('thermalshock.index')"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Record</h2>
            </div>
        </div>

        <form @submit.prevent="form.put(route('thermalshock.update', props.thermalshock.id))" class="space-y-6 max-w-5xl">

            <Card class="border-none shadow-md">
                <CardHeader class="flex flex-row items-center justify-between border-b bg-zinc-50/50 dark:bg-zinc-900/50 pt-4 pb-4">
                    <CardTitle class="text-primary flex items-center gap-2 text-lg font-semibold">
                        <IconFlame class="size-5 text-orange-500" /> 1. Parameter Utama Thermal Shock (ID: {{ props.thermalshock.id }})
                    </CardTitle>

                    <AlertDialog>
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="ghost" size="icon"><IconDotsVertical class="size-4" /></Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <AlertDialogTrigger as-child>
                                    <DropdownMenuItem class="text-destructive cursor-pointer"><IconTrash class="mr-2 size-4" />Hapus Record</DropdownMenuItem>
                                </AlertDialogTrigger>
                            </DropdownMenuContent>
                        </DropdownMenu>
                        <AlertDialogContent class="bg-white dark:bg-zinc-900">
                            <AlertDialogHeader>
                                <AlertDialogTitle>Hapus Data Permanen?</AlertDialogTitle>
                                <AlertDialogDescription>Tindakan ini tidak bisa dibatalkan. Log gabungan tanggal {{ props.thermalshock.hari_tgl }} akan terhapus selamanya dari sistem.</AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction @click="router.delete(route('thermalshock.destroy', props.thermalshock.id))" class="bg-destructive text-white hover:bg-destructive/90">Ya, Hapus</AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent class="pt-6 space-y-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-zinc-50 dark:bg-zinc-900/40 p-4 rounded-lg border border-zinc-100 dark:border-zinc-800">
                        <div class="grid gap-2">
                            <Label class="text-xs font-medium uppercase tracking-wider text-zinc-500" for="hari_tgl">Hari / Tanggal <span class="text-destructive">*</span></Label>
                            <Input type="date" id="hari_tgl" v-model="form.hari_tgl" class="h-9" />
                        </div>
                        <div class="grid gap-2 relative md:col-span-2" ref="tPintu.elementRef">
                            <Label class="text-xs font-medium uppercase tracking-wider text-zinc-500">Thermal Pintu <span class="text-destructive">*</span></Label>
                            <Input v-model="tPintu.search.value" @focus="tPintu.show.value = true" placeholder="Pilih Thermal Pintu..." class="h-9" />
                            <div v-if="tPintu.show.value" class="absolute z-50 mt-16 max-h-48 w-full overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-lg p-1">
                                <div v-for="p in tPintu.filtered.value" :key="p.id" @click="tPintu.select(p)" class="cursor-pointer rounded px-2 py-1.5 text-sm hover:bg-muted">{{ p.thermal_pintu }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        <div class="space-y-4 rounded-xl border border-blue-100 bg-blue-50/10 p-4 dark:border-blue-950 dark:bg-blue-950/10">
                            <div class="flex items-center gap-2 border-b border-blue-100 pb-2 dark:border-blue-900">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-500 text-xs font-bold text-white">180</span>
                                <h3 class="font-semibold text-blue-600 dark:text-blue-400 text-sm uppercase tracking-wider">Pengujian Suhu 180°C</h3>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500" for="suhu_awal_180">Suhu Awal</Label>
                                    <Input type="number" id="suhu_awal_180" v-model="form.suhu_awal_180" class="h-9" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500" for="suhu_display_180">Suhu Display</Label>
                                    <Input type="number" id="suhu_display_180" v-model="form.suhu_display_180" class="h-9" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500" for="suhu_actual_180">Suhu Actual</Label>
                                    <Input type="number" id="suhu_actual_180" v-model="form.suhu_actual_180" class="h-9" />
                                </div>
                            </div>
                            <div class="grid gap-1.5">
                                <Label class="text-xs text-zinc-500" for="suhu_air_180">Suhu Air</Label>
                                <Input id="suhu_air_180" v-model="form.suhu_air_180" class="h-9" />
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500">Jam Awal Proses</Label>
                                    <Input type="text" v-model="form.jam_awal_proses_180" @input="formatTimeInput('jam_awal_proses_180', $event)" placeholder="00:00" class="h-9" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500">Jam Capai Suhu</Label>
                                    <Input type="text" v-model="form.jam_capai_suhu_180" @input="formatTimeInput('jam_capai_suhu_180', $event)" placeholder="00:00" class="h-9" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500">Jam Mulai Tembak</Label>
                                    <Input type="text" v-model="form.jam_mulai_tembak_180" @input="formatTimeInput('jam_mulai_tembak_180', $event)" placeholder="00:00" class="h-9" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500">Jam Selesai Tembak</Label>
                                    <Input type="text" v-model="form.jam_selesai_tembak_180" @input="formatTimeInput('jam_selesai_tembak_180', $event)" placeholder="00:00" class="h-9" />
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 rounded-xl border border-amber-100 bg-amber-50/10 p-4 dark:border-amber-950 dark:bg-amber-950/10">
                            <div class="flex items-center gap-2 border-b border-amber-100 pb-2 dark:border-amber-900">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-500 text-xs font-bold text-white">200</span>
                                <h3 class="font-semibold text-amber-600 dark:text-amber-400 text-sm uppercase tracking-wider">Pengujian Suhu 200°C</h3>
                            </div>
                            <div class="grid grid-cols-3 gap-3">
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500" for="suhu_awal_200">Suhu Awal</Label>
                                    <Input type="number" id="suhu_awal_200" v-model="form.suhu_awal_200" class="h-9" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500" for="suhu_display_200">Suhu Display</Label>
                                    <Input type="number" id="suhu_display_200" v-model="form.suhu_display_200" class="h-9" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500" for="suhu_actual_200">Suhu Actual</Label>
                                    <Input type="number" id="suhu_actual_200" v-model="form.suhu_actual_200" class="h-9" />
                                </div>
                            </div>
                            <div class="grid gap-1.5">
                                <Label class="text-xs text-zinc-500" for="suhu_air_200">Suhu Air</Label>
                                <Input id="suhu_air_200" v-model="form.suhu_air_200" class="h-9" />
                            </div>
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500">Jam Awal Proses</Label>
                                    <Input type="text" v-model="form.jam_awal_proses_200" @input="formatTimeInput('jam_awal_proses_200', $event)" placeholder="00:00" class="h-9" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500">Jam Capai Suhu</Label>
                                    <Input type="text" v-model="form.jam_capai_suhu_200" @input="formatTimeInput('jam_capai_suhu_200', $event)" placeholder="00:00" class="h-9" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500">Jam Mulai Tembak</Label>
                                    <Input type="text" v-model="form.jam_mulai_tembak_200" @input="formatTimeInput('jam_mulai_tembak_200', $event)" placeholder="00:00" class="h-9" />
                                </div>
                                <div class="grid gap-1.5">
                                    <Label class="text-xs text-zinc-500">Jam Selesai Tembak</Label>
                                    <Input type="text" v-model="form.jam_selesai_tembak_200" @input="formatTimeInput('jam_selesai_tembak_200', $event)" placeholder="00:00" class="h-9" />
                                </div>
                            </div>
                        </div>

                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-md">
                <CardHeader class="border-b bg-zinc-50/50 dark:bg-zinc-900/50">
                    <CardTitle class="text-emerald-600 flex items-center gap-2 text-lg font-semibold">
                        <IconHammer class="size-5" /> 2. Data Manufaktur Produk
                    </CardTitle>
                </CardHeader>
                <CardContent class="pt-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2"><Label>Kode Bakar</Label><Input type="number" v-model="form.kode_bakar" /></div>
                        <div class="grid gap-2"><Label>Kode Tanah</Label><Input v-model="form.kode_tanah" /></div>
                        <div class="grid gap-2"><Label>Sampel</Label><Input v-model="form.sampel" /></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="grid gap-2 relative" ref="prodOven.elementRef">
                            <Label>Oven Produksi <span class="text-destructive">*</span></Label>
                            <Input v-model="prodOven.search.value" @focus="prodOven.show.value = true" placeholder="Pilih Oven..." />
                            <div v-if="prodOven.show.value" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-md p-1">
                                <div v-for="item in prodOven.filtered.value" :key="item.id" @click="prodOven.select(item)" class="cursor-pointer rounded px-2 py-1.5 text-sm hover:bg-muted">{{ item.oven }}</div>
                            </div>
                        </div>

                        <div class="grid gap-2 relative" ref="cust.elementRef">
                            <Label>Customer <span class="text-destructive">*</span></Label>
                            <Input v-model="cust.search.value" @focus="cust.show.value = true" placeholder="Ketik Customer/Model/Size/Spek..." />
                            <div v-if="cust.show.value" class="absolute z-50 mt-20 max-h-60 w-[320px] md:w-[450px] overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-xl p-1 flex flex-col gap-0.5">
                                <div v-if="cust.filtered.value.length === 0" class="text-center text-xs text-muted-foreground py-4 italic">Data produk tidak ditemukan</div>
                                <div
                                    v-else
                                    v-for="item in cust.filtered.value"
                                    :key="item.id"
                                    @click="cust.select(item)"
                                    class="cursor-pointer rounded p-2 text-sm hover:bg-muted border-b border-zinc-100 dark:border-zinc-800 last:border-none flex flex-col gap-0.5"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-primary">{{ item.customer }}</span>
                                        <span class="text-[11px] px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 font-medium text-zinc-600 dark:text-zinc-400">Size: {{ item.size }}</span>
                                    </div>
                                    <div class="text-xs text-muted-foreground flex justify-between items-center pt-0.5">
                                        <span>Model: <b class="text-foreground font-medium">{{ item.model }}</b></span>
                                        <span>Spek: <b class="text-foreground font-medium">{{ item.spesifikasi }}</b></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label>Model & Size</Label>
                            <Input :value="selectedCustomer ? `${selectedCustomer.model} / ${selectedCustomer.size}` : '-'" disabled class="bg-muted font-medium" />
                        </div>

                        <div class="grid gap-2">
                            <Label>Spesifikasi</Label>
                            <Input :value="selectedCustomer ? selectedCustomer.spesifikasi : '-'" disabled class="bg-muted font-medium" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2 relative" ref="tFormer.elementRef">
                            <Label>Tinggi Former <span class="text-destructive">*</span></Label>
                            <Input v-model="tFormer.search.value" @focus="tFormer.show.value = true" placeholder="Pilih Tinggi Former..." />
                            <div v-if="tFormer.show.value" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-md p-1">
                                <div v-for="item in tFormer.filtered.value" :key="item.id" @click="tFormer.select(item)" class="cursor-pointer rounded px-2 py-1.5 text-sm hover:bg-muted">{{ item.tinggi_former }} mm</div>
                            </div>
                        </div>

                        <div class="grid gap-2 relative" ref="jKeluar.elementRef">
                            <Label>Jam Keluar Oven <span class="text-destructive">*</span></Label>
                            <Input v-model="jKeluar.search.value" @focus="jKeluar.show.value = true" placeholder="Pilih Jam..." />
                            <div v-if="jKeluar.show.value" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-md p-1">
                                <div v-for="item in jKeluar.filtered.value" :key="item.id" @click="jKeluar.select(item)" class="cursor-pointer rounded px-2 py-1.5 text-sm hover:bg-muted">{{ item.jam_keluar_oven.substring(0,5) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2"><Label>Tanggal Keluar Oven <span class="text-destructive">*</span></Label><Input type="date" v-model="form.tanggal_keluar_oven" /></div>
                        <div class="grid gap-2"><Label>Tanggal Produksi <span class="text-destructive">*</span></Label><Input type="date" v-model="form.tgl_produksi" /></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2"><Label>Berat Former (g) <span class="text-destructive">*</span></Label><Input type="number" v-model="form.berat_former" /></div>
                        <div class="grid gap-2"><Label>Posisi Former</Label><Input type="number" v-model="form.posisi_former" /></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2">
                            <Label>Hasil Test 180</Label>
                            <Select v-model="form.hasil_test_180">
                                <SelectTrigger><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Belum Tes">Belum Tes</SelectItem>
                                    <SelectItem value="OK">OK</SelectItem>
                                    <SelectItem value="NG">NG</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-2">
                            <Label>Hasil Test 200</Label>
                            <Select v-model="form.hasil_test_200">
                                <SelectTrigger><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Belum Tes">Belum Tes</SelectItem>
                                    <SelectItem value="OK">OK</SelectItem>
                                    <SelectItem value="NG">NG</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        <Label for="keterangan">Keterangan</Label>
                        <textarea id="keterangan" v-model="form.keterangan" rows="2" placeholder="Tulis catatan..." class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:border-zinc-800"></textarea>
                    </div>
                </CardContent>
            </Card>

            <Button type="submit" :disabled="form.processing" class="w-full bg-primary hover:bg-primary/90 shadow-md h-11">
                <IconLoader2 v-if="form.processing" class="mr-2 animate-spin size-5" />
                <IconDeviceFloppy v-else class="mr-2 size-5" /> Simpan Semua Perubahan
            </Button>
        </form>
    </div>
</template>
