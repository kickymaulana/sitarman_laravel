<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { IconFlame, IconArrowLeft, IconCheck } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    thermalshocks: Array<{
        id: number;
        hari_tgl: string;
        posisi_former: number;
        thermal_pintu: { thermal_pintu: string } | null;
        customer: { customer: string } | null;
    }>;
    selectedIds: Array<number>;
}>();

const form = useForm({
    ids: props.selectedIds,
    suhu_awal_200: 0,
    suhu_display_200: 0,
    suhu_actual_200: 0,
    // Form fields baru untuk jam pengujian
    jam_awal_proses_200: "",
    jam_capai_suhu_200: "",
    jam_mulai_tembak_200: "",
    jam_selesai_tembak_200: "",
});

const submit = () => {
    form.put(route('thermalshock.bulkUpdate200'), {
        preserveScroll: true,
    });
};

// Fungsi helper pemformat otomatis jam HH:mm saat operator mengetik angka di form
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
    <Head title="Bulk Edit Parameter Suhu 200°C" />

    <div class="max-w-3xl mx-auto p-4 md:p-8">
        <div class="mb-4">
            <Button as-child variant="ghost" size="sm">
                <Link :href="route('thermalshock.index')" class="flex items-center gap-1.5 text-xs text-muted-foreground">
                    <IconArrowLeft class="size-4" /> Kembali ke Daftar
                </Link>
            </Button>
        </div>

        <Card class="shadow-md border border-amber-100 dark:border-amber-950">
            <CardHeader class="space-y-1 bg-amber-50/50 dark:bg-amber-950/10 pb-6 rounded-t-lg">
                <CardTitle class="text-lg font-bold flex items-center gap-2 text-amber-700 dark:text-amber-400">
                    <IconFlame class="size-5" />
                    Isi Parameter & Waktu Massal Suhu 200°C
                </CardTitle>
                <CardDescription>
                    Nilai suhu dan jam yang Anda masukkan akan langsung diterapkan ke <strong>{{ selectedIds.length }} record</strong> thermal shock sekaligus.
                </CardDescription>
            </CardHeader>

            <CardContent class="pt-6">
                <form @submit.prevent="submit" class="space-y-6">

                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">1. Parameter Suhu (°C)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-amber-50/10 dark:bg-amber-950/5 p-4 border rounded-lg">
                            <div class="space-y-1.5">
                                <Label for="suhu_awal_200" class="text-xs">Suhu Awal</Label>
                                <Input id="suhu_awal_200" type="number" v-model.number="form.suhu_awal_200" required min="0" class="h-9" />
                                <span v-if="form.errors.suhu_awal_200" class="text-xs text-destructive">{{ form.errors.suhu_awal_200 }}</span>
                            </div>

                            <div class="space-y-1.5">
                                <Label for="suhu_display_200" class="text-xs">Suhu Display</Label>
                                <Input id="suhu_display_200" type="number" v-model.number="form.suhu_display_200" required min="0" class="h-9" />
                                <span v-if="form.errors.suhu_display_200" class="text-xs text-destructive">{{ form.errors.suhu_display_200 }}</span>
                            </div>

                            <div class="space-y-1.5">
                                <Label for="suhu_actual_200" class="text-xs">Suhu Actual</Label>
                                <Input id="suhu_actual_200" type="number" v-model.number="form.suhu_actual_200" required min="0" class="h-9" />
                                <span v-if="form.errors.suhu_actual_200" class="text-xs text-destructive">{{ form.errors.suhu_actual_200 }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">2. Parameter Waktu Proses (HH:mm)</h4>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-amber-50/10 dark:bg-amber-950/5 p-4 border rounded-lg">
                            <div class="space-y-1.5">
                                <Label class="text-xs">Jam Awal Proses</Label>
                                <Input type="text" v-model="form.jam_awal_proses_200" @input="formatTimeInput('jam_awal_proses_200', $event)" placeholder="00:00" class="h-9 text-center font-mono" />
                                <span v-if="form.errors.jam_awal_proses_200" class="text-xs text-destructive">{{ form.errors.jam_awal_proses_200 }}</span>
                            </div>

                            <div class="space-y-1.5">
                                <Label class="text-xs">Jam Capai Suhu</Label>
                                <Input type="text" v-model="form.jam_capai_suhu_200" @input="formatTimeInput('jam_capai_suhu_200', $event)" placeholder="00:00" class="h-9 text-center font-mono" />
                                <span v-if="form.errors.jam_capai_suhu_200" class="text-xs text-destructive">{{ form.errors.jam_capai_suhu_200 }}</span>
                            </div>

                            <div class="space-y-1.5">
                                <Label class="text-xs">Jam Mulai Tembak</Label>
                                <Input type="text" v-model="form.jam_mulai_tembak_200" @input="formatTimeInput('jam_mulai_tembak_200', $event)" placeholder="00:00" class="h-9 text-center font-mono" />
                                <span v-if="form.errors.jam_mulai_tembak_200" class="text-xs text-destructive">{{ form.errors.jam_mulai_tembak_200 }}</span>
                            </div>

                            <div class="space-y-1.5">
                                <Label class="text-xs">Jam Selesai Tembak</Label>
                                <Input type="text" v-model="form.jam_selesai_tembak_200" @input="formatTimeInput('jam_selesai_tembak_200', $event)" placeholder="00:00" class="h-9 text-center font-mono" />
                                <span v-if="form.errors.jam_selesai_tembak_200" class="text-xs text-destructive">{{ form.errors.jam_selesai_tembak_200 }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <Label class="text-xs text-muted-foreground">Daftar Baris Target Update ({{ thermalshocks.length }} data):</Label>
                        <div class="border rounded-md max-h-36 overflow-y-auto text-xs divide-y bg-zinc-50/50 dark:bg-zinc-900/50">
                            <div v-for="item in thermalshocks" :key="item.id" class="p-2.5 flex justify-between items-center">
                                <div>
                                    <span class="font-semibold text-primary">Pos {{ item.posisi_former }}</span> -
                                    <span>{{ item.customer?.customer ?? 'No Customer' }}</span>
                                </div>
                                <div class="text-muted-foreground text-[11px]">
                                    {{ item.thermal_pintu?.thermal_pintu }} | {{ item.hari_tgl }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t">
                        <Button as-child variant="outline" type="button" class="text-xs">
                            <Link :href="route('thermalshock.index')">Batal</Link>
                        </Button>
                        <Button type="submit" :disabled="form.processing" class="bg-amber-600 hover:bg-amber-700 text-white text-xs">
                            <IconCheck class="mr-1.5 size-4" />
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan Massal' }}
                        </Button>
                    </div>

                </form>
            </CardContent>
        </Card>
    </div>
</template>
