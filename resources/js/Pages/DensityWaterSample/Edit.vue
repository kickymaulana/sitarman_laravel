<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle, AlertDialogTrigger } from "@/components/ui/alert-dialog";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Head, Link, router, useForm } from "@inertiajs/vue3";
import { IconArrowLeft, IconDeviceFloppy, IconTrash } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

type Sample = Record<string, any> & {
    id: number; tgl_test: string; urutan: number; kode_sample: string; no_open: string; status: string;
};

const props = defineProps<{ sample: Sample }>();
const value = (key: string) => props.sample[key] ?? "";
const timeValue = (key: string) => props.sample[key]?.slice(0, 5) ?? "";

const form = useForm({
    tgl_test: value("tgl_test"), urutan: value("urutan"), kode_sample: value("kode_sample"), no_open: value("no_open"),
    ketebalan: value("ketebalan"), berat_awal: value("berat_awal"), berat_akhir: value("berat_akhir"), volume: value("volume"), density: value("density"),
    perlakuan: value("perlakuan"), palm_wo: value("palm_wo"), palm_wa: value("palm_wa"), palm_water: value("palm_water"),
    mc_wo: value("mc_wo"), mc_wa: value("mc_wa"), mc_water: value("mc_water"), sl_wo: value("sl_wo"), sl_wa: value("sl_wa"), sl_water: value("sl_water"),
    spec: value("spec"), mulai_proses: timeValue("mulai_proses"), selesai_proses: timeValue("selesai_proses"), temp_air: value("temp_air"),
});

const firstFields = [
    { key: "ketebalan", label: "Ketebalan", unit: "mm" }, { key: "berat_awal", label: "Berat Awal", unit: "gr" },
    { key: "berat_akhir", label: "Berat Akhir", unit: "gr" }, { key: "volume", label: "Volume", unit: "ml" },
    { key: "density", label: "Density", unit: "ρ = m / V" },
];
const resultFields = ["palm_wo", "palm_wa", "palm_water", "mc_wo", "mc_wa", "mc_water", "sl_wo", "sl_wa", "sl_water"];
const labels: Record<string, string> = { palm_wo: "Palm WO", palm_wa: "Palm WA", palm_water: "Palm Water", mc_wo: "MC WO", mc_wa: "MC WA", mc_water: "MC Water", sl_wo: "SL WO", sl_wa: "SL WA", sl_water: "SL Water" };
</script>

<template>
    <Head title="Edit Density Water Sample" />
    <div class="flex flex-col gap-6 p-4 pt-1 md:p-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <Button variant="outline" size="icon" as-child class="rounded-full"><Link :href="route('density-water-sample.index', { tgl_test: sample.tgl_test })"><IconArrowLeft class="size-4" /></Link></Button>
                <h2 class="text-2xl font-bold">Edit Density Water Sample</h2>
            </div>
            <AlertDialog>
                <AlertDialogTrigger as-child><Button variant="destructive"><IconTrash class="mr-2 size-4" />Hapus</Button></AlertDialogTrigger>
                <AlertDialogContent>
                    <AlertDialogHeader><AlertDialogTitle>Hapus data?</AlertDialogTitle><AlertDialogDescription>Data {{ sample.kode_sample }} urutan {{ sample.urutan }} akan dihapus permanen.</AlertDialogDescription></AlertDialogHeader>
                    <AlertDialogFooter><AlertDialogCancel>Batal</AlertDialogCancel><AlertDialogAction class="bg-destructive text-white" @click="router.delete(route('density-water-sample.destroy', sample.id))">Ya, Hapus</AlertDialogAction></AlertDialogFooter>
                </AlertDialogContent>
            </AlertDialog>
        </div>
        <Card class="max-w-5xl border-none shadow-lg">
            <CardHeader><CardTitle>Semua Data</CardTitle></CardHeader>
            <CardContent>
                <form class="space-y-7" @submit.prevent="form.put(route('density-water-sample.update', sample.id))">
                    <section class="space-y-4">
                        <h3 class="border-b pb-2 font-semibold">Tahap Pertama</h3>
                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="grid gap-2"><Label>Tanggal Test *</Label><Input v-model="form.tgl_test" type="date" /><p v-if="form.errors.tgl_test" class="text-sm text-destructive">{{ form.errors.tgl_test }}</p></div>
                            <div class="grid gap-2"><Label>Urutan *</Label><Input v-model="form.urutan" type="number" min="1" /><p v-if="form.errors.urutan" class="text-sm text-destructive">{{ form.errors.urutan }}</p></div>
                            <div class="grid gap-2"><Label>Kode Sample *</Label><Input v-model="form.kode_sample" /><p v-if="form.errors.kode_sample" class="text-sm text-destructive">{{ form.errors.kode_sample }}</p></div>
                            <div class="grid gap-2"><Label>No. Open</Label><Input v-model="form.no_open" /><p v-if="form.errors.no_open" class="text-sm text-destructive">{{ form.errors.no_open }}</p></div>
                            <div v-for="field in firstFields" :key="field.key" class="grid gap-2"><Label>{{ field.label }} ({{ field.unit }})</Label><Input v-model="form[field.key]" type="number" min="0" step="0.01" /><p v-if="form.errors[field.key]" class="text-sm text-destructive">{{ form.errors[field.key] }}</p></div>
                        </div>
                    </section>
                    <section class="space-y-4">
                        <h3 class="border-b pb-2 font-semibold">Tahap Kedua</h3>
                            <div class="grid gap-5 md:grid-cols-2">
                            <div class="grid gap-2"><Label>Perlakuan</Label><Input v-model="form.perlakuan" /></div>
                        </div>

                        <div class="grid gap-5 md:grid-cols-3">
                            <div v-for="field in resultFields" :key="field" class="grid gap-2"><Label>{{ labels[field] }} (gr)</Label><Input v-model="form[field]" type="number" min="0" step="0.001" /><p v-if="form.errors[field]" class="text-sm text-destructive">{{ form.errors[field] }}</p></div>
                        </div>
                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="grid gap-2 md:col-span-2"><Label>Spec</Label><Input v-model="form.spec" /></div>
                            <div class="grid gap-2"><Label>Mulai Proses</Label><Input v-model="form.mulai_proses" type="time" /></div>
                            <div class="grid gap-2"><Label>Selesai Proses</Label><Input v-model="form.selesai_proses" type="time" /></div>
                            <div class="grid gap-2"><Label>Temp Air (°C)</Label><Input v-model="form.temp_air" type="number" /></div>
                        </div>
                    </section>
                    <Button type="submit" :disabled="form.processing" class="w-full"><IconDeviceFloppy class="mr-2 size-4" />Simpan Perubahan</Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
