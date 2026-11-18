<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/components/ui/table";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

type Sample = {
    id: number;
    urutan: number;
    kode_sample: string;
    no_open: string | null;
    perlakuan: string | null;
    palm_wo: string | null;
    palm_wa: string | null;
    palm_water: string | null;
    mc_wo: string | null;
    mc_wa: string | null;
    mc_water: string | null;
    sl_wo: string | null;
    sl_wa: string | null;
    sl_water: string | null;
};

const props = defineProps<{
    tglTest: string;
    sesiKe: number;
    samples: Sample[];
    defaults: { spec: string | null; mulai_proses: string | null; selesai_proses: string | null; temp_air: number | null };
}>();

const form = useForm({
    tgl_test: props.tglTest,
    sesi_ke: props.sesiKe,
    spec: props.defaults.spec ?? "",
    mulai_proses: props.defaults.mulai_proses?.slice(0, 5) ?? "",
    selesai_proses: props.defaults.selesai_proses?.slice(0, 5) ?? "",
    temp_air: props.defaults.temp_air ?? "",
    records: props.samples.map((sample) => ({
        id: sample.id,
        perlakuan: sample.perlakuan ?? "",
        palm_wo: sample.palm_wo ?? "",
        palm_wa: sample.palm_wa ?? "",
        palm_water: sample.palm_water ?? "",
        mc_wo: sample.mc_wo ?? "",
        mc_wa: sample.mc_wa ?? "",
        mc_water: sample.mc_water ?? "",
        sl_wo: sample.sl_wo ?? "",
        sl_wa: sample.sl_wa ?? "",
        sl_water: sample.sl_water ?? "",
    })),
});

const labels: Record<string, string> = {
    palm_wo: "Palm WO",
    palm_wa: "Palm WA",
    palm_water: "Palm Water",
    mc_wo: "MC WO",
    mc_wa: "MC WA",
    mc_water: "MC Water",
    sl_wo: "SL WO",
    sl_wa: "SL WA",
    sl_water: "SL Water",
};

const fields = ["palm_wo", "palm_wa", "palm_water", "mc_wo", "mc_wa", "mc_water", "sl_wo", "sl_wa", "sl_water"] as const;

const recalculateWater = (index: number, group: "palm" | "mc" | "sl") => {
    const record = form.records[index];
    const wo = Number(record[`${group}_wo`]);
    const wa = Number(record[`${group}_wa`]);

    if (!record[`${group}_wo`] || !record[`${group}_wa`] || !Number.isFinite(wo) || !Number.isFinite(wa)) {
        return;
    }

    record[`${group}_water`] = (wa - wo).toFixed(3);
};

const calculationGroup = (field: typeof fields[number]) => {
    if (field === "palm_wo" || field === "palm_wa") return "palm";
    if (field === "mc_wo" || field === "mc_wa") return "mc";
    if (field === "sl_wo" || field === "sl_wa") return "sl";

    return null;
};
</script>

<template>
    <Head title="Lengkapi Semua Density Water Sample" />
    <div class="flex flex-col gap-6 p-4 pt-1 md:p-8">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('density-water-sample.index', { tgl_test: tglTest, sesi_ke: sesiKe })"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Lengkapi {{ samples.length }} Data Tahap Kedua</h2>
                <p class="text-sm text-muted-foreground">Tanggal test {{ tglTest }} · Sesi {{ sesiKe }} · Urut berdasarkan nomor urutan</p>
            </div>
        </div>

        <form class="space-y-6" @submit.prevent="form.put(route('density-water-sample.bulk-complete.store'))">
            <Card class="border-none shadow-lg">
                <CardHeader>
                    <CardTitle>Data Bersama untuk Semua Sample</CardTitle>
                    <p class="text-sm text-muted-foreground">Spec, waktu proses, dan suhu air cukup diisi satu kali dan akan diterapkan ke seluruh data pada tanggal ini.</p>
                </CardHeader>
                <CardContent class="grid gap-5 md:grid-cols-2">
                    <div class="grid gap-2 md:col-span-2">
                        <Label for="spec">Spec</Label>
                        <Input id="spec" v-model="form.spec" />
                        <p v-if="form.errors.spec" class="text-sm text-destructive">{{ form.errors.spec }}</p>
                    </div>
                    <div class="grid gap-2">
                        <Label for="mulai_proses">Mulai Proses</Label>
                        <Input id="mulai_proses" v-model="form.mulai_proses" type="time" />
                        <p v-if="form.errors.mulai_proses" class="text-sm text-destructive">{{ form.errors.mulai_proses }}</p>
                    </div>
                    <div class="grid gap-2">
                        <Label for="selesai_proses">Selesai Proses</Label>
                        <Input id="selesai_proses" v-model="form.selesai_proses" type="time" />
                        <p v-if="form.errors.selesai_proses" class="text-sm text-destructive">{{ form.errors.selesai_proses }}</p>
                    </div>
                    <div class="grid gap-2">
                        <Label for="temp_air">Temp Air (°C)</Label>
                        <Input id="temp_air" v-model="form.temp_air" type="number" step="1" />
                        <p v-if="form.errors.temp_air" class="text-sm text-destructive">{{ form.errors.temp_air }}</p>
                    </div>
                </CardContent>
            </Card>

            <Card class="border-none shadow-lg">
                <CardHeader>
                    <CardTitle>Data Per Sample</CardTitle>
                    <p v-if="form.errors.records" class="text-sm text-destructive">{{ form.errors.records }}</p>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto rounded-lg border">
                        <Table class="min-w-[1650px]">
                            <TableHeader>
                                <TableRow class="bg-muted/50">
                                    <TableHead>Urutan</TableHead>
                                    <TableHead>Kode Sample</TableHead>
                                    <TableHead>No. Open</TableHead>
                                    <TableHead>Perlakuan</TableHead>
                                    <TableHead v-for="field in fields" :key="field">{{ labels[field] }} (gr)</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow v-for="(sample, index) in samples" :key="sample.id">
                                    <TableCell class="font-bold">{{ sample.urutan }}</TableCell>
                                    <TableCell>{{ sample.kode_sample }}</TableCell>
                                    <TableCell>{{ sample.no_open || "-" }}</TableCell>
                                    <TableCell>
                                        <Input v-model="form.records[index].perlakuan" class="min-w-40" />
                                        <p v-if="form.errors[`records.${index}.perlakuan`]" class="mt-1 text-xs text-destructive">{{ form.errors[`records.${index}.perlakuan`] }}</p>
                                    </TableCell>
                                    <TableCell v-for="field in fields" :key="field">
                                        <Input v-model="form.records[index][field]" type="number" min="0" step="0.001" class="min-w-28" @input="calculationGroup(field) ? recalculateWater(index, calculationGroup(field)!) : undefined" />
                                        <p v-if="form.errors[`records.${index}.${field}`]" class="mt-1 text-xs text-destructive">{{ form.errors[`records.${index}.${field}`] }}</p>
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <Button type="submit" :disabled="form.processing || samples.length === 0" class="w-full">
                <IconLoader2 v-if="form.processing" class="mr-2 size-4 animate-spin" />
                <IconDeviceFloppy v-else class="mr-2 size-4" />
                Simpan {{ samples.length }} Data Tahap Kedua
            </Button>
        </form>
    </div>
</template>
