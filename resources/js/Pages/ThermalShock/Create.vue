<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2, IconFlame, IconHammer } from "@tabler/icons-vue";
import { ref, computed, watch, type ComputedRef } from "vue";
import { onClickOutside } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalOvens: Array<{ id: number; thermal_oven: string }>;
    thermalPintus: Array<{ id: number; thermal_pintu: string }>;
    ovens: Array<{ id: number; oven: string }>;
    customers: Array<{ id: number; customer: string }>;
    modelSizes: Array<{ id: number; customer_id: number; modelsize: string }>;
    spesifikasis: Array<{ id: number; spesifikasi: string }>;
    tinggiFormers: Array<{ id: number; tinggi_former: number }>;
    jamKeluarOvens: Array<{ id: number; jam_keluar_oven: string }>;
}>();

const form = useForm({
    thermal_oven_id: "",
    thermal_pintu_id: "",
    hari_tgl: "",
    suhu_testing: "180",
    suhu_display: 0,
    suhu_actual: 0,
    jam_awal_proses: "",
    jam_capai_suhu: "",
    suhu_awal: 0,
    suhu_air: "-",
    jam_mulai_tembak: "",
    jam_selesai_tembak: "",

    // Field Gabungan
    kode_bakar: 0,
    kode_tanah: "-",
    oven_id: "",
    customer_id: "",
    modelsize_id: "",
    spesifikasi_id: "",
    tinggi_former_id: "",
    jam_keluar_oven_id: "",
    sampel: "-",
    berat_former: 0,
    tanggal_keluar_oven: "",
    tgl_produksi: "",
    posisi_former: 1,
    hasil_test_180: "Belum Tes",
    hasil_test_200: "Belum Tes",
    keterangan: "-"
});

// Reusable Dropdown Factory
const useDropdown = (propsList: ComputedRef<any[]> | any[], keyName: string, formField: string) => {
    const search = ref("");
    const show = ref(false);
    const elementRef = ref(null);

    const list = computed(() => {
        return Array.isArray(propsList) ? propsList : propsList.value;
    });

    onClickOutside(elementRef, () => {
        show.value = false;
        // @ts-ignore
        if (!form[formField]) {
            search.value = "";
        } else {
            const selected = list.value.find(item => item.id === form[formField]);
            if (selected) search.value = String(selected[keyName]);
        }
    });

    const filtered = computed(() => {
        if (!search.value) return list.value;
        return list.value.filter(item =>
            String(item[keyName]).toLowerCase().includes(search.value.toLowerCase())
        );
    });

    const select = (item: any) => {
        // @ts-ignore
        form[formField] = item.id;
        search.value = String(item[keyName]);
        show.value = false;
    };

    const reset = () => {
        // @ts-ignore
        form[formField] = "";
        search.value = "";
    };

    return { search, show, elementRef, filtered, select, reset };
};

// Inisialisasi Dropdown Standar
const tOven = useDropdown(props.thermalOvens, 'thermal_oven', 'thermal_oven_id');
const tPintu = useDropdown(props.thermalPintus, 'thermal_pintu', 'thermal_pintu_id');
const prodOven = useDropdown(props.ovens, 'oven', 'oven_id');
const cust = useDropdown(props.customers, 'customer', 'customer_id');
const spec = useDropdown(props.spesifikasis, 'spesifikasi', 'spesifikasi_id');
const tFormer = useDropdown(props.tinggiFormers, 'tinggi_former', 'tinggi_former_id');
const jKeluar = useDropdown(props.jamKeluarOvens, 'jam_keluar_oven', 'jam_keluar_oven_id');

// Logic Dependent Dropdown untuk Model Size (Bergantung pada Customer)
const availableModelSizes = computed(() => {
    if (!form.customer_id) return [];
    return props.modelSizes.filter(item => item.customer_id === form.customer_id);
});
const mSize = useDropdown(availableModelSizes, 'modelsize', 'modelsize_id');

// Reset Model Size secara otomatis jika Customer berubah
watch(() => form.customer_id, () => {
    mSize.reset();
});

// Auto Format Waktu (HH:mm)
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
</script>

<template>
    <Head title="Tambah Thermal Shock" />
    <div class="flex flex-col gap-6 p-4 md:p-8 pt-1">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('thermalshock.index')">
                    <IconArrowLeft class="size-4" />
                </Link>
            </Button>
            <h2 class="text-3xl font-bold tracking-tight">Tambah Record</h2>
        </div>

        <form @submit.prevent="form.post(route('thermalshock.store'))" class="space-y-6 max-w-5xl">

            <Card class="border-none shadow-md">
                <CardHeader class="border-b bg-zinc-50/50 dark:bg-zinc-900/50">
                    <CardTitle class="text-primary flex items-center gap-2 text-lg">
                        <IconFlame class="size-5" /> 1. Parameter Utama Thermal Shock
                    </CardTitle>
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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2 relative" ref="prodOven.elementRef">
                            <Label>Oven Produksi <span class="text-destructive">*</span></Label>
                            <Input v-model="prodOven.search.value" @focus="prodOven.show.value = true" placeholder="Pilih Oven..." />
                            <div v-if="prodOven.show.value" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-md p-1">
                                <div v-for="item in prodOven.filtered.value" :key="item.id" @click="prodOven.select(item)" class="cursor-pointer rounded px-2 py-1.5 text-sm hover:bg-muted">{{ item.oven }}</div>
                            </div>
                        </div>

                        <div class="grid gap-2 relative" ref="cust.elementRef">
                            <Label>Customer <span class="text-destructive">*</span></Label>
                            <Input v-model="cust.search.value" @focus="cust.show.value = true" placeholder="Pilih Customer..." />
                            <div v-if="cust.show.value" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-md p-1">
                                <div v-for="item in cust.filtered.value" :key="item.id" @click="cust.select(item)" class="cursor-pointer rounded px-2 py-1.5 text-sm hover:bg-muted">{{ item.customer }}</div>
                            </div>
                        </div>

                        <div class="grid gap-2 relative" ref="mSize.elementRef">
                            <Label>Model Size <span class="text-destructive">*</span></Label>
                            <Input
                                v-model="mSize.search.value"
                                @focus="mSize.show.value = true"
                                :disabled="!form.customer_id"
                                :placeholder="form.customer_id ? 'Pilih Model Size...' : 'Pilih Customer Terlebih Dahulu'"
                            />
                            <div v-if="mSize.show.value && form.customer_id" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-md p-1">
                                <div v-if="mSize.filtered.value.length === 0" class="py-2 text-center text-sm text-muted-foreground">Tidak ada model size untuk customer ini.</div>
                                <div v-else v-for="item in mSize.filtered.value" :key="item.id" @click="mSize.select(item)" class="cursor-pointer rounded px-2 py-1.5 text-sm hover:bg-muted">{{ item.modelsize }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="grid gap-2 relative" ref="spec.elementRef">
                            <Label>Spesifikasi <span class="text-destructive">*</span></Label>
                            <Input v-model="spec.search.value" @focus="spec.show.value = true" placeholder="Pilih Spesifikasi..." />
                            <div v-if="spec.show.value" class="absolute z-50 mt-20 max-h-40 w-full overflow-y-auto rounded-md border bg-white dark:bg-zinc-900 shadow-md p-1">
                                <div v-for="item in spec.filtered.value" :key="item.id" @click="spec.select(item)" class="cursor-pointer rounded px-2 py-1.5 text-sm hover:bg-muted">{{ item.spesifikasi }}</div>
                            </div>
                        </div>

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

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                        <textarea
                            id="keterangan"
                            v-model="form.keterangan"
                            rows="2"
                            placeholder="Tulis keterangan..."
                            class="flex min-h-[60px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50 dark:border-zinc-800"
                        ></textarea>
                    </div>
                </CardContent>
            </Card>

            <Button type="submit" :disabled="form.processing" class="w-full bg-primary hover:bg-primary/90 shadow-md h-11">
                <IconLoader2 v-if="form.processing" class="mr-2 animate-spin size-5" />
                <IconDeviceFloppy v-else class="mr-2 size-5" /> Simpan Record Gabungan
            </Button>
        </form>
    </div>
</template>
