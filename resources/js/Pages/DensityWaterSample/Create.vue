<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

const props = defineProps<{
    selectedDate?: string | null;
    selectedSession?: number | string | null;
}>();

const form = useForm({
    tgl_test: props.selectedDate ?? "",
    sesi_ke: props.selectedSession ? String(props.selectedSession) : "",
    kode_sample: "",
    no_open: "",
    ketebalan: "",
    berat_awal: "",
    berat_akhir: "",
    volume: "",
    density: "",
});

const fields = [
    { key: "ketebalan", label: "Ketebalan", unit: "mm" },
    { key: "berat_awal", label: "Berat Awal", unit: "gr" },
    { key: "berat_akhir", label: "Berat Akhir", unit: "gr" },
    { key: "volume", label: "Volume", unit: "ml" },
    { key: "density", label: "Density", unit: "ρ = m / V" },
] as const;

const recalculateVolumeAndDensity = () => {
    const beratAwal = Number(form.berat_awal);
    const beratAkhir = Number(form.berat_akhir);

    if (!form.berat_awal || !form.berat_akhir || !Number.isFinite(beratAwal) || !Number.isFinite(beratAkhir)) {
        return;
    }

    const volume = beratAwal - beratAkhir;
    form.volume = volume.toFixed(2);
    form.density = volume === 0 ? "" : (beratAwal / volume).toFixed(2);
};
</script>

<template>
    <Head title="Input Density Water Sample" />
    <div class="flex flex-col gap-6 p-4 pt-1 md:p-8">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('density-water-sample.index')"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <h2 class="text-2xl font-bold tracking-tight">Input Tahap Pertama</h2>
        </div>
        <Card class="max-w-4xl border-none shadow-lg">
            <CardHeader>
                <CardTitle>Data Awal Density Water Sample</CardTitle>
                <p class="text-sm text-muted-foreground">Nomor urutan dibuat otomatis berdasarkan tanggal test.</p>
            </CardHeader>
            <CardContent>
                <form class="space-y-6" @submit.prevent="form.post(route('density-water-sample.store'))">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="tgl_test">Tanggal Test <span class="text-destructive">*</span></Label>
                            <Input id="tgl_test" v-model="form.tgl_test" type="date" />
                            <p class="text-xs text-muted-foreground">Sesi akan dibuat otomatis untuk tanggal ini.</p>
                            <p v-if="form.errors.tgl_test" class="text-sm text-destructive">{{ form.errors.tgl_test }}</p>
                        </div>
                        <div class="grid gap-2">
                            <Label for="kode_sample">Kode Sample <span class="text-destructive">*</span></Label>
                            <Input id="kode_sample" v-model="form.kode_sample" />
                            <p v-if="form.errors.kode_sample" class="text-sm text-destructive">{{ form.errors.kode_sample }}</p>
                        </div>
                        <div class="grid gap-2 md:col-span-2">
                            <Label for="no_open">No. Open</Label>
                            <Input id="no_open" v-model="form.no_open" />
                            <p v-if="form.errors.no_open" class="text-sm text-destructive">{{ form.errors.no_open }}</p>
                        </div>
                        <div v-for="field in fields" :key="field.key" class="grid gap-2">
                            <Label :for="field.key">{{ field.label }} ({{ field.unit }})</Label>
                            <Input :id="field.key" v-model="form[field.key]" type="number" min="0" step="0.01" @input="field.key === 'berat_awal' || field.key === 'berat_akhir' ? recalculateVolumeAndDensity() : undefined" />
                            <p v-if="form.errors[field.key]" class="text-sm text-destructive">{{ form.errors[field.key] }}</p>
                        </div>
                    </div>
                    <Button type="submit" :disabled="form.processing" class="w-full">
                        <IconLoader2 v-if="form.processing" class="mr-2 size-4 animate-spin" />
                        <IconDeviceFloppy v-else class="mr-2 size-4" />
                        Simpan Tahap Pertama
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
