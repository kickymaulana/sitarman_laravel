<script setup lang="ts">
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { IconArrowLeft, IconDeviceFloppy, IconLoader2 } from "@tabler/icons-vue";

defineOptions({ layout: AuthenticatedLayout });

type Sample = {
    id: number;
    tgl_test: string;
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
    spec: string | null;
    mulai_proses: string | null;
    selesai_proses: string | null;
    temp_air: number | null;
};

const props = defineProps<{
    sample: Sample;
    defaults: { spec: string | null; mulai_proses: string | null; selesai_proses: string | null; temp_air: number | null };
}>();

const form = useForm({
    perlakuan: props.sample.perlakuan ?? "",
    palm_wo: props.sample.palm_wo ?? "",
    palm_wa: props.sample.palm_wa ?? "",
    palm_water: props.sample.palm_water ?? "",
    mc_wo: props.sample.mc_wo ?? "",
    mc_wa: props.sample.mc_wa ?? "",
    mc_water: props.sample.mc_water ?? "",
    sl_wo: props.sample.sl_wo ?? "",
    sl_wa: props.sample.sl_wa ?? "",
    sl_water: props.sample.sl_water ?? "",
    spec: props.defaults.spec ?? "",
    mulai_proses: props.defaults.mulai_proses?.slice(0, 5) ?? "",
    selesai_proses: props.defaults.selesai_proses?.slice(0, 5) ?? "",
    temp_air: props.defaults.temp_air ?? "",
});

const measurementGroups = [
    { title: "Palm", fields: ["palm_wo", "palm_wa", "palm_water"] },
    { title: "MC", fields: ["mc_wo", "mc_wa", "mc_water"] },
    { title: "SL", fields: ["sl_wo", "sl_wa", "sl_water"] },
] as const;

const labels: Record<string, string> = {
    palm_wo: "Palm WO", palm_wa: "Palm WA", palm_water: "Palm Water",
    mc_wo: "MC WO", mc_wa: "MC WA", mc_water: "MC Water",
    sl_wo: "SL WO", sl_wa: "SL WA", sl_water: "SL Water",
};
</script>

<template>
    <Head title="Lengkapi Density Water Sample" />
    <div class="flex flex-col gap-6 p-4 pt-1 md:p-8">
        <div class="flex items-center gap-4">
            <Button variant="outline" size="icon" as-child class="rounded-full">
                <Link :href="route('density-water-sample.index', { tgl_test: sample.tgl_test })"><IconArrowLeft class="size-4" /></Link>
            </Button>
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Lengkapi Data Tahap Kedua</h2>
                <p class="text-sm text-muted-foreground">{{ sample.tgl_test }} · Urutan {{ sample.urutan }} · {{ sample.kode_sample }} · {{ sample.no_open }}</p>
            </div>
        </div>
        <Card class="max-w-5xl border-none shadow-lg">
            <CardHeader>
                <CardTitle>Hasil Pengujian</CardTitle>
                <p class="text-sm text-muted-foreground">Spec, waktu proses, dan suhu air otomatis mengambil data terakhir dan tetap dapat diubah.</p>
            </CardHeader>
            <CardContent>
                <form class="space-y-7" @submit.prevent="form.put(route('density-water-sample.complete.store', sample.id))">
                    <div class="grid gap-5 md:grid-cols-2">
                        <div class="grid gap-2">
                            <Label for="perlakuan">Perlakuan</Label>
                            <Input id="perlakuan" v-model="form.perlakuan" />
                            <p v-if="form.errors.perlakuan" class="text-sm text-destructive">{{ form.errors.perlakuan }}</p>
                        </div>
                    </div>

                    <div v-for="group in measurementGroups" :key="group.title" class="space-y-3">
                        <h3 class="border-b pb-2 font-semibold">{{ group.title }}</h3>
                        <div class="grid gap-5 md:grid-cols-3">
                            <div v-for="field in group.fields" :key="field" class="grid gap-2">
                                <Label :for="field">{{ labels[field] }} (gr)</Label>
                                <Input :id="field" v-model="form[field]" type="number" min="0" step="0.001" />
                                <p v-if="form.errors[field]" class="text-sm text-destructive">{{ form.errors[field] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid gap-5 border-t pt-6 md:grid-cols-2">
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
                    </div>
                    <Button type="submit" :disabled="form.processing" class="w-full">
                        <IconLoader2 v-if="form.processing" class="mr-2 size-4 animate-spin" />
                        <IconDeviceFloppy v-else class="mr-2 size-4" /> Simpan dan Tandai Selesai
                    </Button>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
