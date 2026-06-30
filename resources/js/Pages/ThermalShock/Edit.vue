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
    thermalOvens: Array<{ id: number; thermal_oven: string }>;
    thermalPintus: Array<{ id: number; thermal_pintu: string }>;
    ovens: Array<{ id: number; oven: string }>;
    customers: Array<{ id: number; customer: string; model: string; spesifikasi: string; size: string }>;
    tinggiFormers: Array<{ id: number; tinggi_former: number }>;
    jamKeluarOvens: Array<{ id: number; jam_keluar_oven: string }>;
}>();

const form = useForm({
    thermal_oven_id: props.thermalshock.thermal_oven_id,
    thermal_pintu_id: props.thermalshock.thermal_pintu_id,
    hari_tgl: props.thermalshock.hari_tgl,
    suhu_testing: String(props.thermalshock.suhu_testing ?? "180"),
    suhu_display: props.thermalshock.suhu_display,
    suhu_actual: props.thermalshock.suhu_actual,
    jam_awal_proses: props.thermalshock.jam_awal_proses,
    jam_capai_suhu: props.thermalshock.jam_capai_suhu,
    suhu_awal: props.thermalshock.suhu_awal,
    suhu_air: props.thermalshock.suhu_air,
    jam_mulai_tembak: props.thermalshock.jam_mulai_tembak,
    jam_selesai_tembak: props.thermalshock.jam_selesai_tembak,

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

const tOven = useDropdown(props.thermalOvens, 'thermal_oven', 'thermal_oven_id');
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
    const to = props.thermalOvens.find(o => o.id === form.thermal_oven_id);
    if (to) tOven.search.value = to.thermal_oven;

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

    if (form.jam_awal_proses) form.jam_awal_proses = form.jam_awal_proses.substring(0, 5);
    if (form.jam_capai_suhu) form.jam_capai_suhu = form.jam_capai_suhu.substring(0, 5);
    if (form.jam_mulai_tembak) form.jam_mulai_tembak = form.jam_mulai_tembak.substring(0, 5);
    if (form.jam_selesai_tembak) form.jam_selesai_tembak = form.jam_selesai_tembak.substring(0, 5);
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
                    <CardTitle class="text-primary flex items-center gap-2 text-lg">
                        <IconFlame class="size-5" /> 1. Parameter Utama Thermal Shock (ID: {{ props.thermalshock.id }})
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
                        <AlertDialogContent>
                            <AlertDialogHeader>
                                <AlertDialogTitle>Hapus Data Permanen?</AlertDialogTitle>
                                <AlertDialogDescription>Tindakan ini tidak bisa dibatalkan. Log record tanggal {{ props.thermalshock.hari_tgl }} akan terhapus selamanya.</AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction @click="router.delete(route('thermalshock.destroy', props.thermalshock.id))" class="bg-destructive text-white hover:bg-destructive/90">Ya, Hapus</AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>
                <CardContent class="pt-6 space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="grid gap-2 relative" ref="tOven.elementRef">
                            <Label>Thermal Oven <span class="text-destructive">*</span></Label>
                            <Input v-model="tOven.search.value" @focus="tOven.show.value = true" placeholder="Pilih Thermal Oven..." />
                            <div v-if="tOven.show.value" class="absolute z-50 mt-20 max-h-48 w-full overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-lg p-1">
                                <div v-for="o in tOven.filtered.value" :key="o.id" @click="tOven.select(o)" class="cursor-pointer rounded px-2 py-1.5 text-sm hover:bg-muted">{{ o.thermal_oven }}</div>
                            </div>
                        </div>
                        <div class="grid gap-2 relative" ref="tPintu.elementRef">
                            <Label>Thermal Pintu <span class="text-destructive">*</span></Label>
                            <Input v-model="tPintu.search.value" @focus="tPintu.show.value = true" placeholder="Pilih Thermal Pintu..." />
                            <div v-if="tPintu.show.value" class="absolute z-50 mt-20 max-h-48 w-full overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-lg p-1">
                                <div v-for="p in tPintu.filtered.value" :key="p.id" @click="tPintu.select(p)" class="cursor-pointer rounded px-2 py-1.5 text-sm hover:bg-muted">{{ p.thermal_pintu }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2">
                            <Label for="hari_tgl">Hari / Tanggal <span class="text-destructive">*</span></Label>
                            <Input type="date" id="hari_tgl" v-model="form.hari_tgl" />
                        </div>
                        <div class="grid gap-2">
                            <Label>Suhu Testing</Label>
                            <Select v-model="form.suhu_testing">
                                <SelectTrigger><SelectValue /></SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="180">180 °C</SelectItem>
                                    <SelectItem value="200">200 °C</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <div class="grid gap-2"><Label for="suhu_air">Suhu Air</Label><Input id="suhu_air" v-model="form.suhu_air" /></div>
                    </div>

                    <div class="grid grid-cols-3 md:grid-cols-3 gap-4">
                        <div class="grid gap-2"><Label for="suhu_awal">Suhu Awal (°C)</Label><Input type="number" id="suhu_awal" v-model="form.suhu_awal" /></div>
                        <div class="grid gap-2"><Label for="suhu_display">Suhu Display (°C)</Label><Input type="number" id="suhu_display" v-model="form.suhu_display" /></div>
                        <div class="grid gap-2"><Label for="suhu_actual">Suhu Actual (°C)</Label><Input type="number" id="suhu_actual" v-model="form.suhu_actual" /></div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-2">
                        <div class="grid gap-2"><Label>Jam Awal Proses</Label><Input type="text" v-model="form.jam_awal_proses" @input="formatTimeInput('jam_awal_proses', $event)" placeholder="00:00" /></div>
                        <div class="grid gap-2"><Label>Jam Capai Suhu</Label><Input type="text" v-model="form.jam_capai_suhu" @input="formatTimeInput('jam_capai_suhu', $event)" placeholder="00:00" /></div>
                        <div class="grid gap-2"><Label>Jam Mulai Tembak</Label><Input type="text" v-model="form.jam_mulai_tembak" @input="formatTimeInput('jam_mulai_tembak', $event)" placeholder="00:00" /></div>
                        <div class="grid gap-2"><Label>Jam Selesai Tembak</Label><Input type="text" v-model="form.jam_selesai_tembak" @input="formatTimeInput('jam_selesai_tembak', $event)" placeholder="00:00" /></div>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-md">
                <CardHeader class="border-b bg-zinc-50/50 dark:bg-zinc-900/50">
                    <CardTitle class="text-emerald-600 flex items-center gap-2 text-lg">
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
                        <Label>Keterangan</Label>
                        <textarea v-model="form.keterangan" rows="2" placeholder="Tulis catatan..." class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:border-zinc-800"></textarea>
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
