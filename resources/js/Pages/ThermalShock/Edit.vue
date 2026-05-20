<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm, router } from "@inertiajs/vue3";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { IconArrowLeft, IconDeviceFloppy, IconDotsVertical, IconTrash, IconLoader2 } from "@tabler/icons-vue";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { ref, computed, onMounted } from "vue";
import { onClickOutside } from "@vueuse/core";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshock: any;
    ovens: Array<{ id: number; thermal_oven: string }>;
    pintus: Array<{ id: number; thermal_pintu: string }>;
}>();

const form = useForm({
    thermal_oven_id: props.thermalshock.thermal_oven_id,
    thermal_pintu_id: props.thermalshock.thermal_pintu_id,
    hari_tgl: props.thermalshock.hari_tgl,
    suhu_testing: props.thermalshock.suhu_testing,
    suhu_motor: props.thermalshock.suhu_motor,
    suhu_display: props.thermalshock.suhu_display,
    suhu_actual: props.thermalshock.suhu_actual,
    jam_awal_proses: props.thermalshock.jam_awal_proses,
    jam_capai_suhu: props.thermalshock.jam_capai_suhu,
    suhu_awal: props.thermalshock.suhu_awal,
    suhu_air: props.thermalshock.suhu_air,
    jam_mulai_tembak: props.thermalshock.jam_mulai_tembak,
    jam_selesai_tembak: props.thermalshock.jam_selesai_tembak
});

// Dropdown Oven Logic
const searchOven = ref("");
const showOvenDropdown = ref(false);
const ovenRef = ref(null);

onClickOutside(ovenRef, () => {
    showOvenDropdown.value = false;
    if (!form.thermal_oven_id) {
        searchOven.value = "";
    } else {
        const selected = props.ovens.find(o => o.id === form.thermal_oven_id);
        if (selected) searchOven.value = selected.thermal_oven;
    }
});

const filteredOvens = computed(() => {
    if (!searchOven.value) return props.ovens;
    return props.ovens.filter(o =>
        o.thermal_oven.toLowerCase().includes(searchOven.value.toLowerCase())
    );
});

const selectOven = (oven: { id: number; thermal_oven: string }) => {
    form.thermal_oven_id = oven.id;
    searchOven.value = oven.thermal_oven;
    showOvenDropdown.value = false;
};

// Dropdown Pintu Logic
const searchPintu = ref("");
const showPintuDropdown = ref(false);
const pintuRef = ref(null);

onClickOutside(pintuRef, () => {
    showPintuDropdown.value = false;
    if (!form.thermal_pintu_id) {
        searchPintu.value = "";
    } else {
        const selected = props.pintus.find(p => p.id === form.thermal_pintu_id);
        if (selected) searchPintu.value = selected.thermal_pintu;
    }
});

const filteredPintus = computed(() => {
    if (!searchPintu.value) return props.pintus;
    return props.pintus.filter(p =>
        p.thermal_pintu.toLowerCase().includes(searchPintu.value.toLowerCase())
    );
});

const selectPintu = (pintu: { id: number; thermal_pintu: string }) => {
    form.thermal_pintu_id = pintu.id;
    searchPintu.value = pintu.thermal_pintu;
    showPintuDropdown.value = false;
};

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

onMounted(() => {
    const oven = props.ovens.find(o => o.id === form.thermal_oven_id);
    if (oven) searchOven.value = oven.thermal_oven;

    const pintu = props.pintus.find(p => p.id === form.thermal_pintu_id);
    if (pintu) searchPintu.value = pintu.thermal_pintu;

    // Format existing times from database to HH:mm just in case they have seconds (HH:mm:ss)
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
                    <Link :href="route('thermalshock.show', props.thermalshock.id)"><IconArrowLeft class="size-4" /></Link>
                </Button>
                <h2 class="text-3xl font-bold tracking-tight">Edit Thermal Shock</h2>
            </div>
        </div>

        <div class="max-w-4xl">
            <Card class="border-none shadow-lg">
                <CardHeader class="flex flex-row items-center justify-between border-b pb-4 mb-4">
                    <CardTitle class="text-primary text-lg">Update Data ID: {{ props.thermalshock.id }}</CardTitle>
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
                                <AlertDialogTitle>Hapus Data?</AlertDialogTitle>
                                <AlertDialogDescription>Hapus permanen log thermal shock tanggal {{ props.thermalshock.hari_tgl }}?</AlertDialogDescription>
                            </AlertDialogHeader>
                            <AlertDialogFooter>
                                <AlertDialogCancel>Batal</AlertDialogCancel>
                                <AlertDialogAction @click="router.delete(route('thermalshock.destroy', props.thermalshock.id))" class="bg-destructive text-white hover:bg-destructive/90">Ya, Hapus</AlertDialogAction>
                            </AlertDialogFooter>
                        </AlertDialogContent>
                    </AlertDialog>
                </CardHeader>

                <CardContent>
                    <form @submit.prevent="form.put(route('thermalshock.update', props.thermalshock.id))" class="space-y-6">

                        <!-- Row 1: Oven & Pintu Relasi -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2 relative" ref="ovenRef">
                                <Label>Thermal Oven</Label>
                                <Input
                                    v-model="searchOven"
                                    @focus="showOvenDropdown = true"
                                    placeholder="Cari & Pilih Oven..."
                                />
                                <div v-if="showOvenDropdown" class="absolute z-50 mt-20 max-h-48 w-full overflow-y-auto rounded-md border bg-popover text-popover-foreground shadow-md p-1 bg-white dark:bg-zinc-900">
                                    <div v-if="filteredOvens.length === 0" class="py-3 text-center text-sm text-muted-foreground">
                                        Oven tidak ditemukan.
                                    </div>
                                    <div v-else v-for="o in filteredOvens" :key="o.id" @click="selectOven(o)" class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                                        {{ o.thermal_oven }}
                                    </div>
                                </div>
                                <p v-if="form.errors.thermal_oven_id" class="text-sm text-destructive">{{ form.errors.thermal_oven_id }}</p>
                            </div>

                            <div class="grid gap-2 relative" ref="pintuRef">
                                <Label>Thermal Pintu</Label>
                                <Input
                                    v-model="searchPintu"
                                    @focus="showPintuDropdown = true"
                                    placeholder="Cari & Pilih Pintu..."
                                />
                                <div v-if="showPintuDropdown" class="absolute z-50 mt-20 max-h-48 w-full overflow-y-auto rounded-md border bg-popover text-popover-foreground shadow-md p-1 bg-white dark:bg-zinc-900">
                                    <div v-if="filteredPintus.length === 0" class="py-3 text-center text-sm text-muted-foreground">
                                        Pintu tidak ditemukan.
                                    </div>
                                    <div v-else v-for="p in filteredPintus" :key="p.id" @click="selectPintu(p)" class="relative flex cursor-pointer select-none items-center rounded-sm px-2 py-1.5 text-sm outline-none hover:bg-accent hover:text-accent-foreground">
                                        {{ p.thermal_pintu }}
                                    </div>
                                </div>
                                <p v-if="form.errors.thermal_pintu_id" class="text-sm text-destructive">{{ form.errors.thermal_pintu_id }}</p>
                            </div>
                        </div>

                        <!-- Row 2: Hari Tgl & Suhu Testing -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="hari_tgl">Hari / Tanggal</Label>
                                <Input type="date" id="hari_tgl" v-model="form.hari_tgl" />
                                <p v-if="form.errors.hari_tgl" class="text-sm text-destructive">{{ form.errors.hari_tgl }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="suhu_testing">Suhu Testing (°C)</Label>
                                <!-- Ubah select menjadi input biasa dengan attribute list -->
                                <Input
                                    type="number"
                                    id="suhu_testing"
                                    v-model="form.suhu_testing"
                                    list="opsi_suhu"
                                    placeholder="Pilih atau ketik suhu..."
                                />
                                <!-- Datalist menyediakan rekomendasi pilihan (180 & 200) saat input diklik -->
                                <datalist id="opsi_suhu">
                                    <option value="180">180 °C</option>
                                    <option value="200">200 °C</option>
                                </datalist>
                                <p v-if="form.errors.suhu_testing" class="text-sm text-destructive">{{ form.errors.suhu_testing }}</p>
                            </div>
                        </div>

                        <!-- Row 3: Parameter Suhu -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="grid gap-2">
                                <Label for="suhu_awal">Suhu Awal (°C)</Label>
                                <Input type="number" id="suhu_awal" v-model="form.suhu_awal" placeholder="Suhu awal" />
                                <p v-if="form.errors.suhu_awal" class="text-sm text-destructive">{{ form.errors.suhu_awal }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="suhu_display">Suhu Display (°C)</Label>
                                <Input type="number" id="suhu_display" v-model="form.suhu_display" placeholder="Suhu display" />
                                <p v-if="form.errors.suhu_display" class="text-sm text-destructive">{{ form.errors.suhu_display }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="suhu_actual">Suhu Actual (°C)</Label>
                                <Input type="number" id="suhu_actual" v-model="form.suhu_actual" placeholder="Suhu aktual" />
                                <p v-if="form.errors.suhu_actual" class="text-sm text-destructive">{{ form.errors.suhu_actual }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="suhu_air">Suhu Air</Label>
                                <Input id="suhu_air" v-model="form.suhu_air" placeholder="Contoh: 31/32" />
                                <p v-if="form.errors.suhu_air" class="text-sm text-destructive">{{ form.errors.suhu_air }}</p>
                            </div>
                        </div>

                        <!-- Row 4: Waktu Proses (Format 24 Jam Indonesia) -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="grid gap-2">
                                <Label for="jam_awal_proses">Jam Awal Proses</Label>
                                <Input type="text" id="jam_awal_proses" v-model="form.jam_awal_proses" @input="formatTimeInput('jam_awal_proses', $event)" placeholder="00:00" />
                                <span class="text-[10px] text-muted-foreground italic">Format 24 Jam (Contoh: 14:00)</span>
                                <p v-if="form.errors.jam_awal_proses" class="text-sm text-destructive">{{ form.errors.jam_awal_proses }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="jam_capai_suhu">Jam Capai Suhu</Label>
                                <Input type="text" id="jam_capai_suhu" v-model="form.jam_capai_suhu" @input="formatTimeInput('jam_capai_suhu', $event)" placeholder="00:00" />
                                <span class="text-[10px] text-muted-foreground italic">Format 24 Jam (Contoh: 14:30)</span>
                                <p v-if="form.errors.jam_capai_suhu" class="text-sm text-destructive">{{ form.errors.jam_capai_suhu }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="jam_mulai_tembak">Jam Mulai Tembak</Label>
                                <Input type="text" id="jam_mulai_tembak" v-model="form.jam_mulai_tembak" @input="formatTimeInput('jam_mulai_tembak', $event)" placeholder="00:00" />
                                <span class="text-[10px] text-muted-foreground italic">Format 24 Jam (Contoh: 15:00)</span>
                                <p v-if="form.errors.jam_mulai_tembak" class="text-sm text-destructive">{{ form.errors.jam_mulai_tembak }}</p>
                            </div>
                            <div class="grid gap-2">
                                <Label for="jam_selesai_tembak">Jam Selesai Tembak</Label>
                                <Input type="text" id="jam_selesai_tembak" v-model="form.jam_selesai_tembak" @input="formatTimeInput('jam_selesai_tembak', $event)" placeholder="00:00" />
                                <span class="text-[10px] text-muted-foreground italic">Format 24 Jam (Contoh: 16:00)</span>
                                <p v-if="form.errors.jam_selesai_tembak" class="text-sm text-destructive">{{ form.errors.jam_selesai_tembak }}</p>
                            </div>
                        </div>

                        <Button type="submit" :disabled="form.processing" class="w-full bg-primary hover:bg-primary/90 mt-4 shadow-md">
                            <IconLoader2 v-if="form.processing" class="mr-2 animate-spin" />
                            <IconDeviceFloppy v-else class="mr-2" /> Simpan Perubahan
                        </Button>
                    </form>
                </CardContent>
            </Card>
        </div>
    </div>
</template>
